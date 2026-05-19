<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>on¿on</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.bunny.net/css?family=figtree:300,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/css/profile.css', 'resources/js/app.js', 'resources/js/custom.js'])
    @livewireStyles
</head>
<body style="background-color: #E3C4A8; margin: 0; font-family: 'Figtree', sans-serif;">
    <main>
        @yield('content')
    </main>

    @stack('modals')
    @livewireScripts
</body>
</html>