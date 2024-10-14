<?php

namespace App\Jobs;

use App\Models\Short;
use App\Services\VideoService;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanUp implements ShouldQueue
{
	use Queueable;

	public function handle(): void
	{
		collect(File::allFiles(storage_path('app/public/audio')))
			->each(function ($file) {
				File::delete($file->getPathname());
			});
		collect(File::allFiles(storage_path('app/public/subtitles')))
			->each(function ($file) {
				File::delete($file->getPathname());
			});
		collect(File::allFiles(storage_path('app/public/temp')))
			->each(function ($file) {
				File::delete($file->getPathname());
			});
		collect(File::allFiles(storage_path('app/public/videos')))
			->each(function ($file) {
				File::delete($file->getPathname());
			});
		collect(File::allFiles(storage_path('app/public/final')))
			->each(function ($file) {
				File::delete($file->getPathname());
			});
		Short::whereNotNull('published_at')
			->update(['video_without_sound_path' => null, 'subtitle_path' => null, 'audio_path' => null, 'audio_duration' => null]);
	}
}
