<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use OpenAI;

class SuggestionService
{
    public $client;
    public string $yourApiKey;


    public function generateSuggestion(array $messages): string
    {
        $result = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
        ]);
        return $result->choices[0]->message->content;
    }

    public function __construct()
    {
        $this->yourApiKey = getenv('OPEN_AI_API_KEY');
        $this->client = OpenAI::client($this->yourApiKey);
    }

}
