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

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select class="form-control" wire:model.live="type">
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="expense">{{ __('Expense') }}</option>
                                <option value="income">{{ __('Income') }}</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" class="form-control" wire:model.defer="amount">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="date">
                            @error('date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select class="form-control" wire:model.defer="status">
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option }}">{{ __(ucfirst($option)) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Category') }}</label>
                            <div class="input-group">
                                <select class="form-control" wire:model.defer="categoryId">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-info"
                                    wire:click="$dispatch('open-category-modal')">
                                    +
                                </button>
                            </div>
                            @error('categoryId')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Customer') }}</label>
                            <select class="form-control" wire:model.defer="customerId">
                                <option value="">{{ __('Select Customer (Optional)') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customerId')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" class="form-control" wire:model.defer="dueDate">
                            @error('dueDate')
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
                <div class="row mb-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('Search by description or customer...') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filterType">
                            <option value="">{{ __('Filter by Type') }}</option>
                            <option value="income">{{ __('Income') }}</option>
                            <option value="expense">{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" wire:model.live="filterStatus">
                            <option value="">{{ __('Filter by Status') }}</option>
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}">{{ __(ucfirst($option)) }}</option>
                            @endforeach
                            <option value="overdue">{{ __('Overdue') }}</option>
                        </select>
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Status') }}</th>
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
                                <td>{{ $transaction->category->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->date->format('m/d/Y') }}</td>
                                <td>{{ $transaction->due_date ? $transaction->due_date->format('m/d/Y') : '' }}</td>
                                <td>
                                    @php
                                        $status = $transaction->calculated_status;
                                        $color = match ($status) {
                                            'received', 'paid' => 'success',
                                            'overdue' => 'danger',
                                            'return', 'archived' => 'secondary',
                                            default => 'info', // Pending
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ __(ucfirst($status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $transaction->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>

                                    @if (
                                        $transaction->type === 'income' &&
                                            in_array($transaction->calculated_status, ['pending', 'overdue']) &&
                                            !$transaction->source_id)
                                        <button wire:click="markReceived({{ $transaction->id }})"
                                            class="btn btn-sm btn-success">{{ __('Mark Received') }}</button>
                                    @endif

                                    @if (!$transaction->source_id)
                                        <button wire:click="delete({{ $transaction->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this transaction?') }}"
                                            class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
    @livewire('category-modal')
</div>
