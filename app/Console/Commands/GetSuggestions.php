<?php

namespace App\Console\Commands;

use App\Jobs\CalculateAudioDuration;
use App\Jobs\CalculateAudioDurationForImages;
use App\Jobs\ChoiceVideo;
use App\Jobs\ConvertScriptToSound;
use App\Jobs\CutVideo;
use App\Jobs\GenerateAiImage;
use App\Jobs\GenerateImages;
use App\Jobs\MakeVideoFromImage;
use App\Jobs\MakeVideoFromImages;
use App\Jobs\MakeVideoSilent;
use App\Jobs\MergeVideoWithAudio;
use App\Models\Interest;
use App\Models\Short;
use App\Services\SuggestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class GetSuggestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-suggestions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $suggestion = Interest::query()->inRandomOrder()->first();
        $textToAi = 'could you give me 10 advice on give it without any Introductions i want your message start from  first advice
        to last advice without Introductions and conclusion and greeting
        ' . $suggestion->name;

        $messages = [
            ['role' => 'user', 'content' => $textToAi],
        ];
        $text = (new SuggestionService())->generateSuggestion($messages);

        $pattern = '/\d+\.\s+/';
        $splitText = preg_split($pattern, trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $array = array_map('trim', $splitText);
        //remover /n
        $content = array_map(function ($item) {
            return str_replace("\n", '', $item);
        }, $array);
        $short = Short::query()->create([
            'script' => $text,
            'content' => $content,
            'interest_id' => $suggestion->id,
        ]);
        $messages = [
            ['role' => 'system', 'content' => $text],
            ['role' => 'user', 'content' => 'could you give me five search tag to search on google about this topics give it without any Introductions i want your message start from  first tag
                                              to last tag without Introductions and conclusion and greeting '],
        ];
        $text = (new SuggestionService())->generateSuggestion($messages);

        $array = explode("\n", $text);
        $searches = array_map('trim', $array);


        Bus::chain([
                // GenerateImages::dispatch($short, $searches[0] . ' , '. $searches[1]),
                ConvertScriptToSound::dispatch($short),
                CalculateAudioDuration::dispatch($short),
                ChoiceVideo::dispatch($short),
                CutVideo::dispatch($short),
                MakeVideoSilent::dispatch($short),

                //  CalculateAudioDurationForImages::dispatch($short),
                //  MakeVideoFromImages::dispatch($short),
                MergeVideoWithAudio::dispatch($short),
            ]
        );

    }
}
