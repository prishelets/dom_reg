@extends('layout', ['title' => 'Add Card'])

@section('content')

<h1 class="text-2xl font-semibold mb-4">Add Card</h1>

<form method="POST" action="/cards/store" class="space-y-4">
    @csrf

    <div>
        <label class="block font-medium">Card Holder</label>
        <input type="text" name="holder" class="border px-3 py-2 rounded w-full">
    </div>

    <div>
        <label class="block font-medium">Card Number</label>
        <input type="text" name="number" class="border px-3 py-2 rounded w-full">
    </div>

    <div class="flex gap-4">
        <div class="w-24">
            <label class="block font-medium">Month</label>
            <input type="text" name="exp_month" class="border px-3 py-2 rounded w-full">
        </div>

        <div class="w-24">
            <label class="block font-medium">Year</label>
            <input type="text" name="exp_year" class="border px-3 py-2 rounded w-full">
        </div>

        <div class="w-24">
            <label class="block font-medium">CVV</label>
            <input type="text" name="cvv" class="border px-3 py-2 rounded w-full">
        </div>
    </div>

    <div>
        <label class="block font-medium">Bank</label>
        <input type="text" name="bank" class="border px-3 py-2 rounded w-full">
    </div>

    <button class="btn btn-success">Add Card</button>

</form>

@endsection
