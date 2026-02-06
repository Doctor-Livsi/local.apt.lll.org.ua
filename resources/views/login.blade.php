<!DOCTYPE html>
<html lang="uk" data-url-prefix="/" data-placement="horizontal" data-behaviour="pinned" data-layout="fluid" data-radius="rounded" data-color="light-sky" data-navcolor="default" data-show="true">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IT 911 | Авторизація</title>
    <meta name="description" content="Login Page">

    <!-- Favicon Tags Start -->
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/img/favicon/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/img/favicon/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/img/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/img/favicon/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="/img/favicon/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="/img/favicon/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="/img/favicon/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="/img/favicon/apple-touch-icon-152x152.png">
    <link rel="icon" type="image/png" href="/img/favicon/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/img/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/img/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/img/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/img/favicon/favicon-128.png" sizes="128x128">
    <meta name="application-name" content=" ">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="/img/favicon/mstile-144x144.png">
    <meta name="msapplication-square70x70logo" content="/img/favicon/mstile-70x70.png">
    <meta name="msapplication-square150x150logo" content="/img/favicon/mstile-150x150.png">
    <meta name="msapplication-wide310x150logo" content="/img/favicon/mstile-310x150.png">
    <meta name="msapplication-square310x310logo" content="/img/favicon/mstile-310x310.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/font/CS-Interface/style.css"/>

    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/main.css">
    <!-- Template Base Styles End -->
    @vite(['resources/js/pages/Login/login.js'])
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

