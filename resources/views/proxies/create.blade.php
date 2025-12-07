@extends('layout')

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Add Proxies</h1>
    <a href="/proxies" class="btn btn-outline-secondary">← Back to list</a>
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
    <form method="POST" action="/proxies/store" class="row g-4">
        @csrf

        <div class="col-12 col-lg-4">
            <label class="form-label fw-semibold">Default protocol</label>
            @php $selected = old('default_protocol', 'default'); @endphp
            <select name="default_protocol" class="form-select">
                <option value="default" @selected($selected === 'default')>Auto (HTTP unless specified)</option>
                <option value="http" @selected($selected === 'http')>HTTP</option>
                <option value="socks5" @selected($selected === 'socks5')>SOCKS5</option>
            </select>
        </div>

        <div class="col-12 col-lg-4">
            <label class="form-label fw-semibold">Label (optional)</label>
            <input type="text"
                   name="label"
                   class="form-control"
                   value="{{ old('label') }}"
                   placeholder="e.g. Dynadot farm #1">
            <div class="form-text">Метка применится ко всем прокси из списка.</div>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Proxies list</label>
            <textarea name="proxies" rows="12" class="form-control" placeholder="username:pass@1.1.1.1:8080">{{ old('proxies') }}</textarea>
            <div class="form-text">
                Allowed formats:<br>
                ip:port<br>
                username:pass@ip:port<br>
                socks5://ip:port<br>
                socks5://username:pass@ip:port
            </div>
        </div>

        <div class="col-12 text-end">
            <button class="btn btn-success">Save proxies</button>
        </div>
    </form>
</div>

@endsection
