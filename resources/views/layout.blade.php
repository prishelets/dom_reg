<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Control Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GLOBAL BUTTON STYLES -->
    <style>
        .btn {
            @apply px-4 py-2 rounded font-medium text-sm transition inline-block;
        }
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        .btn-success {
            @apply bg-green-600 text-white hover:bg-green-700;
        }
        .btn-danger {
            @apply bg-red-600 text-white hover:bg-red-700;
        }
        .btn-gray {
            @apply bg-gray-600 text-white hover:bg-gray-700;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <nav class="nav-bg text-white px-6 py-4 flex gap-6 shadow">
    <a href="/tasks"
       class="nav-text font-medium {{ request()->is('tasks*') ? 'nav-active' : '' }}">
        Tasks
    </a>

    <a href="/proxies"
       class="nav-text font-medium {{ request()->is('proxies*') ? 'nav-active' : '' }}">
        Proxies
    </a>
</nav>

    <!-- MAIN CONTENT -->
    <div class="p-6">
        @yield('content')
    </div>

</body>
</html>
