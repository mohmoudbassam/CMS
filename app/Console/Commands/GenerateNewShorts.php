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

class GenerateNewShorts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'new-shorts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new shorts';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
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

		$shorts = $this->createNewShortsScripts($country);

		foreach ($shorts as $short) {
			CreateVideo::dispatch($short, $refresh_token);
		}

		CleanUp::dispatch();
	}

	public function ensureDirectoriesCreated(): void
	{
		File::ensureDirectoryExists(storage_path('app/public/audio'));
		File::ensureDirectoryExists(storage_path('app/public/videos'));
		File::ensureDirectoryExists(storage_path('app/public/subtitles'));
		File::ensureDirectoryExists(storage_path('app/public/final'));
		File::ensureDirectoryExists(storage_path('app/public/temp'));
	}

	private function createNewShortsScripts($country)
	{
		$lang = match ($country) {
			'Canada' => 'english',
			'Sweden' => 'swedish',
			'Germany' => 'german',
		};
		$requirements = 'Can you give me 50 random fun and interesting facts with its title and description? each fact should be around 10-15 seconds. Provide the content as someone is reading the fact in a youtube short video. don\'t repeat the facts.
		Keep the facts engaging, polarizing, over the top and relatable to everyone or people in particular situations. and don\'t forget to make them fun and interesting. also don\'t forget to make them short and to the point.
		Provide also a good title and description for each short to use them as youtube short title and description. Also provide tags for the youtube short. Make sure to add relative hashtags to the title and description. 
		I want the response to be with this format: [{"title": short_title, "description": short_description, "tags": [short_tags], "content": short_content}, ...] only without adding any opening or closing text.
		Please provide everything in '.$lang.' language.';

		$messages = [
			['role' => 'user', 'content' => $requirements],
		];
		$ai_response = json_decode($raw_response = (new SuggestionService())->generateSuggestion($messages), true);
//		dd($raw_response, $ai_response);

		$shorts = [];
		foreach ($ai_response as $response) {
			if (empty($response['title']) || empty($response['content']) || empty($response['description']) || empty($response['tags'])) {
				continue;
			}
			$shorts[] = Short::query()->create([
				'title' => $response['title'],
				'description' => $response['description'],
				'tags' => is_array($response['tags']) ? implode(',', $response['tags']) : $response['tags'],
				'script' => $response['content'],
				'content' => $response['content'],
			]);
		}
		return $shorts;
	}
}
