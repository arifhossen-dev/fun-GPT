<?php

use App\AI\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('roast');
});

Route::post('/roast',function (){
    $attribute = request()->validate([
        'topic' => ['required','string','min:2','max:50']
    ]);

    $prompt ="Please roast {$attribute['topic']} in a sarcastic tone.";

    $mp3 = (new Chat())->send(message:$prompt,speech:true);

    $file = Storage::disk('local')->put('/roasts/'.md5($mp3).".mp3", $mp3);

    return redirect('/')->with([
        'file' => $file,
        'flash' => 'Boom',
    ]);
});
