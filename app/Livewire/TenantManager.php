<?php

namespace App\Livewire;

use App\Models\Tenant;
use Livewire\Component;

class TenantManager extends Component
{
    public $name, $domain, $email, $phone, $address, $city, $state, $zipcode;
    public $responsible_person, $is_active = true;
    public $tenantId;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $this->tenantId,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
        ]);

        Tenant::updateOrCreate(['id' => $this->tenantId], [
            'name' => $this->name,
            'domain' => $this->domain,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
            'responsible_person' => $this->responsible_person,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
    }

    public function edit($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->tenantId = $tenant->id;
        $this->name = $tenant->name;
        $this->domain = $tenant->domain;
        $this->email = $tenant->email;
        $this->phone = $tenant->phone;
        $this->address = $tenant->address;
        $this->city = $tenant->city;
        $this->state = $tenant->state;
        $this->zipcode = $tenant->zipcode;
        $this->responsible_person = $tenant->responsible_person;
        $this->is_active = $tenant->is_active;
    }

    public function delete($tenantId)
    {
        Tenant::destroy($tenantId);
    }

    public function resetForm()
    {
        $this->reset();
    }

    public function render()
    {
        // O superadmin pode ver todos os tenants
        $tenants = Tenant::all();

        return view('livewire.tenant-manager', [
            'tenants' => $tenants,
        ]);
    }
}
