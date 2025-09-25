<div class="container mt-5">
    <h2 class="mb-4">{{ __('My Profile') }}</h2>

    @if (session()->has('profile_success'))
        <div class="alert alert-success">{{ session('profile_success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">{{ __('Profile Information') }}</div>
        <div class="card-body">
            <form wire:submit.prevent="saveProfile">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" class="form-control" wire:model.defer="name">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control" wire:model.defer="email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Preferred Language') }}</label>
                        <select class="form-control" wire:model.defer="preferredLang">
                            @foreach ($languages as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('preferredLang')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success">{{ __('Save Profile') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">{{ __('Update Password') }}</div>
        <div class="card-body">
            @if (session()->has('password_success'))
                <div class="alert alert-success">{{ session('password_success') }}</div>
            @endif

            <form wire:submit.prevent="updatePassword">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('Current Password') }}</label>
                        <input type="password" class="form-control" wire:model.defer="currentPassword">
                        @error('currentPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('New Password') }}</label>
                        <input type="password" class="form-control" wire:model.defer="newPassword">
                        @error('newPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Confirm New Password') }}</label>
                        <input type="password" class="form-control" wire:model.defer="newPasswordConfirmation">
                        @error('newPasswordConfirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning">{{ __('Change Password') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
