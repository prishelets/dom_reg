<!DOCTYPE html>
<html>
<head>
    <title>Tasks</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="p-4">

<h2>Tasks</h2>

<a href="/tasks/create"
   class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md shadow hover:bg-green-700 mb-4">
    ➕ <span>Add Task</span>
</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="min-w-full border border-gray-300 text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="border px-3 py-2 text-left">ID</th>
            <th class="border px-3 py-2 text-left">Domain</th>
            <th class="border px-3 py-2 text-left">Registrar</th>
            <th class="border px-3 py-2 text-left">Country</th>
            <th class="border px-3 py-2 text-left">Brand</th>
            <th class="border px-3 py-2 text-left">Status</th>
            <th class="border px-3 py-2 text-left">Email Login</th>
            <th class="border px-3 py-2 text-left">Proxy</th>
            <th class="border px-3 py-2 text-left">CF Email</th>
            <th class="border px-3 py-2 text-left">Domain Paid</th>
            <th class="border px-3 py-2 text-left">NS Servers</th>
            <th class="border px-3 py-2 text-left">Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($tasks as $task)
        <tr class="odd:bg-white even:bg-gray-50">
            <td class="border px-3 py-2">{{ $task->id }}</td>
            <td class="border px-3 py-2">{{ $task->domain }}</td>
            <td class="border px-3 py-2">{{ $task->registrar }}</td>
            <td class="border px-3 py-2">{{ $task->country }}</td>
            <td class="border px-3 py-2">{{ $task->brand }}</td>
            <td class="border px-3 py-2">{{ $task->status }}</td>
            <td class="border px-3 py-2">{{ $task->email_login }}</td>
            <td class="border px-3 py-2">{{ $task->proxy }}</td>
            <td class="border px-3 py-2">{{ $task->cloudflare_email }}</td>
            <td class="border px-3 py-2">{{ $task->domain_paid ? 'Yes' : 'No' }}</td>
            <td class="border px-3 py-2">{{ $task->ns_servers }}</td>

            <td class="border px-3 py-2 flex gap-2">

                {{-- DELETE --}}
                <form method="POST" action="/tasks/{{ $task->id }}/delete" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    <button class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>

                {{-- OPEN IN INCOGNITO --}}
                <a href="https://{{ $task->domain }}" target="_blank"
                    class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                    Open Browser
                </a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>


</body>
</html>
