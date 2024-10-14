<?php

namespace App\Jobs;

use App\Models\Short;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class CreateVideo implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public function __construct(public Short $short, public null|string $refresh_token = null)
	{
	}

	public function handle(): void
	{
		Bus::chain([
			new ConvertScriptToSound($this->short),
			new GenerateSubtitle($this->short),
			new CalculateAudioDuration($this->short),
			new ChoiceVideo($this->short),
			new CalculateVideoDuration($this->short),
			new CutVideo($this->short),
			new MakeVideoSilent($this->short),
			new MergeVideoWithAudio($this->short),
			new MergeVideoWithSubtitle($this->short),
			new UploadVideoToYoutube($this->short, $this->refresh_token),
			new CleanUp(),
		])->dispatch();
	}
}
