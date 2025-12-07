@extends('layout')

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Tasks</h1>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="fa-solid fa-plus me-1"></i> Add Tasks
        </button>
        <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#batchTaskModal">
            <i class="fa-solid fa-layer-group me-1"></i> Add Tasks Batch
        </button>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#searchTaskModal">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Find Tasks
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
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
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
            <i class="fa-solid fa-rotate-right me-1"></i> Refresh
        </button>
    </div>
    <form method="GET" class="row g-3 align-items-end mb-4" id="filter-form">
        <div class="col-12 col-md-5 col-lg-4">
            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                Status
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#statusFilterModal">
                    Filter
                </button>
            </label>
            <div id="status-hidden-inputs">
                @foreach($statusFilter as $selected)
                    <input type="hidden" name="status[]" value="{{ $selected }}">
                @endforeach
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2" id="selected-status-chips">
                @forelse($statusFilter as $selected)
                    <button type="button" class="btn btn-sm btn-outline-secondary status-chip" data-status="{{ $selected }}">
                        {{ $selected }} <span class="ms-1">&times;</span>
                    </button>
                @empty
                    <span class="text-muted small">All statuses</span>
                @endforelse
            </div>
        </div>

        <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label fw-semibold">Rows per page</label>
            <select name="per_page" class="form-select" onchange="this.form.submit()">
                @foreach([20, 50, 100] as $size)
                    <option value="{{ $size }}" @selected(($perPage ?? 20) == $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-md-4 col-lg-3 text-md-end">
            <button type="submit" class="btn btn-outline-secondary mt-2 mt-md-0">Apply filters</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="text-muted small">
            @if($tasks->total())
                Showing {{ $tasks->firstItem() }}-{{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
            @else
                No tasks found
            @endif
        </div>
        {{ $tasks->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Domain</th>
                    <th>Registrar</th>
                    <th>Country</th>
                    <th>Brand</th>
                    <th>Status</th>
                    <th class="text-center">Account</th>
                    <th>Domain Paid</th>
                    <th>NS Servers</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->domain }}</td>
                        <td>{{ $task->registrar }}</td>
                    <td>
                        @php
                            $country = $task->country ? config('countries.' . strtolower($task->country)) : null;
                        @endphp
                        @if($country && isset($country['code']))
                            <span class="country-flag" title="{{ $country['name'] }}">
                                <img src="https://flagcdn.com/24x18/{{ $country['code'] }}.png" alt="{{ $country['name'] }} flag">
                            </span>
                        @else
                            <span class="text-muted">{{ strtoupper($task->country ?? '-') }}</span>
                        @endif
                    </td>
                        <td>{{ $task->brand }}</td>
                        <td>
                            <span class="{{ $task->statusColorClass() }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(!empty($task->registrar_email))
                                <button type="button"
                                        class="btn btn-sm btn-outline-green"
                                        data-action="show-account"
                                        data-task-id="{{ $task->id }}"
                                        data-task-label="{{ $task->domain }}">
                                    Show
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($task->domain_paid)
                                <button type="button"
                                        class="btn btn-sm btn-outline-green"
                                        data-action="show-domain-info"
                                        data-paid-date="{{ $task->domain_paid_date }}"
                                        data-paid-price="{{ $task->domain_paid_price }}"
                                        data-paid-currency="{{ $task->domain_paid_currency }}"
                                        data-paid-card-full="{{ $task->domain_paid_card_number }}"
                                        data-task-label="{{ $task->domain }}">
                                    Show
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>{{ $task->ns_servers }}</td>
                        <td class="text-center">
                            <div class="actions-grid">
                                @if($task->logs_exists)
                                    <button type="button"
                                            class="btn btn-sm btn-secondary"
                                            data-action="show-logs"
                                            data-task-id="{{ $task->id }}"
                                            data-task-label="{{ $task->domain }}">
                                        Logs
                                    </button>
                                @else
                                    <span class="btn btn-sm btn-secondary btn-placeholder">Logs</span>
                                @endif

                                <a href="https://{{ $task->domain }}" target="_blank"
                                   class="btn btn-sm btn-primary">
                                    Open
                                </a>

                                <form method="POST" action="/tasks/{{ $task->id }}/delete"
                                      class="m-0"
                                      onsubmit="return confirm('Delete this task?');">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <div class="text-muted small">
            @if($tasks->total())
                Showing {{ $tasks->firstItem() }}-{{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
            @else
                No tasks found
            @endif
        </div>
        {{ $tasks->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/tasks/store">
                @csrf
                <input type="hidden" name="form_context" value="inline">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-12 col-lg-5">
                            <label class="form-label fw-semibold">Registrar</label>
                            <select name="registrar" class="form-select">
                                <option value="dynadot.com" @selected(old('registrar') === 'dynadot.com')>Dynadot.com</option>
                            </select>
                            @error('registrar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-semibold">Country</label>
                            <select name="country" class="form-select">
                                <option value="uk" @selected(old('country') === 'uk')>United Kingdom</option>
                                <option value="it" @selected(old('country') === 'it')>Italy</option>
                            </select>
                            @error('country')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="form-label fw-semibold">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="Brand name">
                            @error('brand')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Domains (one per line)</label>
                            <textarea name="domains" rows="8" class="form-control" placeholder="example.com">{{ old('domains') }}</textarea>
                            @error('domains')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green">Add Tasks</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="batchTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tasks Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/tasks/batch-upload" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registrar</label>
                        <select name="batch_registrar" class="form-select">
                            <option value="dynadot.com" @selected(old('batch_registrar') === 'dynadot.com')>Dynadot.com</option>
                        </select>
                        @error('batch_registrar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Excel file (.xlsx or .csv)</label>
                        <input type="file" name="batch_file" class="form-control" accept=".xlsx,.csv">
                        @error('batch_file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Файл должен содержать три столбца: <strong>Domain</strong>, <strong>Brand</strong>, <strong>Country</strong> (код страны, например IT).<br>
                            Заголовок допускается.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="searchTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Find Task by ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="/tasks">
                <div class="modal-body">
                    @foreach($statusFilter as $selected)
                        <input type="hidden" name="status[]" value="{{ $selected }}">
                    @endforeach
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 20 }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="search-task-id">Task ID</label>
                        <input type="number" min="1" class="form-control" id="search-task-id" name="task_id"
                               value="{{ old('task_id', $taskIdFilter) }}" required>
                        @error('task_search')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-green">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="statusFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter by Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($statuses->isEmpty())
                    <p class="text-muted mb-0">Status list is empty.</p>
                @else
                    <div class="status-filter-list">
                        @foreach($statuses as $status)
                            @php($statusId = 'status-option-' . \Illuminate\Support\Str::slug($status, '-'))
                            <div class="form-check">
                                <input class="form-check-input status-option"
                                       type="checkbox"
                                       value="{{ $status }}"
                                       id="{{ $statusId }}"
                                       @checked(in_array($status, $statusFilter, true))>
                                <label class="form-check-label" for="{{ $statusId }}">
                                    {{ $status }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="status-filter-clear">Clear</button>
                <button type="button" class="btn btn-green" id="status-filter-apply">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logs-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="logs-modal-status" class="text-muted small mb-3"></div>
                <ul id="logs-modal-list" class="list-group list-group-flush"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="accountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="account-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="account-modal-status" class="text-muted small mb-3"></div>
                <ul id="account-modal-list" class="list-group list-group-flush"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="domainPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <p class="text-muted mb-0 small">Task</p>
                    <h5 class="modal-title" id="domain-paid-title"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="domain-paid-status" class="text-muted small mb-3"></div>
                <ul class="list-group list-group-flush" id="domain-paid-list"></ul>
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
    const copyToClipboard = async (text) => {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
            } else {
                const temp = document.createElement('textarea');
                temp.value = text;
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                temp.remove();
            }
            window.showToast?.('Copied', 'success');
        } catch (error) {
            window.showToast?.('Failed to copy', 'error');
        }
    };

    const filterForm = document.getElementById('filter-form');
    const statusHiddenContainer = document.getElementById('status-hidden-inputs');
    const statusChipContainer = document.getElementById('selected-status-chips');
    const statusModalElement = document.getElementById('statusFilterModal');
    const statusModal = statusModalElement ? bootstrap.Modal.getOrCreateInstance(statusModalElement) : null;

    const getStatusCheckboxes = () => Array.from(statusModalElement?.querySelectorAll('.status-option') ?? []);

    let selectedStatuses = statusHiddenContainer
        ? Array.from(statusHiddenContainer.querySelectorAll('input[name="status[]"]')).map(input => input.value)
        : [];

    const renderStatusSelections = () => {
        if (!statusHiddenContainer || !statusChipContainer) {
            return;
        }

        statusHiddenContainer.innerHTML = '';
        selectedStatuses.forEach(value => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'status[]';
            hidden.value = value;
            statusHiddenContainer.appendChild(hidden);
        });

        statusChipContainer.innerHTML = '';
        if (!selectedStatuses.length) {
            const placeholder = document.createElement('span');
            placeholder.className = 'text-muted small';
            placeholder.textContent = 'All statuses';
            statusChipContainer.appendChild(placeholder);
            return;
        }

        selectedStatuses.forEach(value => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'btn btn-sm btn-outline-secondary status-chip';
            chip.dataset.status = value;
            chip.innerHTML = `${value} <span class="ms-1">&times;</span>`;
            statusChipContainer.appendChild(chip);
        });
    };

    const syncStatusCheckboxes = () => {
        getStatusCheckboxes().forEach(checkbox => {
            checkbox.checked = selectedStatuses.includes(checkbox.value);
        });
    };

    const submitFilters = () => {
        filterForm?.submit();
    };

    const applyButton = document.getElementById('status-filter-apply');
    if (applyButton) {
        applyButton.addEventListener('click', () => {
            selectedStatuses = getStatusCheckboxes()
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            renderStatusSelections();
            statusModal?.hide();
            submitFilters();
        });
    }

    const clearButton = document.getElementById('status-filter-clear');
    if (clearButton) {
        clearButton.addEventListener('click', () => {
            selectedStatuses = [];
            getStatusCheckboxes().forEach(checkbox => {
                checkbox.checked = false;
            });
            renderStatusSelections();
            statusModal?.hide();
            submitFilters();
        });
    }

    if (statusModalElement) {
        statusModalElement.addEventListener('show.bs.modal', syncStatusCheckboxes);
    }

    if (statusChipContainer) {
        statusChipContainer.addEventListener('click', (event) => {
            const chip = event.target.closest('.status-chip');
            if (!chip) {
                return;
            }
            const removedStatus = chip.dataset.status;
            selectedStatuses = selectedStatuses.filter(status => status !== removedStatus);
            syncStatusCheckboxes();
            renderStatusSelections();
            submitFilters();
        });
    }

    renderStatusSelections();

    const openModal = @json(session('open_modal'));
    if (openModal === 'task') {
        const taskModalElement = document.getElementById('addTaskModal');
        if (taskModalElement) {
            new bootstrap.Modal(taskModalElement).show();
        }
    } else if (openModal === 'batch') {
        const batchModalElement = document.getElementById('batchTaskModal');
        if (batchModalElement) {
            new bootstrap.Modal(batchModalElement).show();
        }
    } else if (openModal === 'search') {
        const searchModalElement = document.getElementById('searchTaskModal');
        if (searchModalElement) {
            new bootstrap.Modal(searchModalElement).show();
        }
    }

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(triggerEl => new bootstrap.Popover(triggerEl, {html: true, sanitize: false}));

    const logsModalElement = document.getElementById('logsModal');
    const bootstrapModal = new bootstrap.Modal(logsModalElement);
    const titleEl = document.getElementById('logs-modal-title');
    const statusEl = document.getElementById('logs-modal-status');
    const listEl = document.getElementById('logs-modal-list');

    const TYPE_BADGES = {
        success: { entry: 'log-entry-success', badge: 'log-badge-success' },
        error:   { entry: 'log-entry-error', badge: 'log-badge-error' },
        warning: { entry: 'log-entry-warning', badge: 'log-badge-warning' },
        info:    { entry: 'log-entry-info', badge: 'log-badge-info' },
        default: { entry: 'log-entry-default', badge: 'log-badge-default' },
    };

    const accountModalElement = document.getElementById('accountModal');
    const accountModal = new bootstrap.Modal(accountModalElement);
    const accountTitleEl = document.getElementById('account-modal-title');
    const accountStatusEl = document.getElementById('account-modal-status');
    const accountListEl = document.getElementById('account-modal-list');
    const domainModalElement = document.getElementById('domainPaidModal');
    const domainModal = new bootstrap.Modal(domainModalElement);
    const domainTitleEl = document.getElementById('domain-paid-title');
    const domainStatusEl = document.getElementById('domain-paid-status');
    const domainListEl = document.getElementById('domain-paid-list');

    document.querySelectorAll('[data-action="show-logs"]').forEach(button => {
        button.addEventListener('click', async () => {
            const taskId = button.getAttribute('data-task-id');
            const label = button.getAttribute('data-task-label') || `Task #${taskId}`;

            titleEl.textContent = label;
            statusEl.textContent = 'Loading logs...';
            listEl.innerHTML = '';
            bootstrapModal.show();

            try {
                const response = await fetch(`/tasks/${taskId}/logs`);
                if (!response.ok) throw new Error('Failed to load logs');
                const data = await response.json();

                const logs = data.logs || [];
                if (!logs.length) {
                    statusEl.textContent = 'No logs for this task yet.';
                    return;
                }

                statusEl.textContent = `${logs.length} log(s) found.`;
                logs.forEach(log => {
                    const li = document.createElement('li');
                    const typeKey = (log.type || '').toLowerCase();
                    const entryClass = TYPE_BADGES[typeKey]?.entry ?? TYPE_BADGES.default.entry;
                    const badgeClass = TYPE_BADGES[typeKey]?.badge ?? TYPE_BADGES.default.badge;

                    li.className = `log-entry ${entryClass}`;

                    const header = document.createElement('div');
                    header.className = 'log-entry-header';

                    const template = document.createElement('div');
                    template.className = 'log-entry-title';
                    template.textContent = log.template_name || 'Log entry';

                    const typeBadge = document.createElement('span');
                    typeBadge.className = `log-badge ${badgeClass}`;
                    typeBadge.textContent = (log.type ?? 'info').toLowerCase();

                    header.appendChild(template);
                    header.appendChild(typeBadge);

                    const meta = document.createElement('div');
                    meta.className = 'log-entry-meta';
                    if (log.created_at) {
                        const timeSpan = document.createElement('span');
                        timeSpan.textContent = log.created_at;
                        meta.appendChild(timeSpan);
                    }
                    if (log.error_id) {
                        const errorId = document.createElement('span');
                        const label = document.createElement('span');
                        label.className = 'log-error-id-label';
                        label.textContent = 'Error ID:';

                        const idValue = document.createElement('span');
                        idValue.className = 'log-error-id';
                        idValue.textContent = log.error_id;
                        idValue.addEventListener('click', () => copyToClipboard(log.error_id));

                        const errorWrap = document.createElement('span');
                        errorWrap.className = 'log-error-wrap';
                        errorWrap.appendChild(label);
                        errorWrap.appendChild(idValue);
                        meta.appendChild(errorWrap);
                    }

                    const body = document.createElement('p');
                    body.className = 'mb-0 text-body log-text';
                    body.textContent = log.text ?? '';

                    li.appendChild(header);
                    if (meta.childNodes.length) {
                        li.appendChild(meta);
                    }
                    if ((log.text ?? '').trim() !== '') {
                        li.appendChild(body);
                    }

                    listEl.appendChild(li);
                });
            } catch (error) {
                statusEl.textContent = error.message;
            }
        });
    });

    document.querySelectorAll('[data-action="show-account"]').forEach(button => {
        button.addEventListener('click', async () => {
            const taskId = button.getAttribute('data-task-id');
            const label = button.getAttribute('data-task-label') || `Task #${taskId}`;

            accountTitleEl.textContent = label;
            accountStatusEl.textContent = 'Loading account info...';
            accountListEl.innerHTML = '';
            accountModal.show();

            try {
                const response = await fetch(`/tasks/${taskId}/account`);
                if (!response.ok) throw new Error('Failed to load account info');
                const data = await response.json();

                const account = data.account || [];
                if (!account.length) {
                    accountStatusEl.textContent = 'No account details available.';
                    return;
                }

                accountStatusEl.textContent = 'Account details';
                account.forEach(row => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-start gap-3';

                    const labelEl = document.createElement('span');
                    labelEl.className = 'text-muted small';
                    labelEl.textContent = row.label;

                    const valueEl = document.createElement('span');
                    valueEl.className = 'fw-semibold text-break log-text';
                    valueEl.textContent = row.value ?? '—';

                    li.appendChild(labelEl);
                    li.appendChild(valueEl);
                    accountListEl.appendChild(li);
                });
            } catch (error) {
                accountStatusEl.textContent = error.message;
            }
        });
    });

    document.querySelectorAll('[data-action="show-domain-info"]').forEach(button => {
        button.addEventListener('click', () => {
            const paidDate = button.getAttribute('data-paid-date') || '—';
            const paidPrice = button.getAttribute('data-paid-price') || '—';
            const paidCurrency = button.getAttribute('data-paid-currency') || '';
            const paidCardFull = button.getAttribute('data-paid-card-full') || '';
            const paidCard = paidCardFull || '—';
            let maskedCard = paidCard;
            if (paidCard && paidCard.length >= 8) {
                maskedCard = `${paidCard.slice(0, 4)}••••••${paidCard.slice(-4)}`;
            }
            const label = button.getAttribute('data-task-label') || 'Domain';

            domainTitleEl.textContent = label;
            domainStatusEl.textContent = 'Domain payment details';

            domainListEl.innerHTML = `
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Дата покупки</span>
                    <span class="fw-semibold">${paidDate || '—'}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Стоимость</span>
                    <span class="fw-semibold">${paidPrice || '—'} ${paidCurrency}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Карта списания</span>
                    <span class="fw-semibold">
                        ${paidCard && paidCard !== '—'
                            ? `<span class="log-error-id domain-card-copy text-muted" data-full-card="${paidCardFull}" style="border-color: var(--brand-gray); color: var(--brand-gray);">${maskedCard}</span>`
                            : '—'}
                    </span>
                </li>`;

            domainModal.show();
        });
    });

    const bindCardCopy = () => {
        domainListEl.querySelectorAll('.domain-card-copy').forEach(el => {
            el.addEventListener('click', () => {
                const fullCard = el.dataset.fullCard || '';
                if (fullCard.trim() === '') {
                    window.showToast?.('Card number is empty', 'error');
                    return;
                }
                copyToClipboard(fullCard);
            }, { once: true });
        });
    };

    bindCardCopy();

    new MutationObserver(() => bindCardCopy()).observe(domainListEl, { childList: true });
});
</script>
@endpush

@endsection
