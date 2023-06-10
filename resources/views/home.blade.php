<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOME</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5F5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: #FFF;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type=text] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #CCC;
        }

        button {
            padding: 10px 20px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        button:hover {
            background-color: #007B9A;
        }

        .character {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .character input[type=text] {
            width: 45%; /* ある程度の間隔を保つために 50% 未満に設定 */
        }
    </style>
</head>
<body class="antialiased">
<div class="container">
    <h1>HOME</h1>
    <!-- submit-form.blade.php -->
    <div>
        <form action="/home" method="POST">
            @csrf
            <label for="theme">テーマ:</label>
            <input type="text" id="theme" name="theme" value="" required>

            <div class="character">
                <label for="position1">登場人物1</label>
                <input type="text" id="position1" name="positions[]" placeholder="ポジション"
                       value="{{ old('positions.0') }}" required>
                <input type="text" id="name1" name="names[]" placeholder="名前" value="{{ old('names.0') }}" required>
            </div>

            <div class="character">
                <label for="position2">登場人物2</label>
                <input type="text" id="position2" name="positions[]" placeholder="ポジション"
                       value="{{ old('positions.1') }}" required>
                <input type="text" id="name2" name="names[]" placeholder="名前" value="{{ old('names.1') }}" required>
            </div>
            <div class="character">
                <label for="position1">登場人物3</label>
                <input type="text" id="position3" name="positions[]" placeholder="ポジション"
                       value="{{ old('positions.3') }}" required>
                <input type="text" id="name3" name="names[]" placeholder="名前" value="{{ old('names.3') }}" required>
            </div>

            <div class="character">
                <label for="position2">登場人物4</label>
                <input type="text" id="position4" name="positions[]" placeholder="ポジション"
                       value="{{ old('positions.4') }}" required>
                <input type="text" id="name4" name="names[]" placeholder="名前" value="{{ old('names.4') }}" required>
            </div>

            <!-- 他の登場人物も同様に -->
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
