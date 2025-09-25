<div class="container mt-5">
    <div class="row mt-4">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-end">
            <p><strong>{{ __('Subtotal') }}:</strong> ${{ number_format($invoice->subtotal, 2) }}</p>
            <p><strong>{{ __('Tax') }} ({{ $invoice->tax_rate * 100 }}%):</strong>
                ${{ number_format($invoice->tax_amount, 2) }}</p>
            <h4 class="mt-3"><strong>{{ __('Total') }}:</strong> ${{ number_format($invoice->total, 2) }}</h4>
            <hr>
            <p class="text-success"><strong>{{ __('Paid Amount') }}:</strong>
                ${{ number_format($invoice->paid_amount, 2) }}</p>
            <p class="text-danger"><strong>{{ __('Balance Due') }}:</strong>
                ${{ number_format($invoice->total - $invoice->paid_amount, 2) }}</p>
            <p class="text-muted">
                {{ __('Status') }}:
                <span
                    class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partially_paid' ? 'warning' : 'danger') }}">
                    {{ __(ucfirst($invoice->status)) }}
                </span>
            </p>

            @if ($invoice->status != 'paid')
                <div class="card mt-4 text-start">
                    <div class="card-header bg-warning text-dark">{{ __('Add Payment') }}</div>
                    <div class="card-body">
                        <form wire:submit.prevent="addPayment">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Amount') }}</label>
                                <input type="number" step="0.01" class="form-control"
                                    wire:model.defer="newPaymentAmount" min="0.01"
                                    max="{{ $invoice->total - $invoice->paid_amount }}">
                                @error('newPaymentAmount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Payment Date') }}</label>
                                <input type="date" class="form-control" wire:model.defer="newPaymentDate">
                                @error('newPaymentDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Notes') }}</label>
                                <textarea class="form-control" wire:model.defer="newPaymentNotes"></textarea>
                                @error('newPaymentNotes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Save Payment') }}</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">{{ __('Payment History') }}</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($invoice->payments->isEmpty())
                <p class="text-muted">{{ __('No payments recorded yet.') }}</p>
            @endif
        </div>
    </div>
</div>
