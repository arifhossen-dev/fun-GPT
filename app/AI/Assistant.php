<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;

class Assistant
{
    protected array $messages = [];

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function systemMessage(string $message): static
    {
        $this->addMessage($message, 'system');

        return $this;
    }

    public function addMessage(string $message, string $role = 'user')
    {
        $this->messages[] = [
            'role' => $role,
            'content' => $message
        ];

        return $this;
    }

    public function reply(string $message): ?string
    {
        return $this->send($message);
    }

    public function send(string $message, bool $speech): ?string
    {
        $this->addMessage($message, 'user');

        $response = OpenAI::chat()->create([
            "model" => "gpt-3.5-turbo-0125",
            "messages" => $this->messages
        ])->choices[0]->message->content;

        if ($response) {
            $this->addMessage($response, 'assistant');
        }

        return $speech ? $this->speech($response) : $response;
    }

    public function speech(string $message): string
    {
        return OpenAI::audio()->speech([
            "model" => "tts-1",
            "input" => $message,
            "voice" => "alloy"
        ]);
    }

    public function visualize(string $description, array $options = []): string
    {
        $this->addMessage($description);

        $description = collect($this->messages)
            ->where('role', 'user')
            ->pluck('content')->implode(' ');

        logger($description);

        $options = array_merge([
            "model" => "dall-e-3",
            "prompt" => $description,
        ], $options);

        $url = OpenAI::images()->create($options)->data[0]->url;

        $this->addMessage($url, 'assistant');

        return $url;
    }

    public function messages()
    {
        return $this->messages;
    }
}
