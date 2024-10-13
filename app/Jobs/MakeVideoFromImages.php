<?php

namespace App\Jobs;

use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MakeVideoFromImages implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new job instance.
	 */
	public function __construct(public $short)
	{
		//
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		$images = $this->short->media()->where('type', 'image')->get();
		$images = $images->pluck('path')->toArray();
		$video_path = (new VideoService())->generateVideoFromImages($images, $this->short->video_image_duration);

		$this->short->update(['video_without_sound_path' => $video_path]);

	}
}
