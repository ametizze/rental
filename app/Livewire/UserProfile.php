<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // Importante

class UserProfile extends Component
{
    // Propriedades do usuário
    public $name;
    public $email;
    public $preferredLang;

    // Propriedades da senha
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation; // Laravel espera este nome para a regra 'confirmed'

    public $languages = [];

    public function mount()
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->preferredLang = $user->preferred_lang;

        // Lista de idiomas (Hardcoded para o MVP)
        $this->languages = [
            'en' => 'English',
            'pt_BR' => 'Português (Brasil)',
            'es' => 'Español',
        ];
    }

    public function saveProfile()
    {
        // ... (código saveProfile permanece o mesmo) ...
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'preferredLang' => 'required|in:' . implode(',', array_keys($this->languages)),
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'preferred_lang' => $this->preferredLang,
        ]);

        session()->flash('profile_success', __('Profile updated successfully.'));
    }

    public function updatePassword()
    {
        $user = Auth::user();

        // 1. Validação com verificação de senha atual customizada
        $this->validate([
            'currentPassword' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail(__('The current password you provided does not match your password.'));
                }
            }],
            'newPassword' => 'required|string|min:8|confirmed', // 'confirmed' verifica newPasswordConfirmation
        ], [
            'newPassword.confirmed' => __('The new password confirmation does not match.'),
            'newPassword.min' => __('The new password must be at least 8 characters.'),
        ]);

        // 2. Hash e Salva a nova senha
        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        // 3. Limpa o formulário e dá feedback
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        session()->flash('password_success', __('Your password has been updated successfully.'));
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
