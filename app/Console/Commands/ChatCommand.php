<?php

namespace App\Console\Commands;

use App\AI\Assistant;
use Illuminate\Console\Command;
use function Laravel\Prompts\{info, outro, spin, text};


class ChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a chat with OpenAI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $question = text(
            label: 'What is your question for AI',
            required: true
        );

        $chat = new Assistant();

        $response = spin(fn() => $chat->send($question), 'Sending request...');

        info($response);

        while ($question = text('Do you what to respond?')) {
            $response = spin(fn() => $chat->send($question), 'Sending request...');

            info($response);
        }

        outro('Assistant closed');
    }
}
