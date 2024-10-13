<?php

namespace App\Jobs;

use App\Services\AudioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ConvertScriptToSound implements ShouldQueue
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

        $soundPath = (new AudioService())->convertTextToSound($this->short->script);
        $this->short->update(['audio_path' => $soundPath]);

    }
}
