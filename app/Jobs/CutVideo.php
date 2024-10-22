<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CutVideo implements ShouldQueue
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
		$this->checkVideoDuration();
		$start_time = rand(0, $this->short->video_duration - $this->short->audio_duration);
		$path = (new VideoService())->cutVideo($this->short->video_without_sound_path, $start_time, $start_time + $this->short->audio_duration);
		$this->short->video_without_sound_path = $path;
		$this->short->save();
	}

	public function checkVideoDuration(): void
	{
		if ($this->short->video_duration < $this->short->audio_duration) {
			$this->short->update(['video_without_sound_path' => (new VideoService())->duplicateVideo($this->short->video_without_sound_path)]);
			$this->checkVideoDuration();
		}
	}
}
