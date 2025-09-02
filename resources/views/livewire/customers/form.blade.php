<div class="col-lg-8">
    <div class="card">
        <div class="card-header">
            <strong>{{ $customerId ? 'Editar cliente' : 'Novo cliente' }}</strong>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código *</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                        wire:model.defer="code">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nome *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        wire:model.defer="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        wire:model.defer="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" wire:model.defer="phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tel. alternativo</label>
                    <input type="text" class="form-control" wire:model.defer="alt_phone">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea class="form-control" rows="3" wire:model.defer="notes"></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
