<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>Student Care — @yield('title', 'Infinity Academy')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;background:#F8F6F2;font-family:'DM Sans',sans-serif;">

    {{-- Navbar --}}
    @include('student-care.partials.navbar')

    {{-- Layout --}}
    <div style="display:flex;min-height:calc(100vh - 62px);">

        {{-- Sidebar (بدون <aside> wrapper زيادة) --}}
        @include('student-care.partials.sidebar')

        {{-- Content --}}
        <main style="flex:1;overflow-x:hidden;min-width:0;padding:30px;">
            @yield('content')
        </main>

    </div>

</body>
</html>