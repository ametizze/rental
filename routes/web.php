<?php
// routes/web.php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Guest-only: login form + submit
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.attempt')->middleware('throttle:10,1');
});

// Auth-only
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('home');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    });

    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
    });
});
