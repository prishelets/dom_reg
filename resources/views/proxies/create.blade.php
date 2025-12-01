@extends('layout')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow space-y-6">
    <h2 class="text-2xl font-semibold">Add Proxies</h2>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-4 py-2 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/proxies/store" class="space-y-5">
        @csrf

        <div>
            <label class="block font-medium mb-1">Default protocol</label>
            <select name="default_protocol" class="w-full border rounded px-3 py-2">
                @php $selected = old('default_protocol', 'default'); @endphp
                <option value="default" @selected($selected === 'default')>Auto (HTTP unless specified)</option>
                <option value="http" @selected($selected === 'http')>HTTP</option>
                <option value="socks5" @selected($selected === 'socks5')>SOCKS5</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Proxies list</label>
            <textarea name="proxies" rows="10" class="w-full border rounded px-3 py-2" placeholder="username:pass@1.1.1.1:8080">{{ old('proxies') }}</textarea>
            <p class="text-sm text-gray-600 mt-2">
                Allowed formats:
                <br>ip:port
                <br>username:pass@ip:port
                <br>socks5://ip:port
                <br>socks5://username:pass@ip:port
            </p>
        </div>

        <div class="pt-2 flex justify-end">
            <button class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                Save proxies
            </button>
        </div>
    </form>
</div>

@endsection
