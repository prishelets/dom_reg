@extends('layout')

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Settings</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

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
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-accounts" data-bs-toggle="tab" data-bs-target="#tab-pane-accounts" type="button"
                    role="tab" aria-controls="tab-pane-accounts" aria-selected="true">
                Dynadot Account Registration
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-cloudflare" data-bs-toggle="tab" data-bs-target="#tab-pane-cloudflare" type="button"
                    role="tab" aria-controls="tab-pane-cloudflare" aria-selected="false">
                Cloudflare
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-pane-accounts" role="tabpanel" aria-labelledby="tab-accounts">
            <div class="mb-4">
                <div class="text-muted text-uppercase small fw-semibold">Dynadot next account creation at:</div>
                <div class="fs-5">{{ $last_run }}</div>
            </div>

            <form method="POST" action="/settings" class="row g-4" id="settings-form">
        @csrf
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="enable_schedule" name="enable_schedule" value="1"
                       @checked(old('enable_schedule', $enabled))>
                <label class="form-check-label fw-semibold" for="enable_schedule">
                    Enable Dynadot scheduling
                </label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Interval from (minutes)</label>
            <input type="number" min="1" max="1440" name="interval_from" class="form-control schedule-field"
                   value="{{ old('interval_from', $interval_from) }}" required>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Interval to (minutes)</label>
            <input type="number" min="1" max="1440" name="interval_to" class="form-control schedule-field"
                   value="{{ old('interval_to', $interval_to) }}" required>
        </div>

        <div class="col-12">
            <hr>
            <h5 class="fw-semibold">Account ready delay</h5>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Ready interval from (hours)</label>
            <input type="number" min="1" max="168" name="ready_from" class="form-control"
                   value="{{ old('ready_from', $ready_from) }}" required>
        </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Ready interval to (hours)</label>
                <input type="number" min="1" max="168" name="ready_to" class="form-control"
                       value="{{ old('ready_to', $ready_to) }}" required>
            </div>

            <div class="col-12">
                <hr>
                <h5 class="fw-semibold">Account next-check delay</h5>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Next check interval from (hours)</label>
                <input type="number" min="1" max="168" name="check_from" class="form-control"
                       value="{{ old('check_from', $check_from) }}" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Next check interval to (hours)</label>
                <input type="number" min="1" max="168" name="check_to" class="form-control"
                       value="{{ old('check_to', $check_to) }}" required>
            </div>

        <div class="col-12">
            <hr>
            <h5 class="fw-semibold">Emails (ID|domain per line)</h5>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Email domains</label>
            <textarea name="email_domains" rows="6" class="form-control" placeholder="123|example.com&#10;456|domain.net">{{ old('email_domains', $email_domains) }}</textarea>
            <div class="form-text">Формат: <code>id|domain</code> на каждой строке.</div>
        </div>

        <div class="col-12 text-end">
            <button class="btn btn-success">Save settings</button>
        </div>
            </form>
        </div>

        <div class="tab-pane fade" id="tab-pane-cloudflare" role="tabpanel" aria-labelledby="tab-cloudflare">
            <form method="POST" action="/settings/cloudflare" class="row g-4">
                @csrf
                <div class="col-12">
                    <label class="form-label fw-semibold">Cloudflare Notes</label>
                    <textarea name="cloudflare_note" rows="10" class="form-control" placeholder="Enter Cloudflare related notes">{{ old('cloudflare_note', $cloudflare_note) }}</textarea>
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-success">Save Cloudflare</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkbox = document.getElementById('enable_schedule');
        const fields = document.querySelectorAll('.schedule-field');
        const form = document.getElementById('settings-form');

        const toggleFields = () => {
            fields.forEach(field => {
                field.disabled = !checkbox.checked;
                field.closest('.col-12').style.opacity = checkbox.checked ? '1' : '0.6';
            });
        };

        toggleFields();
        checkbox.addEventListener('change', toggleFields);

        form.addEventListener('submit', () => {
            fields.forEach(field => field.disabled = false);
        });
    });
</script>
@endpush

@endsection
