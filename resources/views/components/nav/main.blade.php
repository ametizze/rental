{{-- Bootstrap 5 navbar with language & tenant controls --}}
@php
    // Safe fallback: if $items wasn't injected by the class, build defaults here.
$navItems = $items ?? [
    ['label' => __('messages.dashboard'), 'route' => 'home', 'active' => request()->routeIs('home')],
    [
        'label' => __('messages.customers'),
        'route' => 'customers.index',
        'active' => request()->routeIs('customers.*'),
    ],
    ['label' => __('messages.assets'), 'route' => 'assets.index', 'active' => request()->routeIs('assets.*')],
    ['label' => __('messages.rentals'), 'route' => 'rentals.index', 'active' => request()->routeIs('rentals.*')],
    ];
@endphp

<nav class="navbar border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ __('messages.brand') }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav"
            aria-controls="topnav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="topnav" class="collapse navbar-collapse" wire:ignore.self>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @foreach ($navItems as $item)
                    @continue(!Route::has($item['route'] ?? ''))
                    <li class="nav-item">
                        <a class="nav-link @if (!empty($item['active'])) active @endif"
                            href="{{ route($item['route']) }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="d-flex align-items-center gap-2">
                {{-- Language switcher --}}
                @php
                    $current = app()->getLocale(); // 'en', 'pt_BR', 'es'
                    $langLabel = match ($current) {
                        'pt_BR' => __('messages.portuguese'),
                        'es' => __('messages.spanish'),
                        default => __('messages.english'),
                    };
                @endphp
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        type="button">
                        {{ __('messages.language') }}: {{ $langLabel }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">{{ __('messages.english') }}</a>
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'pt_BR']) }}">{{ __('messages.portuguese') }}</a>
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}">{{ __('messages.spanish') }}</a>
                        </li>
                    </ul>
                </div>

                {{-- Tenant switcher (only if class exists and the class said to show it) --}}
                @if (($showSwitcher ?? false) && class_exists(\App\Livewire\Tenant\Switcher::class))
                    <livewire:tenant.switcher />
                @endif

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                            type="button">
                            {{ auth()->user()->name ?? __('messages.user') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted">ID: {{ auth()->id() }}</span></li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
