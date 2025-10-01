<div class="container my-5">
    <h2 class="mb-4">{{ __('Quick Rent Module') }}</h2>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">{{ __('New Quick Rental') }}</div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form wire:submit.prevent="quickRent">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Equipment Reference Code') }} (#21, #45, etc.)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="equipmentRef"
                                placeholder="{{ __('Enter code and press lookup...') }}">
                            <button type="button" class="btn btn-outline-secondary"
                                wire:click="lookupEquipment">{{ __('Lookup') }}</button>
                        </div>
                        @error('equipmentRef')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Select Customer') }}</label>
                        <select class="form-control" wire:model.defer="customerId">
                            <option value="">{{ __('Select Customer') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customerId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($equipmentDetail)
                    <div class="alert alert-info mt-4">
                        <p class="mb-1"><strong>{{ __('Confirmation') }}:</strong></p>
                        <p class="mb-0">{{ $equipmentDetail->name }} ({{ $equipmentDetail->serial }})</p>
                        <p class="mb-0 text-success">
                            {{ __('Daily Rate') }}: ${{ number_format($equipmentDetail->daily_rate, 2) }}
                        </p>
                    </div>
                @elseif ($equipmentRef && !$equipmentDetail)
                    <div class="alert alert-warning mt-4">{{ __('No available equipment found with this code.') }}
                    </div>
                @endif


                <button type="submit" class="btn btn-success mt-4 w-100"
                    @if (!$equipmentDetail) disabled @endif>
                    {{ __('Mark as Rented Now') }}
                </button>
            </form>
        </div>
    </div>
</div>
