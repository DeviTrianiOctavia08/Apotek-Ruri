<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;

class PenggunaLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.pengguna-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::guard('pengguna')->attempt($credentials)) {
            $request->session()->regenerate();
        
            $role = Auth::guard('pengguna')->user()->role;
        
            if ($role === 'pemilik') {
                return redirect()->intended('/pemilik/dashboard');
            } elseif ($role === 'apoteker') {
                return redirect()->intended('/apoteker/dashboard');
            } elseif ($role === 'dokter') {
                return redirect()->intended('/dokter/dashboard');
            } else {
                return redirect('/'); // default
            }
        }
        
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('pengguna')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("login");
    }
}
