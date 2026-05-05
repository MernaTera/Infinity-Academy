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
<body style="margin:0;background:#F8F6F2;font-family:'DM Sans',sans-serif;">

    @include('admin.partials.navbar')

    <div style="display:flex;min-height:calc(100vh - 62px);">
        @include('admin.partials.sidebar')
        <main style="flex:1;overflow-x:hidden;">
            @yield('content')
        </main>
    </div>

</body>
</html>