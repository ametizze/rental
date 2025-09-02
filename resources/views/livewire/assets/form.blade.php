<div class="col-lg-9">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $assetId ? 'Edit asset' : 'New asset' }}</strong>
            <div class="d-flex gap-2">
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @error('tenant')
                <div class="alert alert-warning">{{ $message }}</div>
            @enderror

            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Code *</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                        wire:model.defer="code">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label">Category</label>
                    <select class="form-select" wire:model.defer="category_id">
                        <option value="">—</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">New category</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('newCategory') is-invalid @enderror"
                            wire:model.defer="newCategory" placeholder="e.g. Compactor">
                        <button type="button" class="btn btn-outline-secondary" wire:click="saveCategory">Add</button>
                    </div>
                    @error('newCategory')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Make</label>
                    <input type="text" class="form-control" wire:model.defer="make">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Model</label>
                    <input type="text" class="form-control" wire:model.defer="model">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Serial</label>
                    <input type="text" class="form-control" wire:model.defer="serial_number">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <input type="number" class="form-control" wire:model.defer="year">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status *</label>
                    <select class="form-select" wire:model.defer="status">
                        <option value="available">available</option>
                        <option value="rented">rented</option>
                        <option value="maintenance">maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Daily rate</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" step="0.01" class="form-control" wire:model.defer="price_per_day">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" wire:model.defer="description"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Photos</label>
                    <input type="file" class="form-control" wire:model="photos" multiple
                        accept=".jpg,.jpeg,.png,.webp">
                    @error('photos.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                    {{-- existing photos --}}
                    @if ($existingPhotos)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($existingPhotos as $p)
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $p['path']) }}" class="rounded border"
                                        style="width:120px;height:90px;object-fit:cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                        wire:click="removePhoto({{ $p['id'] }})">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
