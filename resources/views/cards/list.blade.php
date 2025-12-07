@extends('layout', ['title' => 'Cards'])

@section('content')

<div class="page-header">
    <h1 class="h3 mb-0">Cards</h1>
    <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#cardModal">
        <i class="fa-solid fa-credit-card me-1"></i> Add Card
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 small">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Holder</th>
                <th>Number</th>
                <th>Expiration</th>
                <th>Label</th>
                <th>Last Used</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($cards as $card)
                <tr>
                    <td>{{ $card->id }}</td>
                    <td>{{ $card->holder }}</td>
                    <td>{{ $card->number }}</td>
                    <td>{{ sprintf('%02d/%s', $card->exp_month, $card->exp_year) }}</td>
                    <td>{{ $card->label ?? '—' }}</td>
                    <td>{{ $card->card_last_used_at ? $card->card_last_used_at->format('Y-m-d H:i') : '—' }}</td>

                    <td>
                        <form action="/cards/{{ $card->id }}/delete" method="POST" onsubmit="return confirm('Delete this card?');">
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

<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/cards/store" autocomplete="off">
                @csrf
                <input type="hidden" name="form_context" value="inline">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Holder</label>
                        <input type="text" name="holder" class="form-control" value="{{ old('holder') }}" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Card Number</label>
                        <input type="text" name="number" class="form-control" value="{{ old('number') }}" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label fw-semibold">Month</label>
                            <input type="number" min="1" max="12" name="exp_month" class="form-control" value="{{ old('exp_month') }}" autocomplete="off" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="text" inputmode="numeric" pattern="\d{2,4}" name="exp_year" class="form-control" value="{{ old('exp_year') }}" placeholder="26 or 2026" autocomplete="off" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">CVV</label>
                            <input type="text" name="cvv" class="form-control" value="{{ old('cvv') }}" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-3">
                            <label class="form-label fw-semibold">Label / Bank</label>
                            <input type="text" name="label" class="form-control" value="{{ old('label') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green">
                        <i class="fa-solid fa-check me-1"></i> Save Card
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
