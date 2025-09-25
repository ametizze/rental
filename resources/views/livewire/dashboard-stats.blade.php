<div>
    <div class="container mt-4">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">{{ __('Daily Revenue') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($dailyRevenue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">{{ __('Weekly Revenue') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($weeklyRevenue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">{{ __('Monthly Revenue') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($monthlyRevenue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">{{ __('Active Rentals') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $activeRentals }}</h2>
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
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-header">{{ __('Accounts Payable') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($balancePayable, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
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
                    type: 'bar', // Tipo Bar (o correto para dados históricos)
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
                            x: {
                                stacked: false, // Pode ser true para barras empilhadas, mas deixamos false para comparação
                            },
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
