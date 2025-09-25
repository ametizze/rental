<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Transaction Category Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $categoryId ? __('Edit Category') : __('Create New Category') }}
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">{{ __('Category Name') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
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
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-success w-100">{{ __('Save') }}</button>
                            @if ($categoryId)
                                <button type="button" wire:click="resetForm"
                                    class="btn btn-secondary w-100 mt-2">{{ __('Cancel') }}</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('Existing Categories') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $category->type == 'income' ? 'success' : 'danger' }}">
                                        {{ __(ucfirst($category->type)) }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $category->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $category->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this category?') }}"
                                        class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
