<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Control Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- NAVIGATION -->
    <nav class="bg-gray-800 text-white px-6 py-3 flex gap-6">
        <a href="/tasks" class="hover:text-gray-300 {{ request()->is('tasks*') ? 'underline' : '' }}">
            Tasks
        </a>

        <a href="/proxies" class="hover:text-gray-300 {{ request()->is('proxies*') ? 'underline' : '' }}">
            Proxies
        </a>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="p-6">
        @yield('content')
    </div>

</body>
</html>
