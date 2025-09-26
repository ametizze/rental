<div>
    <div class="container my-5">
        <h2 class="mb-4">{{ __('Maintenance Log Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ $logId ? __('Edit Maintenance Log') : __('New Maintenance Cost') }}
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Equipment') }}</label>
                            <select class="form-control" wire:model.defer="equipmentId">
                                <option value="">{{ __('Select Equipment') }}</option>
                                @foreach ($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }}
                                        ({{ $equipment->serial }})
                                    </option>
                                @endforeach
                            </select>
                            @error('equipmentId')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Cost') }}</label>
                            <input type="number" step="0.01" class="form-control" wire:model.defer="cost">
                            @error('cost')
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
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <input type="text" class="form-control" wire:model.defer="description">
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                    @if ($logId)
                        <button type="button" wire:click="resetForm"
                            class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('Maintenance History') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Equipment') }}</th>
                            <th>{{ __('Cost') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->equipment->name ?? 'N/A' }}</td>
                                <td>${{ number_format($log->cost, 2) }}</td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->date->format('m/d/Y') }}</td>
                                <td>
                                    <button wire:click="edit({{ $log->id }})"
                                        class="btn btn-sm btn-warning">{{ __('Edit') }}</button>
                                    <button wire:click="delete({{ $log->id }})"
                                        class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
