<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use App\Models\Transaksi;
use App\Models\Obat;
use Illuminate\Support\Facades\Auth;

class ApotekerResepController extends Controller
{
    public function index()
    {
        $resep = Resep::with('detail.obat')
            ->where('status', 'belum')
            ->orderByDesc('tanggal_resep')
            ->get();

        return view('apoteker.resep.index', compact('resep'));
    }

    //liat detail resep
    public function show($id)
    {
        $resep = Resep::with('detail.obat')->findOrFail($id);
        return view('apoteker.resep.show', compact('resep'));
    }
    
    //proses resep
    public function proses($id)
        {
            $resep = Resep::with('detail')->findOrFail($id);

            foreach ($resep->detail as $item) {
                $obat = Obat::where('id_obat', $item->id_obat)->first();

                if (!$obat || $obat->stok < $item->jumlah) {
                    return back()->withErrors(['msg' => "Stok tidak cukup untuk obat {$obat->nama_obat}"]);
                }

                $obat->stok -= $item->jumlah;
                $obat->tanggal_keluar = now();
                $obat->save();

                Transaksi::create([
                    'id_obat' => $item->id_obat,
                    'id_pengguna' => Auth::guard('pengguna')->id(),
                    'jenis' => 'keluar',
                    'jumlah' => $item->jumlah,
                    'total' => $item->jumlah * $obat->harga,
                    'tanggal_transaksi' => now()
                ]);
            }

            // Update status resep
            $resep->status = 'selesai';
            $resep->save();

            return redirect()->route('apoteker.resep.index')->with('success', 'Resep berhasil diproses');
        }

}
