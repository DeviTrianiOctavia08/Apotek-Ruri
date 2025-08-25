<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class PenggunaRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.pengguna-register');
    }

    public function register(Request $request)
    {
        $request->merge([
            'username' => strtolower($request->username)
        ]);
        
        $request->validate([
            'username' => [
                'required',
                'unique:pengguna',
                'regex:/^[a-z0-9]+$/'
            ],
            'password' => [
                'required',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'nama_lengkap'  => 'required',
            'kontak'        => 'required',
            'alamat'        => 'required',
            'role'          => 'required|in:apoteker',
        ]);

        //  dd($request->all());

        DB::table('pengguna')->insert([
        'username'      => $request->username,
        'password'      => Hash::make($request->password),
        'nama_lengkap'  => $request->nama_lengkap,
        'kontak'        => $request->kontak,
        'alamat'        => $request->alamat,
        'role'          => $request->role,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

        return redirect()->route('pengguna.login.form')->with('success', 'Registrasi berhasil, silakan login.');

        return match ($user->role) {
            'apoteker'  => redirect('/apoteker/dashboard'),
            default     => redirect('/login'),
        };
    }
}
