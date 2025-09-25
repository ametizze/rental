<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Transaction Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $transactionId ? __('Edit Transaction') : __('New Transaction') }}
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select class="form-control" wire:model.defer="type">
                                <option value="expense">{{ __('Expense') }}</option>
                                <option value="income">{{ __('Income') }}</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" class="form-control" wire:model.defer="amount">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="date">
                            @error('date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <input type="text" class="form-control" wire:model.defer="description">
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                    @if ($transactionId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Transactions') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">
                                        {{ __(ucfirst($transaction->type)) }}
                                    </span>
                                </td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->date }}</td>
                                <td>
                                    <button wire:click="edit({{ $transaction->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $transaction->id }})"
                                        class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
