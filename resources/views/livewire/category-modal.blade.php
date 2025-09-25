<div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="categoryModalLabel">
                    {{ $categoryId ? __('Edit Category') : __('Create New Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    wire:click="close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="save">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" class="form-control" wire:model.defer="name">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select class="form-control" wire:model.defer="type">
                            <option value="expense">{{ __('Expense') }}</option>
                            <option value="income">{{ __('Income') }}</option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    <button type="button" wire:click="close" data-bs-dismiss="modal"
                        class="btn btn-secondary">{{ __('Cancel') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('categoryModal');
            const modal = new bootstrap.Modal(modalElement);

            // 1. Ouvir o evento do componente pai (disparado pelo botão '+')
            Livewire.on('open-category-modal', () => {
                modal.show();
            });

            // 2. Ouvir o evento de sucesso para fechar o modal
            Livewire.on('close-category-modal', () => {
                modal.hide();
            });

            // 3. (OPCIONAL) Disparar evento Livewire quando o modal é fechado pelo botão 'x' do Bootstrap
            modalElement.addEventListener('hidden.bs.modal', (event) => {
                // Chamamos o método close() do Livewire para resetar o estado interno, se necessário
                // @this.close() ou Livewire.dispatch('closeCategoryModal');
            });
        });
    </script>
@endpush
