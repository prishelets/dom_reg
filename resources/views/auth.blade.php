<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <form method="POST" action="/login" class="bg-white p-8 rounded shadow w-80 space-y-4">
        @csrf

        <h1 class="text-xl font-semibold text-center mb-4">Login</h1>

        <div>
            <label class="block text-sm">Email</label>
            <input type="text" name="email" class="w-full border rounded px-3 py-2" />
            @error('email')
                <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" />
        </div>

        <button class="w-full bg-blue-600 text-white py-2 rounded mt-2 hover:bg-blue-700">
            Login
        </button>
    </form>

</body>
</html>
