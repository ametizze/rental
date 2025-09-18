<div class="container mt-5">
    <div>
        <h2 class="mb-4">{{ __('Manage Users') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $userId ? __('Edit User') : __('Create New User') }}
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" wire:model.defer="email">
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control" wire:model.defer="password">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control" wire:model.defer="password_confirmation">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">{{ __('Role') }}</label>
                            <select class="form-control" wire:model.defer="role">
                                <option value="">{{ __('Select Role') }}</option>
                                @foreach ($roles as $key => $name)
                                    <option value="{{ $key }}">{{ __($name) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (auth()->user()->role === 'superadmin')
                            <div class="col-md-6">
                                <label for="tenant" class="form-label">{{ __('Tenant') }}</label>
                                <select class="form-control" wire:model.defer="selectedTenantId">
                                    <option value="">{{ __('Select Tenant') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedTenantId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                    @if ($userId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Users') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Tenant') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->tenant->name ?? 'N/A' }}</td>
                                <td>
                                    <button wire:click="edit({{ $user->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $user->id }})"
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
