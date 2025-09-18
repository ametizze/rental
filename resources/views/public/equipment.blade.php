<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $equipment->name }} - EasyRental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h3>{{ $equipment->name }} ({{ $equipment->serial }})</h3>
            </div>
            <div class="card-body text-center">
                @if ($equipment->photo)
                    <img src="{{ asset('storage/' . $equipment->photo) }}" class="img-fluid rounded-3"
                        style="max-height: 300px;">
                @endif
                <h4 class="mt-3">Status: <span
                        class="badge bg-{{ $equipment->status == 'available' ? 'success' : 'danger' }}">{{ ucfirst($equipment->status) }}</span>
                </h4>
                <p><strong>{{ __('Daily Rate') }}:</strong> ${{ number_format($equipment->daily_rate, 2) }}</p>
                <p><strong>{{ __('Tenant') }}:</strong> {{ $equipment->tenant->name }}</p>
                <hr>
                <p class="text-muted">{{ __('Scanned at') }} {{ now() }}</p>
            </div>
        </div>
    </div>
</body>

</html>
