<?php
// app/Livewire/Navbar.php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/login');
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
