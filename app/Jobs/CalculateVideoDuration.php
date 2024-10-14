<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\AudioService;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateVideoDuration implements ShouldQueue
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
		$duration = (new VideoService())->calculateVideoDuration($this->short->video_without_sound_path);
		$this->short->update(['video_duration' => $duration]);
	}
}
