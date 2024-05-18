<?php

use App\AI\Assistant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('replies', function () {
    return view('replay');
});

Route::post('replies', function () {
    $attributes = request()->validate([
        'body' => ['required', 'string']
    ]);

    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo-1106',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a forum moderator who always response JSON.'],
            [
                'role' => 'system',
                'content' => <<<EOT
                    Pleas inspect the following text determine if it is spam.

                    {$attributes['body']}

                    Expected Response Example:

                    {"is_spam":true|false}
                EOT
            ]
        ],
        'response_format' => ['type' => 'json_object'],
    ])->choices[0]->message->content;

    $response = json_decode($response);

    if ($response->is_spam) {
        throw ValidationException::withMessages([
            'body' => 'Spam was detected.'
        ]);
    }

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
