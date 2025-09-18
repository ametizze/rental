<div class="container mt-4">
    <div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">{{ __('Active Rentals') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">{{ $activeRentals }}</h2>
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
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">{{ __('Yearly Revenue') }}</div>
                    <div class="card-body">
                        <h2 class="card-title">${{ number_format($yearlyRevenue, 2) }}</h2>
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
    </div>
</div>
