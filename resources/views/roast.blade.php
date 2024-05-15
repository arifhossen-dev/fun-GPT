<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/css/app.css')

</head>
<body class="h-full grid place-items-center p-6">
@if (session('file'))
    <div>
        <iframe src="https://giphy.com/embed/RdKjAkFTNZkWUGyRXF" width="480" height="256" frameBorder="0"
                class="giphy-embed" allowFullScreen></iframe>

        <a href="{{ asset(session('file')) }}" download
           class="block w-full text-center rounded p-2 bg-gray-200 hover:bg-blue-500 hover:text-white mt-3">Download
            Audio</a>
    </div>
@else
    <form action="/roast" method="POST" class="w-full lg:max-w-md lg:mx-auto">
        @csrf

        <div class="flex gap-2">
            <input type="text" name="topic" placeholder="What do you want us to roast?" required
                   class="border p-2 rounded flex-1" minlength="2" maxlength="50">

            <button type="submit" class="rounded p-2 bg-gray-200 hover:bg-blue-500 hover:text-white">Roast</button>
        </div>
    </form>
@endif
{{--<div class="border bg-gray-500/5 h-svh mx-auto w-full rounded-3xl">--}}
{{--    <div class="flex h-full">--}}
{{--        <div class="h-full bg-amber-50">--}}
{{--            some--}}
{{--        </div>--}}
{{--        <div class="justify-end flex-1 h-full bg-amber-100">--}}
{{--            <form action="/rost" method="post">--}}
{{--                <div class="flex gap-3">--}}
{{--                    <input type="text" class="w-full" placeholder="Make you rost" name="topic">--}}
{{--                    <div>--}}
{{--                        <button class="bg-green-300 rounded px-3 py-2 hover:bg-green-500" type="submit">Rost</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
</body>
</html>
