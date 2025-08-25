<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;

class PemilikPenggunaController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::all();
        return view('pemilik.pengguna.index', compact('pengguna'));
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

    public function print(Request $request)
    {
        $pengguna = Pengguna::query()
            ->when($request->cari, fn($q) =>
                $q->where('nama_lengkap', 'like', "%{$request->cari}%")
                ->orWhere('username', 'like', "%{$request->cari}%")
                ->orWhere('role', 'like', "%{$request->cari}%")
            )
            ->get();

        return view('pemilik.pengguna.print', compact('pengguna'));
    }

    public function download(Request $request)
    {
        $pengguna = Pengguna::query()
            ->when($request->cari, fn($q) =>
                $q->where('nama_lengkap', 'like', "%{$request->cari}%")
                ->orWhere('username', 'like', "%{$request->cari}%")
                ->orWhere('role', 'like', "%{$request->cari}%")
            )
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pemilik.pengguna.print', compact('pengguna'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_pengguna_' . now()->format('Ymd_His') . '.pdf');
    }
}
