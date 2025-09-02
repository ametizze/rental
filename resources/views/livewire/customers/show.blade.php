<div class="col-lg-8">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Cliente</strong>
            <a class="btn btn-sm btn-primary" href="{{ route('customers.edit', $customer) }}">Editar</a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">{{ $customer->code }}</dd>
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9">{{ $customer->name }}</dd>
                <dt class="col-sm-3">E-mail</dt>
                <dd class="col-sm-9">{{ $customer->email ?? '—' }}</dd>
                <dt class="col-sm-3">Telefone</dt>
                <dd class="col-sm-9">{{ $customer->phone ?? '—' }}</dd>
                <dt class="col-sm-3">Alt. Telefone</dt>
                <dd class="col-sm-9">{{ $customer->alt_phone ?? '—' }}</dd>
                <dt class="col-sm-3">Notas</dt>
                <dd class="col-sm-9">{{ $customer->notes ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</div>
