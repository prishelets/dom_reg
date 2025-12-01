@extends('layout')

@section('content')

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-semibold mb-6">Create Task</h2>

    <form method="POST" action="/tasks/store" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN -->
            <div class="space-y-4">

                <!-- Registrar -->
                <div>
                    <label class="block font-medium mb-1">Registrar</label>
                    <select name="registrar" class="w-full border rounded px-3 py-2">
                        <option value="dynadot.com">Dynadot.com</option>
                        <option value="namecheap.com">Namecheap.com</option>
                        <option value="godaddy.com">GoDaddy.com</option>
                    </select>
                </div>

                <!-- Country -->
                <div>
                    <label class="block font-medium mb-1">Country</label>
                    <select name="country" class="w-full border rounded px-3 py-2">
                        <option value="uk">United Kingdom</option>
                        <option value="us">United States</option>
                        <option value="ua">Ukraine</option>
                        <option value="de">Germany</option>
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label class="block font-medium mb-1">Brand</label>
                    <input type="text" name="brand"
                           class="w-full border rounded px-3 py-2"
                           placeholder="Brand name">
                </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="md:col-span-2">
                <label class="block font-medium mb-1">Domains (one per line)</label>
                <textarea name="domains"
                          rows="12"
                          class="w-full border rounded px-3 py-2"></textarea>
            </div>

        </div>

        <!-- BUTTON -->
        <div class="pt-4">
            <button class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                Create Task
            </button>
        </div>

    </form>

</div>

@endsection