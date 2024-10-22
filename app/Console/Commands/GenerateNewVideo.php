<?php

namespace App\Console\Commands;

use App\Jobs\CleanUp;
use App\Jobs\CreateVideo;
use App\Models\Interest;
use App\Models\Short;
use App\Services\SuggestionService;
use File;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;

class GenerateNewVideo extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'new-video';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new video';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$type = select(
			label: 'What is the type of the video?',
			options: ['Story', 'Motivation'],
			default: 'Story',
			required: true
		);
		$country = select(
			label: 'Which account you want to use?',
			options: ['Canada', 'Sweden', 'Germany'],
			default: 'Canada',
			required: true
		);
		$refresh_token = match ($country) {
			'Canada' => env('YOUTUBE_REFRESH_TOKEN_CA'),
			'Sweden' => env('YOUTUBE_REFRESH_TOKEN_SW'),
			'Germany' => env('YOUTUBE_REFRESH_TOKEN_GE'),
		};

		$this->ensureDirectoriesCreated();

		$short = $this->createNewVideoScript($country, $type);

		if ($short) {
			CreateVideo::dispatch($short, $refresh_token);

			CleanUp::dispatch();
		}
	}

	public function ensureDirectoriesCreated(): void
	{
		File::ensureDirectoryExists(storage_path('app/public/audio'));
		File::ensureDirectoryExists(storage_path('app/public/videos'));
		File::ensureDirectoryExists(storage_path('app/public/subtitles'));
		File::ensureDirectoryExists(storage_path('app/public/final'));
		File::ensureDirectoryExists(storage_path('app/public/temp'));
	}

	private function createNewVideoScript($country, $type)
	{
		$lang = match ($country) {
			'Canada' => 'english',
			'Sweden' => 'swedish',
			'Germany' => 'german',
		};
		if ($type == 'Motivation') {
			$requirements = 'Can you give me a creative motivational speech for 10 minutes that can be inspiring and helpful? Please make sure to add some quotes and examples from inspiring.';
		} else {
			$suggestion = Interest::query()->inRandomOrder()->first();
			$requirements = 'Can you give me a creative story for 10 minutes that can be helpful for '.$suggestion->name . '?';
		}
		$requirements .= 'Provide also a good title and description for the video to use them as youtube video title and description. Also provide tags for the youtube video. Make sure to add relative hashtags to the title and description. 
		I want the response to be with this format: {"title": video_title, "description": video_description, "tags": video_tags, "content": content_text}
		Please provide everything in '.$lang.' language.';

		$messages = [
			['role' => 'user', 'content' => $requirements],
		];
		$ai_response = json_decode((new SuggestionService())->generateSuggestion($messages), true);

		if (empty($ai_response['title']) || empty($ai_response['content']) || empty($ai_response['description']) || empty($ai_response['tags'])) {
			return null;
		}
		dump([
			'title' => $ai_response['title'],
			'description' => $ai_response['description'],
			'tags' => is_array($ai_response['tags']) ? implode(',', $ai_response['tags']) : $ai_response['tags'],
		]);
		return Short::query()->create([
			'title' => $ai_response['title'],
			'description' => $ai_response['description'],
			'tags' => is_array($ai_response['tags']) ? implode(',', $ai_response['tags']) : $ai_response['tags'],
			'script' => $ai_response['content'],
			'content' => $ai_response['content'],
			'interest_id' => $suggestion->id,
		]);
	}
}
