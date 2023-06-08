<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOME</title>
</head>
<body class="antialiased">
<div>
    <h1>HOME</h1>
    <!-- submit-form.blade.php -->
    <div>
        <form action="/home" method="POST">
            @csrf
            <label>
                <textarea type="text" name="prompt" placeholder="内容">
                </textarea>
            </label>
            <!-- フォームの入力フィールドやボタンなど -->
            <button type="submit">送信</button>
        </form>
    </div>
    <div>
        @if (!empty($episodeList))
            @foreach ($episodeList as $episode)
                <h2>{{ $episode['title'] }}</h2>
                <p>{{ $episode['summary'] }}</p>
            @endforeach
        @endif
    </div>

</div>
</body>
</html>
