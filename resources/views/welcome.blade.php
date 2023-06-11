<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIドラマメーカー</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #e0e0e0;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
        }
        .header {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 3px 3px 6px #bfbfbf, -3px -3px 6px #ffffff;
        }
        .link-start {
            display: inline-block;
            color: #333;
            background: #afeeee;
            padding: 20px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2em;
            transition: all 0.3s;
            box-shadow: 12px 12px 24px 0 rgba(0, 0, 0, 0.2), -12px -12px 24px 0 rgba(255, 255, 255, 0.5);
        }
        .link-start:hover {
            box-shadow: inset 6px 6px 10px 0 rgba(0, 0, 0, 0.2), inset -6px -6px 10px 0 rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
<div id="app"></div>
</body>
</html>
