<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Str;
use Symfony\Component\DomCrawler\Crawler;

class ImageService
{
	public function __construct()
	{

	}

	public function convertImageToVideo($image): string
	{
		$image = Storage::disk('public')->path($image);
		//generate random video name

		$videoPath = storage_path('app/public/temp/'.time().Str::random().'.mp4');
		exec("ffmpeg -y -loop 1 -i $image  -c:v libx264 -t 15 -pix_fmt yuv420p -vf scale=320:240 $videoPath");
		return $videoPath;
	}

	public function getImagesFromScraping($search): array
	{
		$response = Http::get("https://www.bing.com/images/search?q=".urlencode($search));

		$htmlContent = $response->body();

		// Step 2: Load HTML into the Symfony DomCrawler
		$crawler = new Crawler($htmlContent);

		// Step 3: Extract image URLs

		$imageUrls = $crawler->filter('a.iusc')->each(function (Crawler $node, $i) {
			$metadata = json_decode($node->attr('m'), true);
			return $metadata['murl'] ?? null; // Full-size image URL
		});

		return array_filter($imageUrls, function ($url) {
			return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
		});
	}
}
