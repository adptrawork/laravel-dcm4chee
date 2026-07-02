<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    <p><a href="{{ route('login') }}">Silakan login untuk melanjutkan</a></p>
</body>
</html>
