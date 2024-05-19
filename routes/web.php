<?php

use App\AI\Assistant;
use App\AI\BotAssistant;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/assistant', function () {
    $assistant = new BotAssistant(config('openai.assistant.id'));

    $messages = $assistant->createThread()
        ->write('Hello!')
        ->write('How do I grab the first paragraph using Laraparse?')
        ->send();

    dd($messages);
});

Route::get('replies', function () {
    return view('replay');
});

Route::post('replies', function () {
    $attributes = request()->validate([
        'body' => [
            'required',
            'string',
            new SpamFree()
        ]
    ]);

    return 'Redirect wherever is needed. Post was valid.';
});

Route::get('/image', function () {
    return view('image', [
        'messages' => session('messages', []),
    ]);
});

Route::post('/image', function () {
    $attributes = request()->validate([
        'description' => ['required', 'string', 'min:3']
    ]);

    $assistant = new Assistant(session('messages', []));

    $assistant->visualize($attributes['description']);

    session(['messages' => $assistant->messages()]);

    return redirect('/image');
});

Route::get('/', function () {
    return view('roast');
});

Route::post('/roast', function () {
    $attribute = request()->validate([
        'topic' => ['required', 'string', 'min:2', 'max:50']
    ]);

    $prompt = "Please roast {$attribute['topic']} in a sarcastic tone.";

    $mp3 = (new Assistant())->send(message: $prompt);

    $file = Storage::disk('local')->put('/roasts/'.md5($mp3).".mp3", $mp3);

    return redirect('/')->with([
        'file' => $file,
        'flash' => 'Boom',
    ]);
});
