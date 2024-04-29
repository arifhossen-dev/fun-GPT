<?php

use App\AI\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    $chat = new Chat();

    $poem = $chat
        ->systemMessage('You are a poetic assistant, skilled in explaining complex programming concepts with creative flair.')
        ->send('Compose a poem that explains the concept of recursion in programming.');

    $newPoem = $chat->reply('Good, but can you make it much, much more silly.');

    return view('welcome', ['poem' => $newPoem]);
});
