<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 m-0">Clientes</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Novo cliente</a>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="search" class="form-control" placeholder="Buscar por nome, código ou e-mail" wire:model.live="q">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td class="text-nowrap">{{ $c->code }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->email }}</td>
                        <td>{{ $c->phone }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.show', $c) }}">Ver</a>
                            <a class="btn btn-sm btn-primary" href="{{ route('customers.edit', $c) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">Nenhum cliente encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
</div>
