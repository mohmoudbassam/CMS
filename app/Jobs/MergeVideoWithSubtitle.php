<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MergeVideoWithSubtitle implements ShouldQueue
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
		$video_path = (new VideoService())->mergeVideoWithSubtitle($this->short->video_path, $this->short->subtitle_path);
		$this->short->update(['video_path' => $video_path]);
	}
}
