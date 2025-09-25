@extends('components.layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">{{ __('Dashboard') }}</h1>

        <div class="row">
            <div class="col-12">
                @livewire('dashboard-stats')
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endsection
