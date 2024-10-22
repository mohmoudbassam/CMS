<?php

namespace App\Jobs;

use App\Models\Media;
use App\Models\Short;
use App\Services\ImageService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Str;

class GenerateImages implements ShouldQueue
{
	use Queueable;

	public function __construct(public Short $short, public $search)
	{

	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{

		$imageUrls = (new ImageService())->getImagesFromScraping($this->search);

		//take the first 5 images
		$imageUrls = array_slice($imageUrls, 0, 7);
		foreach ($imageUrls as $url) {
			// Get the image content
			try {
				$imageContent = file_get_contents($url);
			} catch (Exception $exception) {
				continue;
			}

			// Extract image file name
			$imageName = time().Str::random().'.png';
			// Save image to storage/app/public directory
			Storage::disk('public')->put("images/{$imageName}", $imageContent);

			Media::query()->create([
				'short_id' => $this->short->id,
				'path' => "images/{$imageName}",
				'type' => 'image'
			]);
		}
	}
}
