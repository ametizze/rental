<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Livewire\Component;

class Switcher extends Component
{
    public array $tenants = [];
    public ?int $currentId = null;

    public function mount(): void
    {
        $this->tenants = Tenant::orderBy('name')->get(['id', 'name', 'slug'])->toArray();
        $this->currentId = session('tenant_id');
    }

    public function useTenant(int $tenantId): \Illuminate\Http\RedirectResponse
    {
        $exists = Tenant::whereKey($tenantId)->exists();
        if ($exists) {
            session(['tenant_id' => $tenantId]);
        }
        // Come back to the previous page or home
        return redirect()->to(url()->previous() ?: route('home'));
    }

    public function render()
    {
        return view('livewire.tenant.switcher');
    }
}
