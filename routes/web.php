<?php

use App\AI\Assistant;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/assistant', function () {
    $file = OpenAI::files()->upload([
        'purpose' => 'assistants',
        'file' => fopen(storage_path('docs/parsing.md'), 'rb')
    ]);

    $assistant = OpenAI::assistants()->create([
        'model' => 'gpt-4-turbo-preview',
        'name' => 'Larvify Tutor',
        'instructions' => 'You are a helpful programming teacher.',
        'tools' => [
            ['type' => 'retrieval']
        ],
        'file_ids' => [
            $file->id
        ],
    ]);

    $run = OpenAI::threads()->createAndRun([
        'assistant_id' => $assistant->id,
        'thread' => [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'How do I grab the first paragraph?',
                ]
            ]
        ]
    ]);

    do {
        sleep(1);

        $run = OpenAI::threads()->runs()->retrieve(
            threadId: $run->threadId,
            runId: $run->id
        );
    } while ($run->status !== 'completed');

    $messages = OpenAI::threads()->messages()->list($run->threadId);

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

    $mp3 = (new Assistant())->send(message: $prompt, speech: true);

    $file = Storage::disk('local')->put('/roasts/'.md5($mp3).".mp3", $mp3);

    return redirect('/')->with([
        'file' => $file,
        'flash' => 'Boom',
    ]);
});
