<?php

namespace App\Jobs;

use App\Models\Short;
use Exception;
use Google_Client;
use Google_Http_MediaFileUpload;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadVideoToYoutube implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public function __construct(public Short $short, public null|string $refresh_token = null)
	{
	}

	public function handle(): void
	{
		$client = new Google_Client();
		$client->setClientId(env('GOOGLE_CLIENT_ID'));
		$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
		$client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
		$client->refreshToken($this->refresh_token ?? env('YOUTUBE_REFRESH_TOKEN_CA'));

		$youtube = new Google_Service_YouTube($client);

		$video = new Google_Service_YouTube_Video();
		$video->setSnippet(new Google_Service_YouTube_VideoSnippet());
		$video->getSnippet()->setTitle($this->short->title);
		$video->getSnippet()->setDescription($this->short->description);
		$video->getSnippet()->setTags(explode(',', $this->short->tags));

		$status = new Google_Service_YouTube_VideoStatus();
		$status->setPrivacyStatus('public');
		$video->setStatus($status);

		$videoPath = $this->short->video_path;

		try {
			$chunkSizeBytes = 1 * 1024 * 1024;
			$client->setDefer(true);

			$insertRequest = $youtube->videos->insert('status,snippet', $video);

			$media = new Google_Http_MediaFileUpload(
				$client,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
			);
			$media->setFileSize(filesize($videoPath));

			$status = false;
			$handle = fopen($videoPath, 'rb');
			while (!$status && !feof($handle)) {
				$chunk = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			fclose($handle);
			$client->setDefer(false);

			info('Video uploaded successfully!... video_id:'. $status['id']);

			$this->short->update(['published_at' => now(), 'youtube_video_id' => $status['id']]);

			sleep(5);
		} catch (Exception $e) {
			info($e->getMessage());
		}
	}
}
