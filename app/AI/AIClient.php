<?php

namespace App\AI;

use OpenAI\Responses\Assistants\AssistantResponse;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;

interface AIClient
{
    public function retrieveAssistant(string $assistantId): AssistantResponse;

    public function createAssistant(array $config);

    public function uploadFile(string $file, AssistantResponse $assistant);

    public function createThread(array $parameters = []);

    public function createMessages(string $message, string $threadId);

    public function run(string $threadId, AssistantResponse $assistant): ThreadMessageListResponse;

    public function runStatus(ThreadRunResponse $run): bool;

    public function messages(string $threadId): ThreadMessageListResponse;
}
