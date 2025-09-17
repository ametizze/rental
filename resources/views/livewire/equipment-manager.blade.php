<div>
    <div class="container mt-4">
        <h2 class="mb-4">{{ __('Equipment Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $equipmentId ? __('Edit Equipment') : __('New Equipment') }}
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
                            <label class="form-label">{{ __('Serial') }}</label>
                            <input type="text" class="form-control" wire:model.defer="serial">
                            @error('serial')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Daily Rate') }}</label>
                            <input type="text" class="form-control" wire:model.defer="daily_rate">
                            @error('daily_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Category') }}</label>
                            <input type="text" class="form-control" wire:model.defer="category">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select class="form-control" wire:model.defer="status">
                                <option value="available">{{ __('Available') }}</option>
                                <option value="rented">{{ __('Rented') }}</option>
                                <option value="maintenance">{{ __('Maintenance') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Photo') }}</label>
                            <input type="file" class="form-control" wire:model="photo">
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
                    @if ($equipmentId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Equipment') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Photo') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Serial') }}</th>
                            <th>{{ __('Daily Rate') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment as $item)
                            <tr>
                                <td>
                                    @if ($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}" style="width: 50px;">
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->serial }}</td>
                                <td>${{ number_format($item->daily_rate, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status == 'available' ? 'success' : 'danger' }}">
                                        {{ __(ucfirst($item->status)) }}
                                    </span>
                                </td>
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
            </div>
        </div>
    </div>

</div>
