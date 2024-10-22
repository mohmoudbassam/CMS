<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Str;

class OpenAiImageService
{
	protected $client;

	public function __construct()
	{
		$this->client = new Client();
	}

	public function generateImage($prompt)
	{
		// dd($prompt);
		$apiKey = getenv('OPENAI_API_KEY');
		$response = $this->client->post('https://api.openai.com/v1/images/generations', [
			'headers' => [
				'Authorization' => "Bearer {$apiKey}",
				'Content-Type' => 'application/json',
			],
			'json' => [
				'model' => 'dall-e-3',
				'prompt' => $prompt,
				'n' => 1,
				'size' => '1024x1024',
				'response_format' => 'b64_json',
			]
		]);

		$imageBase64 = json_decode($response->getBody()->getContents(), true)['data'][0]['b64_json'];
		$imageData = base64_decode($imageBase64);

		// Define the image name and path (you can modify the name based on your logic)
		$imageName = 'images/generated_image_'.time().Str::random().'.png';

		Storage::disk('public')->put($imageName, $imageData);

		return $imageName;
	}

	public function editImage($prompt)
	{
		$apiKey = getenv('OPENAI_API_KEY');
		$response = $this->client->post('https://api.openai.com/v1/images/edits', [
			'headers' => [
				'Authorization' => "Bearer {$apiKey}",
			],
			'multipart' => [
				[
					'name' => 'model',
					'contents' => 'dall-e-2'
				],
				[
					'name' => 'image',
					'contents' => fopen(Storage::disk('public')->path('images/sunlit_lounge_rgba.png'), 'r'),
					'filename' => 'sunlit_lounge.png'
				],
				[
					'name' => 'prompt',
					'contents' => 'delete all text on the image return the image without any text clear the image from any text'
				],
				[
					'name' => 'n',
					'contents' => 1
				],
				[
					'name' => 'size',
					'contents' => '1024x1024'
				],
				[
					'name' => 'response_format',
					'contents' => 'b64_json'
				]
			]
		]);


		$imageBase64 = json_decode($response->getBody()->getContents(), true)['data'][0]['b64_json'];
		$imageData = base64_decode($imageBase64);
		$imageName = 'images/generated_image_'.time().Str::random().'.png';

		$path = Storage::disk('public')->put($imageName, $imageData);

		dd($path);
	}

	private function convertToRGBA($inputPath, $outputPath)
	{
		// Load the image
		$image = imagecreatefrompng($inputPath);

		// Check if the image is loaded successfully
		if (!$image) {
			throw new Exception("Failed to load image at {$inputPath}");
		}

		// Create a new true color image with alpha
		$rgbaImage = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagesavealpha($rgbaImage, true);
		$transparency = imagecolorallocatealpha($rgbaImage, 255, 255, 255, 127);
		imagefill($rgbaImage, 0, 0, $transparency);

		// Copy the original image to the RGBA image
		imagecopyresampled($rgbaImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image), imagesx($image), imagesy($image));

		// Save the new image
		imagepng($rgbaImage, $outputPath);

		// Free memory
		imagedestroy($image);
		imagedestroy($rgbaImage);
	}
}
