<div class="container mt-4">
    <div>
        <h2 class="mb-4">{{ __('Customer Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $customerId ? __('Edit Customer') : __('New Customer') }}
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" wire:model.defer="email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" wire:model.defer="phone">
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                    @if ($customerId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Customers') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>
                                    <button wire:click="edit({{ $customer->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $customer->id }})"
                                        class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
