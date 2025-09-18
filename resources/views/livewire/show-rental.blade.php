<div>
    <div class="container mt-4">
        <h2 class="mb-4">{{ __('Rental Details') }}</h2>
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                {{ __('Rental') }} #{{ $this->rental->id }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>{{ __('Customer Details') }}</h5>
                        <p><strong>{{ __('Name') }}:</strong> {{ $this->rental->customer->name }}</p>
                        <p><strong>{{ __('Email') }}:</strong> {{ $this->rental->customer->email }}</p>
                        <p><strong>{{ __('Phone') }}:</strong> {{ $this->rental->customer->phone }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5>{{ __('Rental Information') }}</h5>
                        <p><strong>{{ __('Start Date') }}:</strong> {{ $this->rental->start_date->format('d/m/Y') }}
                        </p>
                        <p><strong>{{ __('End Date') }}:</strong> {{ $this->rental->end_date->format('d/m/Y') }}</p>
                        <p><strong>{{ __('Status') }}:</strong>
                            <span class="badge bg-{{ $this->rental->status == 'active' ? 'primary' : 'success' }}">
                                {{ __(ucfirst($this->rental->status)) }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <h5>{{ __('Equipment') }}</h5>
                <ul class="list-group list-group-flush">
                    @foreach ($this->rental->equipment as $item)
                        <li class="list-group-item">
                            {{ $item->name }} ({{ $item->serial }}) - ${{ number_format($item->daily_rate, 2) }} /
                            {{ __('day') }}
                        </li>
                    @endforeach
                </ul>

                <hr class="my-4">

                <h5>{{ __('Condition Photos') }}</h5>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="fw-bold">{{ __('Start of Rental') }}</p>
                        @if (!empty($this->rental->start_photos))
                            <div class="d-flex flex-wrap">
                                @foreach ($this->rental->start_photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" class="img-thumbnail me-2 mb-2"
                                        style="max-height: 150px;">
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ __('No photos available.') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold">{{ __('End of Rental') }}</p>
                        @if (!empty($this->rental->end_photos))
                            <div class="d-flex flex-wrap">
                                @foreach ($this->rental->end_photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" class="img-thumbnail me-2 mb-2"
                                        style="max-height: 150px;">
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ __('No photos available.') }}</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div class="text-md-end">
                    <h4>{{ __('Total Amount') }}: ${{ number_format($this->rental->total_amount, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
