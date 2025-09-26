<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                @if (auth()->check() && auth()->user()->tenant)
                    {{ auth()->user()->tenant->name }}
                @else
                    {{ config('app.name', 'Rental MultiSystem') }}
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customers') }}" wire:navigate>{{ __('Customers') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('equipment') }}" wire:navigate>{{ __('Equipment') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rentals') }}" wire:navigate>{{ __('Rentals') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('maintenance') }}"
                                wire:navigate>{{ __('Maintenance') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invoices') }}" wire:navigate>{{ __('Invoices') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('transactions') }}"
                                wire:navigate>{{ __('Transactions') }}</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Reports') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.customer-balance') }}">{{ __('Customer Balance') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('profitability') }}">{{ __('Profitability Report') }}</a>
                                </li>
                            </ul>
                        </li>

                        @if (auth()->user()->role === 'superadmin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tenants') }}">{{ __('Tenants') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users') }}">{{ __('Users') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav d-flex">
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li><a class="dropdown-item"
                                    href="{{ route('locale.switch', ['locale' => 'en']) }}">English</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('locale.switch', ['locale' => 'pt_BR']) }}">Português</a></li>
                            <li><a class="dropdown-item"
                                    href="{{ route('locale.switch', ['locale' => 'es']) }}">Español</a></li>
                        </ul>
                    </li>

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                @if (auth()->user()->tenant)
                                    {{ auth()->user()->tenant->name }} -
                                @endif
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">{{ __('Profile') }}</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        wire:click.prevent="logout">{{ __('Logout') }}</a>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</div>
