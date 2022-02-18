<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Expires" content="0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF8"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="language" content="en-US"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="{{ env('APP_NAME') }}">
    <meta name="publisher" content="VS 2021"/>
    <meta name="theme-color" content="#22313a">
    <meta name="msapplication-navbutton-color" content="#22313a">
    <meta name="apple-mobile-web-app-status-bar-style" content="#22313a">
    <link rel="shortcut icon" href="{{ url("assets/img/logos/icon.ico") }}" type="image/x-icon"/>


    <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}?v={{ rand(1111,9999) }}" type="text/css">
    <title>{{ config('app.name') }}</title>
</head>

<body>
<div id="app">
    <app></app>
</div>
<script>

</script>
<script src="{{ mix('assets/js/main.js') }}?v={{ rand(1111,9999) }}" async></script>


</body>

</html>
