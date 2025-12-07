@extends('layout')

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Create Task</h1>
    <a href="/tasks" class="btn btn-outline-secondary">← Back to list</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="table-card">
    <form method="POST" action="/tasks/store" class="row g-4">
        @csrf
        <input type="hidden" name="form_context" value="page">

        <div class="col-12 col-md-4">
            <div class="d-flex flex-column gap-3">
                <div>
                    <label class="form-label fw-semibold">Registrar</label>
                    <select name="registrar" class="form-select">
                        <option value="dynadot.com">Dynadot.com</option>
                    </select>
                </div>

                <div>
                    <label class="form-label fw-semibold">Country</label>
                    <select name="country" class="form-select">
                        <option value="uk">United Kingdom</option>
                        <option value="it">Italy</option>
                    </select>
                </div>

                <div>
                    <label class="form-label fw-semibold">Brand</label>
                    <input type="text" name="brand" class="form-control" placeholder="Brand name">
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <label class="form-label fw-semibold">Domains (one per line)</label>
            <textarea name="domains" rows="12" class="form-control" placeholder="example.com"></textarea>
        </div>

        <div class="col-12 text-end">
            <button class="btn btn-success">Create Task</button>
        </div>
    </form>
</div>

@endsection
