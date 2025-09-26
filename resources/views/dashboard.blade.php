@extends('components.layouts.app')

@section('content')
    <div class="container mt-5">
        @livewire('dashboard-stats')
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endsection
