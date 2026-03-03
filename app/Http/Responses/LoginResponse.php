<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // Ambil role pertama user (menggunakan Spatie Permission)
        $role = $user->roles->first()?->name ?? '';

        // Jika role adalah 'admin' atau 'manajemen', arahkan ke dashboard
        if (in_array(strtolower($role), ['admin', 'manajemen'])) {
            return redirect()->intended(route('dashboard'));
        }

        // Selain itu (pelanggan, dll) arahkan ke halaman landing
        return redirect()->intended(route('home'));
    }
}
