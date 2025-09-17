<div class="container mt-5">
    <div>
        <h2 class="mb-4">Manage Tenants</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $tenantId ? 'Edit Tenant' : 'Create New Tenant' }}
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                wire:model="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="domain" class="form-label">Domain Slug</label>
                            <input type="text" class="form-control @error('domain') is-invalid @enderror"
                                wire:model="domain">
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                        </div>
                        <div class="col-12">
                            <label for="is_active" class="form-label">Is Active?</label>
                            <input type="checkbox" wire:model="is_active">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Save</button>
                        @if ($tenantId)
                            <button type="button" wire:click="resetForm" class="btn btn-secondary">Cancel</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">
                All Tenants
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Domain</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenants as $tenant)
                            <tr>
                                <td>{{ $tenant->id }}</td>
                                <td>{{ $tenant->name }}</td>
                                <td>{{ $tenant->domain }}</td>
                                <td>
                                    <span class="badge bg-{{ $tenant->is_active ? 'success' : 'danger' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $tenant->id }})"
                                        class="btn btn-sm btn-warning">Edit</button>
                                    <button wire:click="delete({{ $tenant->id }})"
                                        class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
