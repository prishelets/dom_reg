@extends('layout')

@section('content')

<h1 class="text-2xl font-semibold mb-4">Tasks</h1>

<a href="/tasks/create" 
   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">
    ➕ Add Task
</a>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="min-w-full border border-gray-300 text-sm">
    <thead class="bg-gray-100">
        <tr>
            
            <th class="border px-3 py-2 text-left">Domain</th>
            <th class="border px-3 py-2 text-left">Registrar</th>
            <th class="border px-3 py-2 text-left">Country</th>
            <th class="border px-3 py-2 text-left">Brand</th>
            <th class="border px-3 py-2 text-left">Status</th>
            <th class="border px-3 py-2 text-left">Email Login</th>
            <th class="border px-3 py-2 text-left">Proxy</th>
            <th class="border px-3 py-2 text-left">CF Email</th>
            <th class="border px-3 py-2 text-left">Domain Paid</th>
            <th class="border px-3 py-2 text-left">NS Servers</th>
            <th class="border px-3 py-2 text-left">Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($tasks as $task)
        <tr class="odd:bg-white even:bg-gray-50">
            
            <td class="border px-3 py-2">{{ $task->domain }}</td>
            <td class="border px-3 py-2">{{ $task->registrar }}</td>
            <td class="border px-3 py-2">{{ $task->country }}</td>
            <td class="border px-3 py-2">{{ $task->brand }}</td>

            <td class="border px-3 py-2">
                
                <span @class([
                    'inline-block text-xs font-semibold px-2 py-1 rounded-md text-white',
                    $task->statusColorClass(),
                ])>
                    {{ $task->status }}
                </span>
            </td>

            <td class="border px-3 py-2">{{ $task->email_login }}</td>
            <td class="border px-3 py-2">{{ $task->proxy }}</td>
            <td class="border px-3 py-2">{{ $task->cloudflare_email }}</td>
            <td class="border px-3 py-2">{{ $task->domain_paid ? 'Yes' : 'No' }}</td>
            <td class="border px-3 py-2">{{ $task->ns_servers }}</td>

            <td class="border px-3 py-2 flex gap-2">

                <form method="POST" action="/tasks/{{ $task->id }}/delete"
                      onsubmit="return confirm('Delete this task?');">
                    @csrf
                    <button class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>

                <a href="https://{{ $task->domain }}" target="_blank"
                   class="bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                    Open
                </a>

                @if($task->logs_exists)
                    <button type="button"
                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded hover:bg-gray-300"
                            data-action="show-logs"
                            data-task-id="{{ $task->id }}"
                            data-task-label="{{ $task->domain }}">
                        Logs
                    </button>
                @endif

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id="logs-modal" class="fixed inset-0 z-50 hidden">
    <div id="logs-modal-overlay" class="absolute inset-0 bg-black/40"></div>

    <div id="logs-modal-panel"
         class="absolute right-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300">

        <div class="border-b px-5 py-4 flex items-center justify-between bg-gray-50">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Task</p>
                <p id="logs-modal-title" class="text-lg font-semibold text-gray-900"></p>
            </div>
            <button type="button" id="logs-modal-close"
                    class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
        </div>

        <div class="p-5 overflow-y-auto flex-1 space-y-3 bg-white">
            <div id="logs-modal-status" class="text-sm text-gray-500"></div>
            <ul id="logs-modal-list" class="space-y-3 text-sm"></ul>
        </div>

        <div class="border-t px-5 py-3 bg-gray-50 text-right">
            <button type="button" id="logs-modal-close-bottom"
                    class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('logs-modal');
    const panel = document.getElementById('logs-modal-panel');
    const overlay = document.getElementById('logs-modal-overlay');
    const titleEl = document.getElementById('logs-modal-title');
    const statusEl = document.getElementById('logs-modal-status');
    const listEl = document.getElementById('logs-modal-list');

    const closeModal = () => {
        panel.classList.add('translate-x-full');
        const handler = () => {
            modal.classList.add('hidden');
            listEl.innerHTML = '';
            statusEl.textContent = '';
            panel.removeEventListener('transitionend', handler);
        };
        panel.addEventListener('transitionend', handler);
    };

    document.querySelectorAll('#logs-modal-close, #logs-modal-close-bottom').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    overlay.addEventListener('click', closeModal);

    const TYPE_STYLES = {
        info:   { accent: 'border-l-blue-500',  badge: 'bg-blue-50 text-blue-700 border-blue-200' },
        warning:{ accent: 'border-l-amber-500', badge: 'bg-amber-50 text-amber-700 border-amber-200' },
        error:  { accent: 'border-l-red-500',   badge: 'bg-red-50 text-red-700 border-red-200' },
        default:{ accent: 'border-l-gray-400',  badge: 'bg-gray-50 text-gray-700 border-gray-200' },
    };

    document.querySelectorAll('[data-action="show-logs"]').forEach(button => {
        button.addEventListener('click', async () => {
            const taskId = button.getAttribute('data-task-id');
            const label = button.getAttribute('data-task-label') || `Task #${taskId}`;

            titleEl.textContent = label;
            statusEl.textContent = 'Loading logs...';
            listEl.innerHTML = '';
            modal.classList.remove('hidden');
            requestAnimationFrame(() => panel.classList.remove('translate-x-full'));

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
                    const typeKey = (log.type || '').toLowerCase();
                    const styles = TYPE_STYLES[typeKey] ?? TYPE_STYLES.default;

                    const item = document.createElement('li');
                    item.className = `border border-gray-200 rounded shadow-sm bg-white p-3 border-l-4 ${styles.accent}`;

                    const header = document.createElement('div');
                    header.className = 'flex flex-wrap items-center justify-between gap-2 mb-2';

                    const time = document.createElement('span');
                    time.className = 'text-xs text-gray-500';
                    time.textContent = log.created_at ?? '—';

                    const badges = document.createElement('div');
                    badges.className = 'flex flex-wrap gap-2 text-[11px] font-semibold';

                    const typeBadge = document.createElement('span');
                    typeBadge.className = `inline-flex items-center px-2 py-0.5 rounded-full border ${styles.badge}`;
                    typeBadge.textContent = (log.type ?? 'info').toUpperCase();

                    const modeBadge = document.createElement('span');
                    modeBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full border border-gray-200 text-gray-600';
                    modeBadge.textContent = log.mode ?? '—';

                    badges.appendChild(typeBadge);
                    badges.appendChild(modeBadge);

                    header.appendChild(time);
                    header.appendChild(badges);

                    const text = document.createElement('p');
                    text.className = 'text-sm text-gray-800 whitespace-pre-wrap leading-relaxed';
                    text.textContent = log.text ?? '';

                    item.appendChild(header);
                    item.appendChild(text);
                    listEl.appendChild(item);
                });
            } catch (error) {
                statusEl.textContent = error.message;
            }
        });
    });
});
</script>
@endpush

@endsection
