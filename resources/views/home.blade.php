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
        <form action="/home" method="POST">
        @csrf
        <!-- フォームの入力フィールドやボタンなど -->
            <button type="submit">送信</button>
        </form>

    </div>
    </body>
</html>
