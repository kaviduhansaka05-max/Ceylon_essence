<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class UserLogin extends Component
{
    public $email, $password;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Make HTTP request to backend API
        $response = Http::post('/api/login', [
            'email' => $this->email,
            'password' => $this->password,
            'user_type' => 'user',
        ]);

        if ($response->successful()) {
            // Handle successful login
            session(['user_token' => $response->json('token')]);
            session()->flash('message', 'User login successful');
            return redirect()->route('user.dashboard');
        } else {
            // Handle failed login attempt
            session()->flash('error', 'Invalid login credentials');
        }
    }
    public function render()
    {
        return view('livewire.user-login');
    }
}