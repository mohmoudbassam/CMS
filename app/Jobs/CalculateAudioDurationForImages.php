<?php

namespace App\Jobs;

use App\Models\Short;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateAudioDurationForImages implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new job instance.
	 */
	public function __construct(public Short $short)
	{
		//
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		$images_number = $this->short->media()->where('type', 'image')->count();
		$image_audio_duration = (ceil($this->short->audio_duration / $images_number));
		$this->short->update(['video_image_duration' => $image_audio_duration]);
	}
}
