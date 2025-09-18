<?php

namespace App\Livewire;

use App\Constants\UserRoles;
use App\Models\User;
use App\Models\Tenant;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    public $name, $email, $password, $password_confirmation, $role;
    public $userId;
    public $tenants;
    public $selectedTenantId; // Apenas para o superadmin

    public function mount()
    {
        if (auth()->user()->role === 'superadmin') {
            $this->tenants = Tenant::all();
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|string|in:' . implode(',', array_keys(UserRoles::ROLES)),
            'password' => 'nullable|min:5|confirmed',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        // Atribui o tenant_id apenas se o usuário não for superadmin
        if (auth()->user()->role === 'superadmin') {
            $data['tenant_id'] = $this->selectedTenantId;
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
        }

        User::updateOrCreate(['id' => $this->userId], $data);
        $this->resetForm();
    }

    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->selectedTenantId = $user->tenant_id;
    }

    public function delete($userId)
    {
        User::destroy($userId);
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role', 'userId', 'selectedTenantId']);
    }

    public function render()
    {
        $users = auth()->user()->role === 'superadmin' ? User::all() : User::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('livewire.user-manager', [
            'users' => $users,
            'roles' => UserRoles::ROLES, // Pass the roles to the view
        ]);
    }
}
