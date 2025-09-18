<?php

use App\Livewire\CustomerBalance;
use App\Livewire\CustomerManager;
use App\Livewire\EquipmentManager;
use App\Livewire\InvoiceManager;
use App\Livewire\Login;
use App\Livewire\RentalManager;
use App\Livewire\ShowInvoice;
use App\Livewire\ShowRental;
use App\Livewire\TenantManager;
use App\Livewire\UserManager;
use App\Models\Equipment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/validator/{uuid}', function ($uuid) {
    $equipment = Equipment::where('qr_uuid', $uuid)->firstOrFail();
    return view('public.equipment', ['equipment' => $equipment]);
})->name('public.equipment');

Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

Route::get('/login-as-superadmin', function () {
    Auth::loginUsingId(1);
    return redirect()->route('dashboard');
})->name('dev.login_as_superadmin');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/invoices', InvoiceManager::class)->name('invoices');
    Route::get('/invoices/{invoice:uuid}', ShowInvoice::class)->name('invoices.show');

    Route::get('/customers', CustomerManager::class)->name('customers');
    Route::get('/equipment', EquipmentManager::class)->name('equipment');

    // Rentals
    Route::get('/rentals', RentalManager::class)->name('rentals');
    Route::get('/rentals/{uuid}/details', ShowRental::class)->name('rentals.details')->middleware('auth');

    // Reports
    Route::get('/reports/customer-balance', CustomerBalance::class)->name('reports.customer-balance')->middleware('auth');

    Route::middleware('can:manage-tenants')->group(function () {
        Route::get('/tenants', TenantManager::class)->name('tenants');
    });

    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', UserManager::class)->name('users');
    });
});

// Public invoice
Route::get('/invoices/{invoice:uuid}/print', function ($uuid) {
    // Set locale to en
    app()->setLocale('en');
    // Get invoice by UUID
    $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
    // Print
    return view('invoices.print', ['invoice' => $invoice]);
})->name('invoices.print');

Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'pt_BR', 'es'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');
