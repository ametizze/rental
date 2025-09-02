@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Sign in</strong>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-info">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" type="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            {{-- optional: <a href="#">Forgot password?</a> --}}
                        </div>

                        <button class="btn btn-primary w-100" type="submit">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
