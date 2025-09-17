<?php

use App\Livewire\TenantManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    // return view('auth.login');
    return;
})->name('login');

Route::get('/tenants', TenantManager::class)->middleware('auth');

Route::get('/login-as-superadmin', function () {
    Auth::loginUsingId(1);
    return redirect('/tenants');
});
