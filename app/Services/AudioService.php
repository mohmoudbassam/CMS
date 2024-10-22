<?php

namespace App\Services;

use App\Models\Short;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Str;

class AudioService
{
	public function convertTextToSound(string $script): string
	{
		$apiKey = getenv('OPENAI_API_KEY');
		$response = Http::withToken($apiKey)
			->timeout(3600)
			->post('https://api.openai.com/v1/audio/speech', [
			'model' => 'tts-1',
			'input' => $script,
			'voice' => 'alloy', // Select from available voices
		]);

		$audioName = 'generated_speech_'.time().Str::random().'.mp3';
		$audioPath = Storage::disk('public')->path('/audio/'.$audioName);

		file_put_contents($audioPath, $response->getBody());

		return $audioPath;
	}

	public function calculateAudioDuration($audioPath): int
	{
		$ffprobe = FFProbe::create([
			'ffmpeg.binaries' => '/opt/homebrew/bin/ffmpeg',
			'ffprobe.binaries' => '/opt/homebrew/bin/ffprobe',
			'timeout' => 3600, // The timeout for the underlying process
			'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
		]);
		return ceil((int)$ffprobe->format($audioPath)->get('duration'));
	}

	public function generateSubtitle(Short $short): string
	{
		exec('whisper '.$short->audio_path.' --model small --output_format srt --output_dir='.storage_path('app/public/subtitles'));
		return str($short->audio_path)->replace('.mp3', '.srt')->replace('/audio/', '/subtitles/');
	}
}
