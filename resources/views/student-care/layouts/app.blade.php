<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Care</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="margin:0; background:#f7f9fc; font-family:'DM Sans', sans-serif;">

    {{-- Navbar --}}
    @include('student-care.partials.navbar')

    <div style="display:flex; min-height:calc(100vh - 62px);">

        {{-- Sidebar --}}
        <aside style="width:250px; background:#fff; border-right:1px solid #e5e7eb;">
            @include('student-care.partials.sidebar')
        </aside>

        {{-- Content --}}
        <main style="flex:1; padding:30px;">
            @yield('content')
        </main>

    </div>

</body>
</html>