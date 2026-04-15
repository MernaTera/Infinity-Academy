<!DOCTYPE html>
<html>
<head>
    <title>Student Care</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="/student-care/dashboard">Dashboard</a>
        <a href="/student-care/waiting-list">Waiting List</a>
        <a href="#">Assignments</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        @yield('content')
    </div>

</body>
</html>