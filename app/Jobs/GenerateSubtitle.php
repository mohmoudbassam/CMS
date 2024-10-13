<?php

namespace App\Jobs;

use App\Services\AudioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSubtitle implements ShouldQueue
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
		$subtitle_path = (new AudioService())->generateSubtitle($this->short);
		$this->short->update(['subtitle_path' => $subtitle_path]);
	}
}
