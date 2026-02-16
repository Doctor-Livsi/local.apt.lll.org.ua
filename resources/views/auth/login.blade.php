<!DOCTYPE html>
<html lang="uk" data-url-prefix="/" data-placement="horizontal" data-behaviour="pinned" data-layout="fluid" data-radius="rounded" data-color="light-sky" data-navcolor="default" data-show="true">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IT 911 | Авторизація</title>
    <meta name="description" content="Login Page">
    <!-- Favicon Tags Start -->
    <!-- Основная иконка — SVG для современных браузеров -->
    <link rel="icon" href="/favicon-red.svg" sizes="48x48" type="image/svg+xml">
    <!-- Fallback для Safari, старых браузеров и максимальной совместимости -->
    <link rel="icon" href="/favicon-red.ico" sizes="32x32" type="image/x-icon">
    <!-- Для добавления на главный экран iPhone/iPad (Safari не поддерживает SVG) -->
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <link rel="preconnect" href="https://fonts.gstatic.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/font/CS-Interface/style.css"/>

    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/main.css">
    <!-- Template Base Styles End -->
    @vite(['resources/js/pages/login/login.js'])
    <script src="/js/pages/logout.js"></script>
</head>
<body class="h-100" data-bs-padding="0px">
<div id="login">
{{--    <login-page :csrf-token="'{{ csrf_token() }}'"></login-page>--}}
</div>

<!-- Vendor Scripts Start -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="/icon/acorn-icons.js"></script>
<script src="/icon/acorn-icons-interface.js"></script>
<script src="/js/vendor/jquery.validate/jquery.validate.min.js"></script>
<script src="/js/vendor/jquery.validate/additional-methods.min.js"></script>
<script src="/js/base/globals.js"></script>
<script src="/js/common.js"></script>
<script src="/js/scripts.js"></script>
</body>
</html>

