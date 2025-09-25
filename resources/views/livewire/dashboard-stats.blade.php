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
                        {{ __('Monthly Overview') }}
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyOverviewChart" style="max-height: 400px;"></canvas>
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
                    type: 'pie',
                    data: {
                        labels: ['{{ __('Income') }}', '{{ __('Expenses') }}'],
                        datasets: [{
                            data: [
                                {{ $monthlyRevenue }},
                                {{-- Aqui você precisaria de uma variável $monthlyExpenses no componente --}}
                                0
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.formattedValue + ' (' + context.dataset.data[
                                            context.dataIndex] + ')';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</div>
