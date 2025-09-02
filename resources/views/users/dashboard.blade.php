@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Dashboard</h1>
                <small class="text-muted">Welcome back, {{ Auth::user()->name ?? 'User' }}.</small>
            </div>
            <div>
                <a href="{{ route('users.profile') }}" class="btn btn-outline-secondary btn-sm">Profile</a>
                <a href="{{ route('logout') }}" class="btn btn-danger btn-sm"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">Rentals</small>
                                <div class="h4 mb-0">{{ $rentals_count ?? 0 }}</div>
                            </div>
                            <div class="text-primary align-self-center">
                                <!-- icon placeholder -->
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h18M3 12h18M3 17h18"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Vehicles</small>
                        <div class="h4">{{ $vehicles_count ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Customers</small>
                        <div class="h4">{{ $customers_count ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">Revenue</small>
                        <div class="h4">${{ number_format($revenue ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions + Recent -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Quick actions</h5>
                        <p class="text-muted small mb-3">Common tasks to get you started.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('assets.create') }}" class="btn btn-primary">New Rental</a>
                            <a href="{{ route('customers.create') }}" class="btn btn-outline-secondary">Add Customer</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Recent activity</h5>
                        @if (!empty($recentActivities) && count($recentActivities))
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>When</th>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th class="text-end">User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentActivities as $act)
                                            <tr>
                                                <td class="align-middle">{{ $act->created_at->diffForHumans() }}</td>
                                                <td class="align-middle">{{ $act->type }}</td>
                                                <td class="align-middle">{{ Str::limit($act->details ?? '-', 60) }}</td>
                                                <td class="align-middle text-end">{{ $act->user->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No recent activity to show.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
