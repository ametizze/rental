<div class="modal fade" id="completeRentalModal" tabindex="-1" aria-labelledby="completeRentalModalLabel" aria-hidden="true"
    wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="completeRentalModalLabel">{{ __('Complete Rental') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    wire:click="close"></button>
            </div>
            <div class="modal-body">
                @if ($rental)
                    <p><strong>{{ __('Customer') }}:</strong> {{ $rental->customer->name }}</p>
                    <p><strong>{{ __('Start Date') }}:</strong> {{ $rental->start_date->format('d/m/Y') }}</p>
                    <p><strong>{{ __('End Date') }}:</strong> {{ $rental->end_date->format('d/m/Y') }}</p>

                    <h5 class="mt-4">{{ __('Equipment') }}:</h5>
                    <ul>
                        @foreach ($rental->equipment as $item)
                            <li>{{ $item->name }} ({{ $item->serial }}) - ${{ number_format($item->daily_rate, 2) }}
                                / {{ __('day') }}</li>
                        @endforeach
                    </ul>

                    <h5 class="mt-4">{{ __('Initial Photos') }}</h5>
                    @if (!empty($rental->start_photos))
                        <div class="d-flex flex-wrap">
                            @foreach ($rental->start_photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}"
                                    style="max-height: 150px; margin-right: 10px; border-radius: 5px;">
                            @endforeach
                        </div>
                    @else
                        <p>{{ __('No photos were taken at the start of the rental.') }}</p>
                    @endif

                    <hr>

                    <form wire:submit.prevent="completeRental">
                        <div class="mb-3">
                            <label for="endPhotos" class="form-label">{{ __('Return Photos') }}</label>
                            <input type="file" class="form-control" wire:model="end_photos" multiple>
                            @error('end_photos.*')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success">{{ __('Confirm Return') }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('completeRentalModal');
            const modal = new bootstrap.Modal(modalElement);

            Livewire.on('openCompleteRentalModal', (event) => {
                modal.show();
            });

            // Listen for the Bootstrap modal close event to close the Livewire component
            modalElement.addEventListener('hidden.bs.modal', (event) => {
                Livewire.dispatch('closeModal');
            });
        });
    </script>
@endpush
