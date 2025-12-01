@extends('layout', ['title' => 'Cards'])

@section('content')

<h1 class="text-2xl font-semibold mb-4">Cards</h1>

<a href="/cards/create" class="btn btn-success mb-4">➕ Add Card</a>

@if(session('success'))
    <div class="p-2 bg-green-200 text-green-800 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="min-w-full border border-gray-300 text-sm">
    <thead class="bg-gray-100">
    <tr>
        <th class="border px-3 py-2">ID</th>
        <th class="border px-3 py-2">Holder</th>
        <th class="border px-3 py-2">Number</th>
        <th class="border px-3 py-2">Exp</th>
        <th class="border px-3 py-2">Bank</th>
        <th class="border px-3 py-2">Actions</th>
    </tr>
    </thead>

    <tbody>
    @foreach($cards as $card)
        <tr class="odd:bg-white even:bg-gray-50">
            <td class="border px-3 py-2">{{ $card->id }}</td>
            <td class="border px-3 py-2">{{ $card->holder }}</td>
            <td class="border px-3 py-2">{{ $card->number }}</td>
            <td class="border px-3 py-2">{{ $card->exp_month }}/{{ $card->exp_year }}</td>
            <td class="border px-3 py-2">{{ $card->bank }}</td>

            <td class="border px-3 py-2">
                <form action="/cards/{{ $card->id }}/delete" method="POST">
                    @csrf
                    <button class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
