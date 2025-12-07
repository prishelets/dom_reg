@extends('layout')

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Proxies</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="/proxies/create" class="btn btn-success">
            <span class="me-1">➕</span> Add Proxy
        </a>
        @if($proxies->count())
            <form method="POST" action="/proxies/delete-all" onsubmit="return confirm('Delete all proxies?');">
                @csrf
                <button class="btn btn-outline-danger">Delete All</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@elseif(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Protocol</th>
                    <th>Login</th>
                    <th>Password</th>
                    <th>IP</th>
                    <th>Port</th>
                    <th>Label</th>
                    <th>Active</th>
                    <th>Last Used</th>
                    <th class="text-center">Stats</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($proxies as $proxy)
                <tr>
                    <td>{{ $proxy->id }}</td>
                    <td>{{ strtoupper($proxy->protocol) }}</td>
                    <td>{{ $proxy->login }}</td>
                    <td>{{ $proxy->password }}</td>
                    <td>{{ $proxy->ip }}</td>
                    <td>{{ $proxy->port }}</td>
                    <td>{{ $proxy->label ?? '—' }}</td>
                    <td>
                        @if($proxy->active)
                            <span class="badge text-bg-primary custom-badge-blue">Active</span>
                        @else
                            <span class="badge text-bg-secondary custom-badge-gray">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $proxy->last_used_at ? $proxy->last_used_at : '—' }}</td>
                    @php
                        $infoData = $proxy->info ? json_decode($proxy->info, true) : null;
                        $hasInfo = $infoData && (count($infoData['success'] ?? []) || count($infoData['error'] ?? []));
                    @endphp
                    <td class="text-center">
                        <span class="badge bg-success">{{ $proxy->success_count ?? 0 }}</span>
                        <span class="text-muted mx-1">/</span>
                        <span class="badge bg-danger">{{ $proxy->error_count ?? 0 }}</span>
                    </td>
                    <td class="text-center">
                        @if($hasInfo)
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary me-1"
                                    data-proxy-info='@json($infoData)'
                                    data-proxy-label="Proxy #{{ $proxy->id }} ({{ $proxy->ip }}:{{ $proxy->port }})"
                                    data-total-success="{{ $proxy->success_count ?? 0 }}"
                                    data-total-error="{{ $proxy->error_count ?? 0 }}">
                                Info
                            </button>
                        @endif
                        <form method="POST" action="/proxies/{{ $proxy->id }}/delete"
                              onsubmit="return confirm('Delete this proxy?');" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="proxyInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <p class="text-muted small mb-0">Proxy</p>
                    <h5 class="modal-title" id="proxy-info-title"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="proxy-info-status" class="text-muted small mb-3"></div>
                <div class="table-responsive d-none" id="proxy-info-table">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mode</th>
                                <th class="text-center">Success</th>
                                <th class="text-center">Error</th>
                            </tr>
                        </thead>
                        <tbody id="proxy-info-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('proxyInfoModal');
    if (!modalEl) {
        return;
    }

    const infoModal = new bootstrap.Modal(modalEl);
    const titleEl = document.getElementById('proxy-info-title');
    const statusEl = document.getElementById('proxy-info-status');
    const tableWrapper = document.getElementById('proxy-info-table');
    const tableBody = document.getElementById('proxy-info-body');

    const renderRows = (data) => {
        tableBody.innerHTML = '';
        const successMap = data.success || {};
        const errorMap = data.error || {};
        const modes = Array.from(new Set([
            ...Object.keys(successMap),
            ...Object.keys(errorMap),
        ]));

        if (!modes.length) {
            tableWrapper.classList.add('d-none');
            statusEl.textContent = 'No mode-specific data yet.';
            return;
        }

        tableWrapper.classList.remove('d-none');
        modes.sort();
        modes.forEach((mode) => {
            const row = document.createElement('tr');
            const successValue = successMap[mode] ?? 0;
            const errorValue = errorMap[mode] ?? 0;

            const modeCell = document.createElement('td');
            modeCell.textContent = mode;

            const successCell = document.createElement('td');
            successCell.className = 'text-center fw-semibold text-brand-green';
            successCell.textContent = successValue;

            const errorCell = document.createElement('td');
            errorCell.className = 'text-center fw-semibold text-brand-red';
            errorCell.textContent = errorValue;

            row.appendChild(modeCell);
            row.appendChild(successCell);
            row.appendChild(errorCell);
            tableBody.appendChild(row);
        });
    };

    document.querySelectorAll('[data-proxy-info]').forEach((button) => {
        button.addEventListener('click', () => {
            titleEl.textContent = button.getAttribute('data-proxy-label') || 'Proxy details';
            const totalSuccess = button.getAttribute('data-total-success') || '0';
            const totalError = button.getAttribute('data-total-error') || '0';
            let parsedInfo = null;

            try {
                parsedInfo = JSON.parse(button.getAttribute('data-proxy-info') || '{}');
            } catch (error) {
                parsedInfo = null;
            }

            if (!parsedInfo) {
                statusEl.textContent = 'No statistics available for this proxy.';
                tableWrapper.classList.add('d-none');
            } else {
                statusEl.textContent = `Total: ${totalSuccess} success / ${totalError} error`;
                renderRows(parsedInfo);
            }

            infoModal.show();
        });
    });
});
</script>
@endpush

@endsection
