<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;

class Chat
{
    protected array $messages = [];

    public function systemMessage(string $message): static
    {
        $this->messages[] = [
            'role' => 'system',
            'content' => $message,
        ];

        return $this;
    }

    public function send(string $message,bool $speech): ?string
    {
        $this->messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        $response = OpenAI::chat()->create([
            "model" => "gpt-3.5-turbo-0125",
            "messages" => $this->messages
        ])->choices[0]->message->content;

        if ($response) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $response
            ];
        }

        return  $speech?$this->speech($response):$response;
    }

    public function speech(string $message):string
    {
        return OpenAI::audio()->speech([
            "model" => "tts-1",
            "input" => $message,
            "voice" => "alloy"
        ]);
    }

    public function reply(string $message): ?string
    {
        return $this->send($message);
    }

    public function messages()
    {
        return $this->messages;
    }
}
