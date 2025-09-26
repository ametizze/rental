<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Stock Item Management') }}</h2>

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $itemId ? __('Edit Stock Item') : __('Create New Stock Item') }}
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Item Name') }}</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Reference Code (Barcode/SKU)') }}</label>
                            <input type="text" class="form-control" wire:model.defer="referenceCode">
                            @error('referenceCode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Unit Price') }}</label>
                            <input type="number" step="0.01" class="form-control" wire:model.defer="unitPrice">
                            @error('unitPrice')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Quantity in Stock') }}</label>
                            <input type="number" class="form-control" wire:model.defer="quantity">
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('Unit of Measure') }}</label>
                            <input type="text" class="form-control" wire:model.defer="unit"
                                placeholder="{{ __('e.g., box, liter, unit') }}">
                            @error('unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('Photo') }}</label>
                            <input type="file" class="form-control" wire:model="photo">

                            <div wire:loading wire:target="photo" class="mt-2 text-muted">
                                {{ __('Uploading photo...') }}
                            </div>
                            @error('photo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="img-fluid mt-2"
                                    style="max-height: 150px;">
                            @elseif ($existingPhoto)
                                <img src="{{ asset('storage/' . $existingPhoto) }}" class="img-fluid mt-2"
                                    style="max-height: 150px;">
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                    @if ($itemId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('Current Stock') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Ref') }}</th>
                            <th>{{ __('Photo') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Unit') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockItems as $item)
                            <tr>
                                <td>{{ $item->reference_code }}</td>
                                <td>
                                    @if ($item->photo_path)
                                        <img src="{{ asset('storage/' . $item->photo_path) }}" style="width: 40px;">
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $item->id }})"
                                        class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $stockItems->links() }}
            </div>
        </div>
    </div>
</div>
