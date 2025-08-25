<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Transaksi;

class ObatController extends Controller
{
    public function index()
    {
        $obat = Obat::orderBy('tanggal_kadaluarsa')->get()->map(function ($item) {
            $item->status_stok = $item->stok == 0
                ? 'Habis'
                : ($item->stok <= 10 ? 'Stok Menipis' : 'Tersedia');

            $exp = Carbon::parse($item->tanggal_kadaluarsa);
            $now = Carbon::now();
            $selisih = $exp->diffInDays($now, false);

            $item->status_kadaluarsa = $selisih < 0
                ? 'Kadaluarsa'
                : ($selisih <= 30 ? 'Hampir Kadaluarsa' : 'Belum Kadaluarsa');

            // Format nama obat jadi: "P01 - Paramex"
            $item->display_nama = $item->id_obat . ' - ' . $item->nama_obat;

            return $item;
        });

        return view('apoteker.obat.index', compact('obat'));
    }

    public function create()
    {
        return view('apoteker.obat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after_or_equal:tanggal_masuk',
            'tanggal_keluar' => 'nullable|date',
        ]);

        // Cek apakah obat dengan nama dan tanggal kadaluarsa sudah ada
        $existing = Obat::where('nama_obat', $request->nama_obat)
                        ->whereDate('tanggal_kadaluarsa', $request->tanggal_kadaluarsa)
                        ->first();

        if ($existing) {
            $existing->stok += $request->stok;
            $existing->harga = $request->harga;
            $existing->tanggal_masuk = $request->tanggal_masuk;
            $existing->tanggal_keluar = $request->tanggal_keluar ?? $existing->tanggal_keluar;
            $existing->save();

            return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui (tidak dibuat ulang).');
        }

        // Generate ID otomatis (contoh: P01, P02, ...)
        $last = Obat::latest('id_obat')->first();
        $lastNumber = $last ? (int) Str::after($last->id_obat, 'P') : 0;
        $newId = 'P' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

        $obat= Obat::create([
            'id_obat' => $newId,
            'nama_obat' => $request->nama_obat,
            'stok' => $request->stok,
            'harga' => $request->harga,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar ?? null,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ]);

        Transaksi::create([
            'id_obat' => $obat->id_obat,
            'jenis_transaksi' => 'masuk',
            'jumlah' => $obat->stok,
            'harga' => $obat->harga,
            'total' => $obat->stok * $obat->harga,
            'tanggal_transaksi' => $obat->tanggal_masuk,
        ]);

