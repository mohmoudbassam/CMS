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
		$path = (new VideoService())->cutVideo($this->short->video_without_sound_path, 0, $this->short->audio_duration);
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
