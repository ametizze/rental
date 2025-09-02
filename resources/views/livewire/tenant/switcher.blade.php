{{-- resources/views/livewire/tenant/switcher.blade.php --}}
<div class="dropdown">
    @php
        $current = collect($tenants)->firstWhere('id', $currentId);
    @endphp
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" type="button">
        {{ $current['name'] ?? 'Selecionar tenant' }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @forelse($tenants as $t)
            <li>
                <button class="dropdown-item @if ($t['id'] === $currentId) active @endif"
                    wire:click="useTenant({{ $t['id'] }})">
                    {{ $t['name'] }} <span class="text-muted small">({{ $t['slug'] }})</span>
                </button>
            </li>
        @empty
            <li><span class="dropdown-item-text text-muted">Nenhum tenant</span></li>
        @endforelse
    </ul>
</div>
