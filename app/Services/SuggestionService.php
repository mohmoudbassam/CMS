<?php

namespace App\Services;

use OpenAI;

class SuggestionService
{
	public $client;
	public string $yourApiKey;

	public function __construct()
	{
		$this->yourApiKey = getenv('OPENAI_API_KEY');
		$this->client = OpenAI::client($this->yourApiKey);
	}

	public function generateSuggestion(array $messages): string
	{
		$result = $this->client->chat()->create([
			'model' => 'gpt-4o-mini',
			'messages' => $messages,
		]);
		return $result->choices[0]->message->content;
	}

	function extractCodeBlocks($content) {
		preg_match_all('/```(.*?)```/s', $content, $matches);
		return $matches[1]; // Return an array of code block contents
	}
}
