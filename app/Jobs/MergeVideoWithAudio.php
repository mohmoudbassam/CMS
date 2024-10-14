<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MergeVideoWithAudio implements ShouldQueue
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
		$video_path = (new VideoService())->mergeVideoWithAudio($this->short->video_without_sound_path, $this->short->audio_path);
		$this->short->update(['video_path' => $video_path]);
	}
}
