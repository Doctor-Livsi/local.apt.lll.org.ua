<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/favicon-red.svg" type="image/svg+xml">
    <title>Помилка 404</title>
    <style>
        html, body { height: 100%; margin: 0; background: #fff; }
        .bg {
            position: fixed; inset: 0;
            background: url("{{ asset('images/errors/404.png') }}") center/cover no-repeat;
        }
        /* если хочешь поверх кнопки/текст */
        .overlay {
            position: fixed; inset: 0;
            display: flex; align-items: flex-end; justify-content: center;
            padding: 24px;
            pointer-events: none;
        }
        .overlay a { pointer-events: auto; }
        .btn {
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(0,0,0,.06);
            border-radius: 12px;
            padding: 10px 14px;
            text-decoration: none;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: #111;
        }
    </style>
</head>
<body>
<div class="bg"></div>
<div class="overlay">
    <a class="btn" href="{{ url('/') }}">На головну</a>
</div>
</body>
</html>
