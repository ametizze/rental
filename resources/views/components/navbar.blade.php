@php
    // Safely detect routes so the navbar doesn't break when modules are missing
$hasHome = \Illuminate\Support\Facades\Route::has('home');
$hasCustomersIdx = \Illuminate\Support\Facades\Route::has('customers.index');
$hasCustomersNew = \Illuminate\Support\Facades\Route::has('customers.create');
$hasAssetsIdx = \Illuminate\Support\Facades\Route::has('assets.index');
$hasAssetsNew = \Illuminate\Support\Facades\Route::has('assets.create');
$hasRentalsIdx = \Illuminate\Support\Facades\Route::has('rentals.index');
$hasRentalsNew = \Illuminate\Support\Facades\Route::has('rentals.create');

// Active helpers by route name prefix
$routeName = request()->route()?->getName() ?? '';
$isHome = str_starts_with($routeName, 'home');
$isCust = str_starts_with($routeName, 'customers.');
$isAsset = str_starts_with($routeName, 'assets.');
$isRent = str_starts_with($routeName, 'rentals.');

// Language label
$currentLocale = app()->getLocale(); // 'en', 'pt_BR', 'es'
$langLabel = match ($currentLocale) {
    'pt_BR' => 'Portuguese (Brazil)',
    'es' => 'Spanish',
    default => 'English',
    };
@endphp

{{-- Simple, collapsible navbar with dropdowns --}}
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ $hasHome ? route('home') : url('/') }}">EasyRental</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav"
            aria-controls="topnav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Keep Livewire from re-morphing collapse wrapper during animation --}}
        <div id="topnav" class="navbar-collapse" wire:ignore.self>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- Dashboard dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isHome ? 'active' : '' }}" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Dashboard
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            @if ($hasHome)
                                <a class="dropdown-item" href="{{ route('home') }}">Home</a>
                            @else
                                <span class="dropdown-item disabled">Home (route missing)</span>
                            @endif
                        </li>
                    </ul>
                </li>

                {{-- Customers dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isCust ? 'active' : '' }}" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Customers
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            @if ($hasCustomersIdx)
                                <a class="dropdown-item" href="{{ route('customers.index') }}">List customers</a>
                            @else
                                <span class="dropdown-item disabled">List (route missing)</span>
                            @endif
                        </li>
                        <li>
                            @if ($hasCustomersNew)
                                <a class="dropdown-item" href="{{ route('customers.create') }}">New customer</a>
                            @else
                                <span class="dropdown-item disabled">New (route missing)</span>
                            @endif
                        </li>
                    </ul>
                </li>

                {{-- Assets dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isAsset ? 'active' : '' }}" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Assets
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            @if ($hasAssetsIdx)
                                <a class="dropdown-item" href="{{ route('assets.index') }}">List assets</a>
                            @else
                                <span class="dropdown-item disabled">List (route missing)</span>
                            @endif
                        </li>
                        <li>
                            @if ($hasAssetsNew)
                                <a class="dropdown-item" href="{{ route('assets.create') }}">New asset</a>
                            @else
                                <span class="dropdown-item disabled">New (route missing)</span>
                            @endif
                        </li>
                    </ul>
                </li>

                {{-- Rentals dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $isRent ? 'active' : '' }}" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Rentals
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            @if ($hasRentalsIdx)
                                <a class="dropdown-item" href="{{ route('rentals.index') }}">List rentals</a>
                            @else
                                <span class="dropdown-item disabled">List (route missing)</span>
                            @endif
                        </li>
                        <li>
                            @if ($hasRentalsNew)
                                <a class="dropdown-item" href="{{ route('rentals.create') }}">New rental</a>
                            @else
                                <span class="dropdown-item disabled">New (route missing)</span>
                            @endif
                        </li>
                    </ul>
                </li>

            </ul>

            {{-- Right side tools --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Language switcher (query param ?lang=) --}}
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        type="button">
                        Language: {{ $langLabel }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">English</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'pt_BR']) }}">Portuguese
                                (Brazil)</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['lang' => 'es']) }}">Spanish</a></li>
                    </ul>
                </div>

                {{-- Livewire tenant switcher (render if present; keep it simple) --}}
                @if (class_exists(\App\Livewire\Tenant\Switcher::class))
                    <livewire:tenant.switcher />
                @endif

                {{-- Minimal user menu (optional) --}}
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                            type="button">
                            {{ auth()->user()->name ?? 'User' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted">ID: {{ auth()->id() }}</span></li>
                            {{-- Add profile/logout when you wire auth --}}
                        </ul>
                    </div>
                @endauth

                @guest
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Login</a>
                @endguest
            </div>
        </div>
    </div>
</nav>
