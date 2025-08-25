<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::all();
        return view('apoteker.pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        return view('apoteker.pengguna.create');
    }

    public function store(Request $request)
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
            'nama_lengkap' => 'required',
            'kontak' => 'required',
            'alamat' => 'required',
            'role' => 'required|in:dokter,apoteker,pemilik'
        ]);

        Pengguna::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Pengguna $pengguna)
    {
        return view('apoteker.pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $request->merge([
            'username' => strtolower($request->username)
        ]);

        $request->validate([
            'username' => [
                'required',
                'regex:/^[a-z0-9]+$/',
                Rule::unique('pengguna', 'username')->ignore($pengguna->id_pengguna, 'id_pengguna'),
            ],
            'password' => [
                'nullable',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'nama_lengkap' => 'required',
            'kontak' => 'required',
            'alamat' => 'required',
            'role' => 'required|in:dokter,apoteker,pemilik'
        ]);

        $pengguna->update([
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $pengguna->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna diperbarui.');
    }


    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna dihapus.');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->input('cari');

        $pengguna = \App\Models\Pengguna::where('nama_lengkap', 'like', '%' . $keyword . '%')
            ->orWhere('username', 'like', '%' . $keyword . '%')
            ->orWhere('role', 'like', '%' . $keyword . '%')
            ->get();

        return response()->json($pengguna);
    }

}
