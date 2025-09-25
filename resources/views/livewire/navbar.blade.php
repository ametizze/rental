<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                EasyRental
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('equipment') }}">{{ __('Equipment') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customers') }}">{{ __('Customers') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rentals') }}">{{ __('Rentals') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invoices') }}">{{ __('Invoices') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('transactions') }}">{{ __('Transactions') }}</a>
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
                                {{-- Aqui entrariam outros relatórios, como o de Lucratividade --}}
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
            </div>
        </div>
    </nav>
</div>
