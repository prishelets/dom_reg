@extends('layout')

@section('content')

<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Proxy List</h1>

        <a href="/proxies/create"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            ➕ Add Proxy
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">ID</th>
                <th class="border px-3 py-2">Protocol</th>
                <th class="border px-3 py-2">Login</th>
                <th class="border px-3 py-2">Password</th>
                <th class="border px-3 py-2">IP</th>
                <th class="border px-3 py-2">Port</th>
                <th class="border px-3 py-2">Active</th>
                <th class="border px-3 py-2">Last used</th>
                <th class="border px-3 py-2">Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($proxies as $proxy)
            <tr class="odd:bg-white even:bg-gray-50">
                <td class="border px-3 py-2">{{ $proxy->id }}</td>
                <td class="border px-3 py-2">{{ $proxy->protocol }}</td>
                <td class="border px-3 py-2">{{ $proxy->login }}</td>
                <td class="border px-3 py-2">{{ $proxy->password }}</td>
                <td class="border px-3 py-2">{{ $proxy->ip }}</td>
                <td class="border px-3 py-2">{{ $proxy->port }}</td>
                <td class="border px-3 py-2">{{ $proxy->active ? 'Yes' : 'No' }}</td>
                <td class="border px-3 py-2">
                    {{ $proxy->last_used_at ? $proxy->last_used_at : '—' }}
                </td>

                <td class="border px-3 py-2">
                    <a href="#" class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                        Edit
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

@endsection
