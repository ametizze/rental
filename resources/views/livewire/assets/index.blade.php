<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 m-0">Equipamentos</h1>
        <a class="btn btn-primary" href="{{ route('assets.create') }}">Novo equipamento</a>
    </div>

    @if (!session('tenant_id'))
        <div class="alert alert-warning">Select a tenant to continue.</div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="search" class="form-control" placeholder="Search code/make/model/serial" wire:model.live="q">
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="category">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="status">
                <option value="">All statuses</option>
                <option value="available">available</option>
                <option value="rented">rented</option>
                <option value="maintenance">maintenance</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:72px;">Photo</th>
                    <th>Code</th>
                    <th>Make/Model</th>
                    <th>Serial</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $a)
                    <tr>
                        <td>
                            @php $thumb = $a->photos->first(); @endphp
                            @if ($thumb)
                                <img src="{{ asset('storage/' . $thumb->path) }}" class="rounded"
                                    style="width:64px;height:48px;object-fit:cover;">
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $a->code }}</td>
                        <td>{{ $a->make }} {{ $a->model }}</td>
                        <td>{{ $a->serial_number }}</td>
                        <td><span
                                class="badge text-bg-{{ $a->status === 'available' ? 'success' : ($a->status === 'rented' ? 'warning' : 'secondary') }}">
                                {{ $a->status }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('assets.show', $a) }}">View</a>
                            <a class="btn btn-sm btn-primary" href="{{ route('assets.edit', $a) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $assets->links() }}
</div>
