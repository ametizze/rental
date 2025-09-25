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
use App\Livewire\TransactionManager;
use App\Livewire\UserManager;
use App\Models\Equipment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rota de Início
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Rota Pública de Validação QR Code
Route::get('/validator/{uuid}', function ($uuid) {
    $equipment = Equipment::where('qr_uuid', $uuid)->firstOrFail();
    return view('public.equipment', ['equipment' => $equipment]);
})->name('public.equipment');

// Auth routes
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

// Temporary Dev Route to login as Superadmin (ID = 1)
// Note: Remove this route in production for security reasons.
Route::get('/login-as-superadmin', function () {
    Auth::loginUsingId(1);
    return redirect()->route('dashboard');
})->name('dev.login_as_superadmin');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard e Módulos Principais
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/equipment', EquipmentManager::class)->name('equipment');
    Route::get('/customers', CustomerManager::class)->name('customers');

    // Módulos Financeiros
    Route::get('/invoices', InvoiceManager::class)->name('invoices');
    Route::get('/invoices/{invoice:uuid}', ShowInvoice::class)->name('invoices.show');
    Route::get('/transactions', TransactionManager::class)->name('transactions');

    // Módulo de Aluguéis
    Route::get('/rentals', RentalManager::class)->name('rentals');
    Route::get('/rentals/{rental:uuid}/details', ShowRental::class)->name('rentals.details'); // Removida middleware('auth') redundante

    // Módulos de Relatórios
    Route::get('/reports/customer-balance', CustomerBalance::class)->name('reports.customer-balance'); // Removida middleware('auth') redundante

    // Rotas de Superadmin (Permissão: manage-tenants)
    Route::middleware('can:manage-tenants')->group(function () {
        Route::get('/tenants', TenantManager::class)->name('tenants');
    });

    // Rotas de Admin e Superadmin (Permissão: manage-users)
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', UserManager::class)->name('users');
    });
});

// Rota Pública de Impressão (Não Autenticada - Segurança via UUID)
Route::get('/invoices/{invoice:uuid}/print', function (Invoice $invoice) {
    // Carrega relações essenciais para a impressão
    $invoice->load(['customer', 'items', 'tenant', 'rental.equipment']);
    // Define o locale para 'en' para garantir consistência na impressão
    app()->setLocale('en');
    return view('invoices.print', ['invoice' => $invoice]);
})->name('invoices.print');

// Rota de Idioma (Switch)
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'pt_BR', 'es'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');
