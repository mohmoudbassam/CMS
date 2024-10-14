<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ChoiceVideo implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new job instance.
	 */
	public function __construct(public Short $short)
	{

	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		$path = (new VideoService())->getRandomVideo(Storage::disk('public')->path('videos'));

		$this->short->video_without_sound_path = Storage::disk('public')->path('/videos/'.$path);
		$this->short->save();
	}
}
