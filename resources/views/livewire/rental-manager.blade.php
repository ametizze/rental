<div>
    <div class="container mt-4">
        <h2 class="mb-4">{{ __('Rental Management') }}</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                {{ __('New Rental') }}
            </div>
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Customer') }}</label>
                            <select class="form-control" wire:model.defer="customer_id">
                                <option value="">{{ __('Select a customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" class="form-control" wire:model.live="start_date">
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" class="form-control" wire:model.live="end_date">
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">{{ __('Available Equipment') }}</label>
                            <div class="row g-3">
                                @foreach ($availableEquipment as $item)
                                    @php
                                        $isSelected = in_array($item->id, $selected_equipment);
                                    @endphp
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <div class="card h-100 position-relative cursor-pointer @if ($isSelected) border-success border-3 @endif"
                                            wire:click="toggleEquipmentSelection({{ $item->id }})">
                                            @if ($isSelected)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                    &#10003;
                                                </span>
                                            @endif
                                            <img src="{{ asset('storage/' . $item->photo) }}" class="card-img-top"
                                                alt="{{ $item->name }}" style="height: 150px; object-fit: cover;">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title">{{ $item->name }}</h5>
                                                <p class="card-text text-muted mb-auto">({{ $item->serial }})</p>
                                                <p class="card-text mt-2">
                                                    <strong>${{ number_format($item->daily_rate, 2) }}</strong> /
                                                    {{ __('day') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label class="form-label">{{ __('Start Photos') }}</label>
                            <input type="file" class="form-control" wire:model="start_photos" multiple>
                            @error('start_photos.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            @if ($start_photos)
                                <div class="mt-2 d-flex flex-wrap">
                                    @foreach ($start_photos as $photo)
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            style="max-height: 100px; margin-right: 10px;">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-md-12 mt-4">
                            <h4>{{ __('Total Amount') }}: ${{ number_format($total_amount, 2) }}</h4>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-4">{{ __('Create Rental') }}</button>
                    <button type="button" wire:click="resetForm"
                        class="btn btn-secondary mt-4">{{ __('Cancel') }}</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">{{ __('All Rentals') }}</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Start Date') }}</th>
                            <th>{{ __('End Date') }}</th>
                            <th>{{ __('Equipment') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rentals as $rental)
                            <tr>
                                <td>{{ $rental->customer->name }}</td>
                                <td>{{ $rental->start_date->format('d/m/Y') }}</td>
                                <td>{{ $rental->end_date->format('d/m/Y') }}</td>
                                <td>
                                    <ul>
                                        @foreach ($rental->equipment as $item)
                                            <li>{{ $item->name }} ({{ $item->serial }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>${{ number_format($rental->total_amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $rental->status == 'active' ? 'primary' : ($rental->status == 'completed' ? 'success' : 'danger') }}">
                                        {{ __(ucfirst($rental->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($rental->status == 'active')
                                        <button wire:click="openCompleteRentalModal({{ $rental->id }})"
                                            class="btn btn-sm btn-success">{{ __('Complete') }}</button>
                                    @endif
                                    <a href="{{ route('rentals.details', ['uuid' => $rental->uuid]) }}"
                                        class="btn btn-sm btn-info">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $rentals->links() }}
            </div>
        </div>
        @livewire('complete-rental-modal')
    </div>
</div>
