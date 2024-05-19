<?php

namespace App\AI;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Assistants\AssistantResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;

class OpenAIClient implements AIClient
{
    public function retrieveAssistant(string $assistantId): AssistantResponse
    {
        return OpenAI::assistants()->retrieve($assistantId);
    }

    public function createAssistant(array $config)
    {
        return OpenAI::assistants()->create($config);
    }

    public function uploadFile(string $file, AssistantResponse $assistant)
    {
        $file = OpenAI::files()->upload([
            'purpose' => 'assistants',
            'file' => fopen($file, 'rb')
        ]);

        return OpenAI::assistants()
            ->files()
            ->create($assistant->id, ['file_id' => $file->id]);
    }

    public function createThread(array $parameters = [])
    {
        return OpenAI::threads()->create($parameters);
    }

    public function createMessages(string $message, string $threadId)
    {
        return OpenAI::threads()->messages()->create($threadId, [
            'role' => 'user',
            'content' => $message,
        ]);
    }

    public function messages(string $threadId): ThreadMessageListResponse
    {
        return OpenAI::threads()->messages()->list($threadId);
    }

    public function run(string $threadId, AssistantResponse $assistant): ThreadMessageListResponse
    {
        $run = OpenAI::threads()->runs()->create($threadId, [
            'assistant_id' => $assistant->id,
        ]);

        while ($this->runStatus($run)) {
            sleep(1);
        }

        return $this->messages($threadId);
    }

    public function runStatus(ThreadRunResponse $run): bool
    {
        $run = OpenAI::threads()->runs()->retrieve(
            threadId: $run->threadId,
            runId: $run->id
        );

        return $run->status !== 'completed';
    }
}
