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
                    <hr>

                    <form wire:submit.prevent="completeRental">
                        <h5 class="mb-3">{{ __('Confirm Return and Document Condition') }}</h5>

                        <div class="mb-3">
                            @foreach ($endPhotos as $index => $photoBlock)
                                <div class="card p-3 mb-3 border" wire:key="end-photo-{{ $index }}">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="removePhotoField({{ $index }})">
                                                {{ __('Remove') }}
                                            </button>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Photo') }} {{ $index + 1 }}</label>
                                            <input type="file" class="form-control"
                                                wire:model="endPhotos.{{ $index }}.photo" accept="image/*"
                                                capture="environment">
                                            @error('endPhotos.' . $index . '.photo')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror

                                            @if (isset($photoBlock['photo']))
                                                <img src="{{ $photoBlock['photo']->temporaryUrl() }}"
                                                    class="img-fluid mt-2" style="max-height: 100px;">
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Description/Label') }}</label>
                                            <input type="text" class="form-control"
                                                wire:model.defer="endPhotos.{{ $index }}.label"
                                                placeholder="{{ __('e.g., Return with scratch on handle') }}">
                                            @error('endPhotos.' . $index . '.label')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-secondary" wire:click="addPhotoField">
                                + {{ __('Add Photo') }}
                            </button>
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
