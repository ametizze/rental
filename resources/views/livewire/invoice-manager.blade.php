<div class="container mt-4">
    <div>
        <h2 class="mb-4">{{ __('Invoice Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">{{ __('New Invoice') }}</div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Customer') }}</label>
                            <select class="form-control" wire:model.live="customer_id">
                                <option value="">{{ __('Select a customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" class="form-control" wire:model.live="due_date">
                            @error('due_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <h4 class="mb-3">{{ __('Invoice Items') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Description') }}</th>
                                        <th style="width: 100px;">{{ __('Qty') }}</th>
                                        <th style="width: 100px;">{{ __('Rate') }}</th>
                                        <th style="width: 120px;">{{ __('Amount') }}</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoiceItems as $index => $item)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                    wire:model.live="invoiceItems.{{ $index }}.description">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                    wire:model.live="invoiceItems.{{ $index }}.quantity">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                    wire:model.live="invoiceItems.{{ $index }}.rate">
                                            </td>
                                            <td>${{ number_format((float) $item['quantity'] * (float) $item['rate'], 2) }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeItem({{ $index }})">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary"
                                wire:click="addItem">{{ __('Add Item') }}</button>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control" wire:model.defer="notes"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 bg-light">
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('Subtotal') }}:</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label mb-0">{{ __('Tax Rate') }}</label>
                                    <input type="number" step="0.0001"
                                        class="form-control form-control-sm w-25 text-end" wire:model.live="tax_rate">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('Tax Amount') }}:</span>
                                    <span>${{ number_format($tax_amount, 2) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>{{ __('Total') }}:</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save Invoice') }}</button>
                    <button type="button" wire:click="resetForm"
                        class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Invoices') }}</div>
            <div class="card-body">
                <div class="container mt-4">
                    <div>
                        <h2 class="mb-4">{{ __('Invoice Management') }}</h2>

                        <div class="card">
                            <div class="card-header bg-dark text-white">{{ __('All Invoices') }}</div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Invoice #') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Due Date') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoices as $invoice)
                                            <tr>
                                                <td>#{{ $invoice->id }}</td>
                                                <td>{{ $invoice->customer->name }}</td>
                                                <td>{{ $invoice->due_date->format('m/d/Y') }}</td>
                                                <td>${{ number_format($invoice->total, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partially_paid' ? 'warning' : 'danger') }}">
                                                        {{ __(ucfirst($invoice->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">

                                                        <a href="{{ route('invoices.show', ['invoice' => $invoice->uuid]) }}"
                                                            class="btn btn-sm btn-info"
                                                            title="{{ __('View Details') }}">
                                                            <i class="bi bi-eye"></i> {{ __('View') }}
                                                        </a>

                                                        <a href="{{ route('invoices.print', ['invoice' => $invoice->uuid]) }}"
                                                            target="_blank" class="btn btn-sm btn-dark"
                                                            title="{{ __('Print / PDF') }}">
                                                            <i class="bi bi-file-earmark-pdf"></i> {{ __('PDF') }}
                                                        </a>

                                                        @if ($invoice->status != 'paid')
                                                            <button wire:click="markAsPaid({{ $invoice->id }})"
                                                                wire:loading.attr="disabled"
                                                                wire:confirm="{{ __('Are you sure you want to do this?') }}"
                                                                class="btn btn-sm btn-success"
                                                                title="{{ __('Mark as Paid (Full)') }}">
                                                                <i class="bi bi-check-lg"></i>
                                                                {{ __('Mark Paid (Full)') }}
                                                            </button>
                                                        @endif

                                                        {{-- Botão de Edição (Opção para futuro) --}}
                                                        {{-- <button class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </button> --}}

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $invoices->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
