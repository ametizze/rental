<div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">{{ __('Dashboard') }}</h2>
            <div class="text-end">
                <button wire:click="fetchStats" class="btn btn-sm btn-outline-secondary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="fetchStats" class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span>
                    {{ __('Refresh Data') }}
                </button>
            </div>
        </div>
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-12">
                <a href="{{ route('stock.sale') }}" class="card text-decoration-none shadow-sm h-100 border-info">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack h3 text-info"></i>
                        <h4 class="card-title mt-2">{{ __('Quick Stock Sale') }}</h4>
                        <p class="text-muted">{{ __('Sell consumables immediately.') }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-12">
                <a href="{{ route('quick.rent') }}" class="card text-decoration-none shadow-sm h-100 border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-speedometer2 h3 text-success"></i>
                        <h4 class="card-title mt-2">{{ __('Quick Rent') }} {{ __('(Fast Check-Out)') }}</h4>
                        <p class="text-muted">{{ __('Mark single equipment as rented instantly.') }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-12">
                <a href="{{ route('rentals') }}" class="card text-decoration-none shadow-sm h-100 border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-truck h3 text-primary"></i>
                        <h4 class="card-title mt-2">{{ __('Create New Rental') }}</h4>
                        <p class="text-muted">{{ __('Start a new equipment rental.') }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-12">
                <a href="{{ route('invoices') }}" class="card text-decoration-none shadow-sm h-100 border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt h3 text-warning"></i>
                        <h4 class="card-title mt-2">{{ __('View Open Invoices') }}</h4>
                        <p class="text-muted">{{ __('Check receivables and collect payments.') }}</p>
                    </div>
                </a>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-md-3">
                @php $flowColor = $dailyNetFlow >= 0 ? 'bg-success' : 'bg-danger'; @endphp
                <div class="card text-white {{ $flowColor }} mb-3">
                    <div class="card-header">{{ __('Daily Net Flow') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format(abs($dailyNetFlow), 2) }}</h2>
                        <small>{{ $dailyNetFlow >= 0 ? __('Profit') : __('Loss') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                @php $flowColor = $weeklyNetFlow >= 0 ? 'bg-success' : 'bg-danger'; @endphp
                <div class="card text-white {{ $flowColor }} mb-3">
                    <div class="card-header">{{ __('Weekly Net Flow') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format(abs($weeklyNetFlow), 2) }}</h2>
                        <small>{{ $weeklyNetFlow >= 0 ? __('Profit') : __('Loss') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                @php $flowColor = $monthlyNetFlow >= 0 ? 'bg-success' : 'bg-danger'; @endphp
                <div class="card text-white {{ $flowColor }} mb-3">
                    <div class="card-header">{{ __('Monthly Net Flow') }} ({{ __('Net Profit') }})</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format(abs($monthlyNetFlow), 2) }}</h2>
                        <small>{{ $monthlyNetFlow >= 0 ? __('Profit') : __('Loss') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">{{ __('Active Rentals') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $activeRentals }}</h2>
                        <small>{{ __('Currently rented out') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">

            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">{{ __('Accounts Receivable') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($balanceReceivable, 2) }}</h2>
                        <small>{{ __('Total Balance Due from Customers') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">{{ __('Invoices Due This Week') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $pendingInvoicesWeekly }}</h2>
                        <small>{{ __('Pending/Overdue Invoices due this week') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">{{ __('Total Overdue Invoices') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $overdueInvoicesCount }}</h2>
                        <small>{{ __('Past due date') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-dark mb-3">
                    <div class="card-header">{{ __('Top 5 Equipment') }}</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($topEquipment as $item)
                                <li class="list-group-item bg-dark text-white">{{ $item->name }}
                                    ({{ $item->total_rentals }} {{ __('rentals') }})
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        {{ __('Income vs Expenses (Last 6 Months)') }}
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyOverviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const ctx = document.getElementById('monthlyOverviewChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @js($chartLabels),
                        datasets: [{
                                label: '{{ __('Income') }}',
                                data: @js($chartIncome),
                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: '{{ __('Expenses') }}',
                                data: @js($chartExpenses),
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</div>
