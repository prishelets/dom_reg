<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accounts List</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

    <div class="container">

        <h2 class="mb-4">Accounts</h2>

        {{-- Флеш сообщения --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Login</th>
                    <th>Password</th>
                    <th>Created</th>
                    <th width="90">Actions</th>
                </tr>
            </thead>
            <tbody>

                @forelse($accounts as $acc)
                    <tr>
                        <td>{{ $acc->id }}</td>
                        <td>{{ $acc->email }}</td>
                        <td>{{ $acc->login }}</td>
                        <td>{{ $acc->password }}</td>
                        <td>{{ $acc->created_at }}</td>

                        <td>
                            <form action="/accounts/{{ $acc->id }}" method="POST" onsubmit="return confirm('Delete?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No accounts yet</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

</body>
</html>
