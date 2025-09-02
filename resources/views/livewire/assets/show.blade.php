<div class="col-lg-9">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Asset</strong>
            <a class="btn btn-sm btn-primary" href="{{ route('assets.edit', $asset) }}">Edit</a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Code</dt>
                <dd class="col-sm-9">{{ $asset->code }}</dd>
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ optional($asset->category)->name ?? '—' }}</dd>
                <dt class="col-sm-3">Make/Model</dt>
                <dd class="col-sm-9">{{ $asset->make }} {{ $asset->model }}</dd>
                <dt class="col-sm-3">Serial</dt>
                <dd class="col-sm-9">{{ $asset->serial_number ?? '—' }}</dd>
                <dt class="col-sm-3">Year</dt>
                <dd class="col-sm-9">{{ $asset->year ?? '—' }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><span
                        class="badge text-bg-{{ $asset->status === 'available' ? 'success' : ($asset->status === 'rented' ? 'warning' : 'secondary') }}">{{ $asset->status }}</span>
                </dd>
                <dt class="col-sm-3">Daily rate</dt>
                <dd class="col-sm-9">
                    {{ $asset->price_per_day !== null ? 'R$ ' . number_format($asset->price_per_day, 2, ',', '.') : '—' }}
                </dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $asset->description ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Photos</strong></div>
        <div class="card-body">
            @php $photos = $asset->photos()->orderByDesc('id')->get(); @endphp
            @if ($photos->isEmpty())
                <div class="text-muted">No photos.</div>
            @else
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($photos as $p)
                        <img src="{{ asset('storage/' . $p->path) }}" class="rounded border"
                            style="width:160px;height:120px;object-fit:cover;">
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
