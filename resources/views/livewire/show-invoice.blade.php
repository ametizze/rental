<div class="container mt-5">
    <div class="row">
        <div class="col-6">
            <h1>{{ __('Invoice') }}</h1>
            <p><strong>{{ __('Invoice Number') }}:</strong> {{ $this->invoice->uuid }}</p>
            <p><strong>{{ __('Invoice Date') }}:</strong> {{ $this->invoice->created_at->format('d/m/Y') }}</p>
        </div>
        <div class="col-6 text-end">
            <h4>{{ $this->invoice->tenant->name }}</h4>
            <p>{{ $this->invoice->tenant->address }}</p>
            <p>{{ $this->invoice->tenant->city }}, {{ $this->invoice->tenant->state }}
                {{ $this->invoice->tenant->zipcode }}</p>
            <p>Tel: {{ $this->invoice->tenant->phone }}</p>
        </div>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-6">
            <h5>{{ __('Bill To') }}:</h5>
            <p><strong>{{ __('Customer') }}:</strong> {{ $this->invoice->customer->name }}</p>
            <p><strong>{{ __('Address') }}:</strong> {{ $this->invoice->customer->address ?? 'N/A' }}</p>
            <p><strong>{{ __('Email') }}:</strong> {{ $this->invoice->customer->email }}</p>
        </div>
    </div>

    <hr>

    <h5 class="mt-4">{{ __('Rental Details') }}:</h5>
    @if ($this->invoice->rental)
        <p><strong>{{ __('Start Date') }}:</strong> {{ $this->invoice->rental->start_date->format('d/m/Y') }}</p>
        <p><strong>{{ __('End Date') }}:</strong> {{ $this->invoice->rental->end_date->format('d/m/Y') }}</p>
        <p><strong>{{ __('Equipment') }}:</strong></p>
        <ul>
            @foreach ($this->invoice->rental->equipment as $item)
                <li>{{ $item->name }} ({{ $item->serial }}) - ${{ number_format($item->daily_rate, 2) }} /
                    {{ __('day') }}</li>
            @endforeach
        </ul>
    @else
        <p class="text-muted">{{ __('This invoice is not linked to a rental.') }}</p>
    @endif

    <hr>

    <h5 class="mt-4">{{ __('Invoice Items') }}:</h5>
    @if (!empty($this->invoice->items))
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Rate') }}</th>
                    <th>{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->rate, 2) }}</td>
                        <td>${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">{{ __('No items found for this invoice.') }}</p>
    @endif

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">{{ __('Condition Photos') }}</div>
                <div class="card-body">
                    <p class="mb-2"><strong>{{ __('Start of Rental') }}:</strong></p>
                    <div class="d-flex flex-wrap mb-4">
                        @if ($this->invoice->rental && !empty($this->invoice->rental->start_photos))
                            @foreach ($this->invoice->rental->start_photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}"
                                    style="max-height: 100px; margin-right: 5px;">
                            @endforeach
                        @else
                            <p>{{ __('No photos were taken.') }}</p>
                        @endif
                    </div>
                    <p class="mb-2"><strong>{{ __('End of Rental') }}:</strong></p>
                    <div class="d-flex flex-wrap">
                        @if ($this->invoice->rental && !empty($this->invoice->rental->end_photos))
                            @foreach ($this->invoice->rental->end_photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}"
                                    style="max-height: 100px; margin-right: 5px;">
                            @endforeach
                        @else
                            <p>{{ __('No photos were taken.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <p><strong>{{ __('Subtotal') }}:</strong> ${{ number_format($this->invoice->subtotal, 2) }}</p>
            <p><strong>{{ __('Tax') }} ({{ $this->invoice->tax_rate * 100 }}%):</strong>
                ${{ number_format($this->invoice->tax_amount, 2) }}</p>
            <h4 class="mt-3"><strong>{{ __('Total') }}:</strong> ${{ number_format($this->invoice->total, 2) }}
            </h4>
            <p class="text-muted">{{ __('Status') }}: {{ __(ucfirst($this->invoice->status)) }}</p>
            <a href="{{ route('invoices.print', ['invoice' => $this->invoice->uuid]) }}" class="btn btn-primary mt-3"
                target="_blank">{{ __('Print') }}</a>
        </div>
    </div>
</div>
