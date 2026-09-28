<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen" style="background:radial-gradient(820px 480px at 8% -5%, rgba(245,145,30,0.16), transparent 60%),radial-gradient(900px 560px at 100% 0%, rgba(45,111,219,0.20), transparent 55%),radial-gradient(700px 520px at 55% 120%, rgba(127,119,221,0.14), transparent 60%),linear-gradient(135deg,#eef2fb 0%,#f5eefb 50%,#fdf1e6 100%);background-attachment:fixed;">
        @include('layouts.navigation')
        <div style="display:flex;min-height:calc(100vh - 62px);">
            @include('layouts.leads-sidebar')
            <main style="flex:1;overflow-x:hidden;">
                @yield('content')
            </main>
        </div>
    </div>
    @include('partials.realtime-notifications')
</body>
</html>