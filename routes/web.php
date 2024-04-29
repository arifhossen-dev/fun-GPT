<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $messages = [
        [
            "role" => "system",
            "content" => "You are a poetic assistant, skilled in explaining complex programming concepts with creative flair."
        ],
        [
            "role" => "user",
            "content" => "Compose a poem that explains the concept of recursion in programming."
        ]
    ];

    $poem = Http::withToken(config('services.openai.secret'))
        ->post('https://api.openai.com/v1/chat/completions',
            [
                "model" => "gpt-3.5-turbo-0125",
                "messages" => $messages
            ])->json('choices.0.message.content');

    $messages[] = [
        'role' => 'assistant',
        'content' => $poem
    ];

    $messages[] = [
        'role' => 'user',
        'content' => 'Good, but can you make it much, much more silly.',
    ];

    $sillyPoem = Http::withToken(config('services.openai.secret'))
        ->post('https://api.openai.com/v1/chat/completions',
            [
                "model" => "gpt-3.5-turbo-0125",
                "messages" => $messages
            ])->json('choices.0.message.content');

    $messages[] = [
        'role' => 'assistant',
        'content' => $sillyPoem
    ];


    return view('welcome', ['poem' => $sillyPoem]);
});
