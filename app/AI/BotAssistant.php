<?php

namespace App\AI;

use OpenAI\Responses\Assistants\AssistantResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;

class BotAssistant
{
    protected AssistantResponse $assistant;
    protected string $threadId;
    protected OpenAIClient $client;

    public function __construct(string $assistantId, ?AIClient $client = null)
    {
        $this->client = $client ?? new OpenAIClient();

        $this->assistant = $this->client->retrieveAssistant($assistantId);
    }

    public static function create(array $config = []): static
    {
        $defaultConfig = [
            'model' => 'gpt-4-turbo-preview',
            'name' => 'Larvify Tutor',
            'instructions' => 'You are a helpful programming teacher.',
            'tools' => [
                ['type' => 'retrieval']
            ],
        ];

        $assistant = (new OpenAIClient())->createAssistant(array_merge_recursive($defaultConfig), $config);

        return new static($assistant->id);
    }

    public function educate(string $file): static
    {
        $this->client->uploadFile($file, $this->assistant);

        return $this;
    }

    public function createThread(array $parameters = []): static
    {
        $thread = $this->client->createThread($parameters);

        $this->threadId = $thread->id;

        return $this;
    }

    public function send(): ThreadMessageListResponse
    {
        return $this->client->run($this->threadId, $this->assistant);
    }

    public function messages(): ThreadMessageListResponse
    {
        return $this->client->messages($this->threadId);
    }

    public function write(string $message): static
    {
        $this->client->createMessages($message, $this->threadId);

        return $this;
    }
}
