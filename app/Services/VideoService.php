<?php

namespace App\Services;


use FFMpeg\FFProbe;
use Illuminate\Support\Facades\File;
use Str;

class VideoService
{
	public function duplicateVideo($video): string
	{
		$output_path = storage_path('app/public/temp/'.time().Str::random().'.mp4');
		$cmd = "ffmpeg -stream_loop 1 -i $video -c copy $output_path";
		exec($cmd);
		return $output_path;
	}

	public function calculateVideoDuration($path): int
	{
		$ffprobe = FFProbe::create([
			'ffmpeg.binaries' => '/opt/homebrew/bin/ffmpeg',
			'ffprobe.binaries' => '/opt/homebrew/bin/ffprobe',
			'timeout' => 3600, // The timeout for the underlying process
			'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
		]);
		return ceil((int)$ffprobe->format($path)->get('duration'));
	}

	public function mergeVideoWithAudio($video_path, $audio_path): string
	{
		$output_path = storage_path('app/public/temp/'.time().Str::random().'.mp4');
		$cmd = "ffmpeg -y -i $video_path -i $audio_path -c:v copy -map 0:v -map 1:a -y $output_path";
		exec($cmd);
		return $output_path;
	}

	public function mergeVideoWithSubtitle($video_path, $subtitle_path): string
	{
		$output_path = storage_path('app/public/final/'.time().Str::random().'.mp4');
		$cmd = "ffmpeg -i $video_path -vf subtitles=$subtitle_path $output_path";
		exec($cmd);
		return $output_path;
	}

	public function makeVideoSilent($video_path): string
	{
		$output_path = storage_path('app/public/temp/videos'.time().Str::random().'.mp4');
		$cmd = "ffmpeg -i $video_path -c copy -an $output_path";
		exec($cmd);
		return $output_path;
	}

	public function cutVideo($video_path, $start_time = 0, $end_time = 60): string
	{
		$start_time = gmdate('H:i:s', $start_time ?: 0);
		$end_time = gmdate('H:i:s', $end_time ?: 60);
		$output_path = storage_path('app/public/temp/videos'.time().Str::random().'.mp4');
		$cmd = "ffmpeg -i $video_path -ss $start_time -to $end_time -async 1 $output_path";
		exec($cmd);
		return $output_path;
	}

	public function getRandomVideo($folder_path): string
	{
		$files = File::files($folder_path);
		$random_video = $files[array_rand($files)];
		return $random_video->getFilename();
	}
}
