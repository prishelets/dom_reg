<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Control Panel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white border-b border-gray-300 px-6 py-3 flex gap-4">

    <a href="/tasks"
       class="px-3 py-1.5 rounded-md text-sm font-medium
              {{ request()->is('tasks*') 
                    ? 'bg-blue-600 text-white shadow' 
                    : 'text-gray-700 hover:bg-gray-200' }}">
        Tasks
    </a>

    <a href="/proxies"
       class="px-3 py-1.5 rounded-md text-sm font-medium
              {{ request()->is('proxies*') 
                    ? 'bg-blue-600 text-white shadow' 
                    : 'text-gray-700 hover:bg-gray-200' }}">
        Proxies
    </a>

    <a href="/cards"
   class="px-3 py-1.5 rounded-md text-sm font-medium
          {{ request()->is('cards*') ? 'bg-blue-600 text-white shadow' : 'text-gray-700 hover:bg-gray-200' }}">
    Cards
</a>

</nav>


    <!-- MAIN CONTENT -->
    <div class="p-6">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
