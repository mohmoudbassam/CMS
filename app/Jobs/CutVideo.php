<?php

namespace App\Jobs;

use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CutVideo implements ShouldQueue
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
       $path= (new VideoService())->CutVideo($this->short->video_without_sound_path);
         $this->short->video_without_sound_path = $path;
         $this->short->save();
    }
}
