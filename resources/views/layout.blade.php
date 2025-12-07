<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Control Panel' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --brand-green: #00a989;
            --brand-blue: #1d5bae;
            --brand-red: #e74c3c;
            --brand-purple: #8e44ad;
            --brand-gray: #6c757d;
        }
        .text-brand-green { color: var(--brand-green) !important; }
        .text-brand-red { color: var(--brand-red) !important; }
        body {
            background-color: #f3f4f6;
            min-height: 100vh;
        }
        .nav-link.active {
            background-color: #2563eb !important;
            color: #fff !important;
        }
        .badge-status {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.55rem;
            border-radius: 0.35rem;
            color: #fff;
            white-space: nowrap;
            min-width: 120px;
            text-align: center;
        }
        .badge-status-orange { background-color: #f97316; }
        .badge-status-blue { background-color: var(--brand-blue); }
        .badge-status-green { background-color: var(--brand-green); }
        .badge-status-red { background-color: #dd2f23; }
        .badge-status-gray { background-color: var(--brand-gray); }
        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .table-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
            padding: 1.25rem;
        }
        .table-card .table {
            margin-bottom: 0;
        }
        .table-card .table thead {
            background-color: #f8fafc;
        }
        .table-card .table th {
            color: #475569;
            text-transform: uppercase;
            font-size: 0.77rem;
            letter-spacing: 0.03em;
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 90px));
            gap: 0.35rem;
            justify-content: center;
        }
        .actions-grid .btn {
            width: 100%;
        }
        .btn-placeholder {
            visibility: hidden;
        }
        .btn-green {
            color: #fff;
            background-color: var(--brand-green);
            border-color: var(--brand-green);
        }
        .btn-green:hover,
        .btn-green:focus {
            color: #fff;
            background-color: #009077;
            border-color: #009077;
        }
        .btn-outline-green {
            color: var(--brand-green);
            border-color: var(--brand-green);
        }
        .btn-outline-green:hover,
        .btn-outline-green:focus {
            color: #fff;
            background-color: var(--brand-green);
            border-color: var(--brand-green);
        }
        .custom-badge-blue {
            background-color: var(--brand-blue) !important;
            color: #fff !important;
        }
        .custom-badge-gray {
            background-color: var(--brand-gray) !important;
            color: #fff !important;
        }
        .log-text {
            white-space: pre-line;
            word-break: break-word;
            font-size: 0.8rem;
            font-family: 'Consolas', 'SFMono-Regular', Menlo, Monaco, 'Courier New', monospace;
        }
        .log-entry {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0.85rem 1rem;
            margin-bottom: 0.85rem;
            list-style: none;
        }
        .log-entry-header {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
        }
        .log-entry-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.92rem;
        }
        .log-entry-meta {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 0.2rem;
            margin-bottom: 0.7rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
        }
        .log-badge {
            font-size: 0.65rem;
            letter-spacing: 0.05em;
            text-transform: lowercase;
            border-radius: 999px;
            padding: 0.15rem 0.6rem;
            font-weight: 600;
            color: #fff;
        }
        .log-entry-success { background-color: #ecfdf5; }
        .log-entry-error { background-color: #fef2f2; }
        .log-entry-warning { background-color: #fffbeb; }
        .log-entry-info { background-color: #eff6ff; }
        .log-entry-default { background-color: #f8fafc; }
        .log-badge-success { background-color: var(--brand-green); }
        .log-badge-error { background-color: var(--brand-red); }
        .log-badge-warning { background-color: #facc15; }
        .log-badge-info { background-color: var(--brand-blue); }
        .log-badge-default { background-color: var(--brand-gray); }
        .log-error-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .log-error-id-label {
            font-size: 0.7rem;
            color: #475569;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        .log-error-id {
            border: 1px solid var(--brand-red);
            color: var(--brand-red);
            border-radius: 999px;
            padding: 0.15rem 0.65rem;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            user-select: all;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .log-error-id:hover {
            background-color: var(--brand-red);
            color: #fff;
        }
        .domain-card-copy {
            border: 1px solid var(--brand-gray);
            color: var(--brand-gray);
        }
        .domain-card-copy:hover {
            background-color: var(--brand-gray);
            color: #fff;
        }
        .toast-container {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 1080;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }
        .app-toast {
            min-width: 220px;
            background-color: #0f172a;
            color: #fff;
            padding: 0.65rem 1rem;
            border-radius: 0.65rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: auto;
            font-size: 0.85rem;
        }
        .app-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .app-toast-success { background-color: var(--brand-green); }
        .app-toast-error { background-color: var(--brand-red); }
        .app-toast-info { background-color: var(--brand-blue); }
        }
        .country-flag img {
            width: 24px;
            height: auto;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.15);
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="bg-white border-bottom shadow-sm mb-4">
        <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <a class="navbar-brand fw-semibold fs-4 text-decoration-none text-dark mb-0" href="/">Control Panel</a>
            <div class="d-flex flex-wrap gap-2">
                <a href="/tasks"
                   class="btn btn-sm {{ request()->is('tasks*') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">
                    Tasks
                </a>
                <a href="/proxies"
                   class="btn btn-sm {{ request()->is('proxies*') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">
                    Proxies
                </a>
                <a href="/cards"
                   class="btn btn-sm {{ request()->is('cards*') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">
                    Cards
                </a>
                <a href="/settings"
                   class="btn btn-sm {{ request()->is('settings*') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">
                    Settings
                </a>

                <form action="/logout" method="POST">
    @csrf
    <button class="text-red-600 hover:underline text-sm">Logout</button>
</form>
            </div>
        </div>
    </header>

    <div class="container pb-5">
        @yield('content')
    </div>

    <div class="toast-container" id="app-toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const container = document.getElementById('app-toast-container');

            window.showToast = function (message, type = 'info') {
                if (!container) return;

                const allowed = ['info', 'success', 'error'];
                const toastType = allowed.includes(type) ? type : 'info';

                const toast = document.createElement('div');
                toast.className = `app-toast app-toast-${toastType}`;
                toast.textContent = message;
                container.appendChild(toast);

                requestAnimationFrame(() => toast.classList.add('show'));

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            };
        })();
    </script>
    @stack('scripts')
</body>
</html>
