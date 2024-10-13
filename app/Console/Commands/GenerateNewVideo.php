<?php

namespace App\Console\Commands;

use App\Jobs\CalculateAudioDuration;
use App\Jobs\ChoiceVideo;
use App\Jobs\CleanUp;
use App\Jobs\ConvertScriptToSound;
use App\Jobs\CutVideo;
use App\Jobs\GenerateSubtitle;
use App\Jobs\MakeVideoSilent;
use App\Jobs\MergeVideoWithAudio;
use App\Jobs\MergeVideoWithSubtitle;
use App\Jobs\UploadVideoToYoutube;
use App\Models\Interest;
use App\Models\Short;
use App\Services\SuggestionService;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class GenerateNewVideo extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:new-video';

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
		$this->ensureDirectoriesCreated();

		$short = $this->createNewVideoScript();

		if ($short) {
			Bus::chain([
				new ConvertScriptToSound($short),
				new GenerateSubtitle($short),
				new CalculateAudioDuration($short),
				new ChoiceVideo($short),
				new CutVideo($short),
				new MakeVideoSilent($short),
				new MergeVideoWithAudio($short),
				new MergeVideoWithSubtitle($short),
				new UploadVideoToYoutube($short),
				new CleanUp(),
			])->dispatch();
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

	private function createNewVideoScript()
	{
		$suggestion = Interest::query()->inRandomOrder()->first();
		$textToAi = 'Can you give me a creative story for 10 minutes that can be helpful for '.$suggestion->name . '? Provide also a good title and description for the story to use them as youtube video title and description. Also provide tags for the youtube video. Make sure to add relative hashtags to the title and description. 
		I want the response to be with this format: {"title": story_title, "description": video_description, "tags": video_tags, "story": story_text}';

		$messages = [
			['role' => 'user', 'content' => $textToAi],
		];
		$text = (new SuggestionService())->generateSuggestion($messages);
		$story = json_decode($text, true);

		if (empty($story['title']) || empty($story['story']) || empty($story['description']) || empty($story['tags'])) {
			return null;
		}
		return Short::query()->create([
			'title' => $story['title'],
			'description' => $story['description'],
			'tags' => implode(',', $story['tags']),
			'script' => $story['story'],
			'content' => $story['story'],
			'interest_id' => $suggestion->id,
		]);
	}
}
