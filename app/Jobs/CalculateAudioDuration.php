<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\AudioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateAudioDuration implements ShouldQueue
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
		$duration = (new AudioService())->calculateAudioDuration($this->short->audio_path);
		$this->short->update(['audio_duration' => $duration]);

	}
}
