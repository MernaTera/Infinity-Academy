<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>@yield('title', 'Admin') — Infinity Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <div style="min-height:100vh;background:radial-gradient(820px 480px at 8% -5%, rgba(245,145,30,0.16), transparent 60%),radial-gradient(900px 560px at 100% 0%, rgba(45,111,219,0.20), transparent 55%),radial-gradient(700px 520px at 55% 120%, rgba(127,119,221,0.14), transparent 60%),linear-gradient(135deg,#eef2fb 0%,#f5eefb 50%,#fdf1e6 100%);background-attachment:fixed;">
        @include('admin.partials.navbar')

        <div style="display:flex;min-height:calc(100vh - 62px);">
            @include('admin.partials.sidebar')
            <main style="flex:1;overflow-x:hidden;">
                @yield('content')
            </main>
        </div>
    </div>
@include('partials.realtime-notifications')
</body>
</html>