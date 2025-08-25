<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;


class LaporanController extends Controller
{
    // public function form()
    // {
    //     return view('laporan.form');
    // }

    // public function cetak(Request $request)
    // {
    //     $request->validate([
    //         'dari' => 'required|date',
    //         'sampai' => 'required|date|after_or_equal:dari',
    //     ]);

    //     $transaksi = Transaksi::with('obat', 'pengguna')
    //         ->where('jenis', 'keluar')
    //         ->whereBetween('tanggal_transaksi', [$request->dari, $request->sampai])
    //         ->get();

    //     $pdf = PDF::loadView('laporan.pdf', [
    //         'transaksi' => $transaksi,
    //         'dari' => $request->dari,
    //         'sampai' => $request->sampai
    //     ]);

    //     return $pdf->download('laporan-transaksi.pdf');
    // }
}