        return redirect()->route('obat.index')->with('success', 'Obat baru berhasil ditambahkan.');
    }

    public function edit($id_obat)
    {
        $obat = Obat::findOrFail($id_obat);
        return view('apoteker.obat.edit', compact('obat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        $obat = Obat::findOrFail($id);

        $tanggalKadaluarsaBaru = date('Y-m-d', strtotime($request->tanggal_kadaluarsa));
        $tanggalKadaluarsaLama = date('Y-m-d', strtotime($obat->tanggal_kadaluarsa));
        $stokLama = $obat->stok;

        // 🟢 Jika stok = 0, boleh timpa meskipun tanggal kadaluarsa beda
        if ($stokLama == 0) {
            $selisih = $request->stok; // karena sebelumnya 0

            $obat->update([
                'nama_obat' => $request->nama_obat,
                'stok' => $request->stok,
                'harga' => $request->harga,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
            ]);

            // Catat sebagai transaksi masuk
            if ($selisih > 0) {
                Transaksi::create([
                    'id_obat' => $obat->id_obat,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $selisih,
                    'harga' => $request->harga,
                    'total' => $selisih * $request->harga,
                    'tanggal_transaksi' => now(),
                ]);
            }
        }
        // 🔴 Jika stok masih ada dan tanggal kadaluarsa berubah → buat data baru
        elseif ($stokLama > 0 && $tanggalKadaluarsaBaru !== $tanggalKadaluarsaLama) {
            Obat::create([
                'nama_obat' => $request->nama_obat,
                'stok' => $request->stok,
                'harga' => $request->harga,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
            ]);
        }
        // ⚪ Jika tanggal kadaluarsa sama → update biasa
        else {
            $obat->update([
                'nama_obat' => $request->nama_obat,
                'stok' => $request->stok,
                'harga' => $request->harga,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
            ]);

            $selisih = $request->stok - $stokLama;

            if ($selisih > 0) {
                Transaksi::create([
                    'id_obat' => $obat->id_obat,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $selisih,
                    'harga' => $request->harga,
                    'total' => $selisih * $request->harga,
                    'tanggal_transaksi' => now(),
                ]);
            } elseif ($selisih < 0) {
                $obat->update([
                    'nama_obat' => $request->nama_obat,
                    'stok' => $request->stok,
                    'harga' => $request->harga,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    // tidak perlu update tanggal kadaluarsa karena sama
                ]);
            }
        }

        return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'nama_obat' => 'required|string|max:255',
    //         'stok' => 'required|integer|min:0',
    //         'harga' => 'required|integer|min:0',
    //         'tanggal_masuk' => 'required|date',
    //         'tanggal_kadaluarsa' => 'required|date',
    //     ]);

    //     $obat = Obat::findOrFail($id);
    //     $stokLama = $obat->stok;

    //     $tanggalKadaluarsaBaru = date('Y-m-d', strtotime($request->tanggal_kadaluarsa));
    //     $tanggalKadaluarsaLama = date('Y-m-d', strtotime($obat->tanggal_kadaluarsa));

    //     if ($tanggalKadaluarsaBaru !== $tanggalKadaluarsaLama) {
    //         Obat::create([
    //             'nama_obat' => $request->nama_obat,
    //             'stok' => $request->stok,
    //             'harga' => $request->harga,
    //             'tanggal_masuk' => $request->tanggal_masuk,
    //             'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
    //         ]);
    //     } else {
    //         $selisih = $request->stok - $stokLama; // hitung sebelum update

    //         $obat->update([
    //             'nama_obat' => $request->nama_obat,
    //             'stok' => $request->stok,
    //             'harga' => $request->harga,
    //             'tanggal_masuk' => $request->tanggal_masuk,
    //             'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
    //         ]);

    //         if ($selisih > 0) {
    //             Transaksi::create([
    //                 'id_obat' => $obat->id_obat,
    //                 'jenis_transaksi' => 'masuk',
    //                 'jumlah' => $selisih,
    //                 'harga' => $request->harga,
    //                 'total' => $selisih * $request->harga,
    //                 'tanggal_transaksi' => now(),
    //             ]);
    //         } elseif ($selisih < 0) {
    //             $obat->update([
    //                 'nama_obat' => $request->nama_obat,
    //                 'stok' => $request->stok,
    //                 'harga' => $request->harga,
    //                 'tanggal_masuk' => $request->tanggal_masuk,
    //                 // tidak perlu update tanggal kadaluarsa karena sama
    //             ]);
    //             // Transaksi::create([
    //             //     'id_obat' => $obat->id_obat,
    //             //     'jenis_transaksi' => 'keluar',
    //             //     'jumlah' => abs($selisih),
    //             //     'harga' => $request->harga,
    //             //     'total' => abs($selisih) * $request->harga,
    //             //     'tanggal_transaksi' => now(),
    //             // ]);
    //         }


    //     }

    //     return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
    // }

    public function destroy($id_obat)
    {
        Obat::destroy($id_obat);
        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus.');
    }

    public function ajaxSearch(Request $request)
    {
        $statusStok = strtolower($request->status_stok);
        $statusKadaluarsa = strtolower($request->status_kadaluarsa);

        $query = Obat::query();

        if ($request->nama) {
            $query->where('nama_obat', 'like', '%' . $request->nama . '%');
        }

        if ($request->tanggal) {
            $query->whereDate('tanggal_kadaluarsa', '<=', $request->tanggal);
        }

        $obat = $query->orderBy('tanggal_kadaluarsa')->get();

        $filtered = $obat->filter(function ($item) use ($statusStok, $statusKadaluarsa) {
            $stok = $item->stok;
            $stokStatus = $stok == 0 ? 'habis' : ($stok <= 10 ? 'menipis' : 'tersedia');

            $now = Carbon::now();
            $exp = Carbon::parse($item->tanggal_kadaluarsa);
            $selisih = $now->diffInDays($exp, false);

            $kadaluarsaStatus = $selisih < 0 ? 'kadaluarsa' : ($selisih <= 30 ? 'hampir' : 'belum');

            $stokCocok = empty($statusStok) || $stokStatus === $statusStok;
            $kadaluarsaCocok = empty($statusKadaluarsa) || $kadaluarsaStatus === $statusKadaluarsa;

            return $stokCocok && $kadaluarsaCocok;
        })->values();

        $result = $filtered->map(function ($item) {
            $stok = $item->stok;
            $stok_label = $stok == 0 ? 'Habis' : ($stok <= 10 ? 'Stok Menipis' : 'Tersedia');
            $stok_class = $stok == 0 ? 'badge-merah' : ($stok <= 10 ? 'badge-kuning' : 'badge-biru');

            $now = Carbon::now();
            $exp = Carbon::parse($item->tanggal_kadaluarsa);
            $selisih = $now->diffInDays($exp, false);
            $kadaluarsa_label = $selisih < 0 ? 'Kadaluarsa' : ($selisih <= 30 ? 'Hampir Kadaluarsa' : 'Belum Kadaluarsa');
            $kadaluarsa_class = $kadaluarsa_label === 'Kadaluarsa' ? 'badge-merah' :
                                ($kadaluarsa_label === 'Hampir Kadaluarsa' ? 'badge-kuning' : 'badge-biru');

            return [
                'id_obat' => $item->id_obat,
                'nama_obat' => $item->id_obat . ' - ' . $item->nama_obat,
                'stok' => $item->stok,
                'harga' => $item->harga,
                'tanggal_masuk' => $item->tanggal_masuk,
                'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                'stok_label' => $stok_label,
                'stok_class' => $stok_class,
                'kadaluarsa_label' => $kadaluarsa_label,
                'kadaluarsa_class' => $kadaluarsa_class
            ];
        });

        return response()->json($result->values());
    }
}
