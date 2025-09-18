<div>
    <div class="container mt-4">
        <h2 class="mb-4">{{ __('Customer Balance Report') }}</h2>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Select a customer') }}</label>
                        <select class="form-control" wire:model.live="customerId">
                            <option value="">{{ __('All Customers') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                @if ($customerId)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>{{ __('Unpaid Invoices for') }}: {{ $unpaidInvoices->first()->customer->name ?? '' }}
                        </h5>
                        <h4>{{ __('Total Balance') }}: ${{ number_format($totalBalance, 2) }}</h4>
                    </div>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Invoice #') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unpaidInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                                    <td>${{ number_format($invoice->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice->id) }}"
                                            class="btn btn-sm btn-info">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">{{ __('Please select a customer to view their balance.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
