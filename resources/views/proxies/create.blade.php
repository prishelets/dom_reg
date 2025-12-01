<!DOCTYPE html>
<html>
<head>
    <title>Add Proxy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 p-6">

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-semibold mb-6">Add Proxy</h2>

    <form method="POST" action="/proxies/store" class="space-y-6">
        @csrf

        <div>
            <label class="block font-medium mb-1">Protocol</label>
            <select name="protocol" class="w-full border rounded px-3 py-2">
                <option value="http">http</option>
                <option value="https">https</option>
                <option value="socks5">socks5</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Login</label>
            <input type="text" name="login" class="w-full border rounded px-3 py-2" placeholder="Optional">
        </div>

        <div>
            <label class="block font-medium mb-1">Password</label>
            <input type="text" name="password" class="w-full border rounded px-3 py-2" placeholder="Optional">
        </div>

        <div>
            <label class="block font-medium mb-1">IP</label>
            <input type="text" name="ip" class="w-full border rounded px-3 py-2" placeholder="192.168.1.50" required>
        </div>

        <div>
            <label class="block font-medium mb-1">Port</label>
            <input type="number" name="port" class="w-full border rounded px-3 py-2" placeholder="8080" required>
        </div>

        <div class="pt-4 flex justify-end">
            <button class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                Save Proxy
            </button>
        </div>

    </form>

</div>

</body>
</html>
