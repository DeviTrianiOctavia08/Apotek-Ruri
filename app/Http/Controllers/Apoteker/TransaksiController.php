<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Obat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    //TABLE AWAL
    public function index()
    {
        $transaksi = Transaksi::with(['obat', 'pengguna'])->orderByDesc('tanggal_transaksi')->get();
        return view('apoteker.transaksi.index', compact('transaksi'));
    }

    //klik button tambah
    public function create()
    {
        $obat = Obat::where('stok', '>', 0)
        ->orderBy('tanggal_kadaluarsa', 'asc')
        ->get();

        return view('apoteker.transaksi.create', compact('obat'));
    }

    //formnya
    public function store(Request $request)
{
    $request->validate([
        'id_obat' => 'required|exists:obat,id_obat',
        'jumlah' => 'required|integer|min:1',
        'jenis' => 'required|in:masuk,keluar',
        'tanggal_transaksi' => 'required|date',
        'tanggal_kadaluarsa' => 'nullable|date',
    ]);

    DB::beginTransaction();

    try {
        $obatLama = Obat::findOrFail($request->id_obat);
        $tanggalBaru = date('Y-m-d', strtotime($request->tanggal_kadaluarsa));
        $tanggalLama = date('Y-m-d', strtotime($obatLama->tanggal_kadaluarsa));
        $harga_satuan = $obatLama->harga;
        $jumlah = $request->jumlah;
        $total_harga = $harga_satuan * $jumlah;

        if ($request->jenis === 'masuk') {
            if ($tanggalBaru !== $tanggalLama) {
                // Buat entri obat baru jika tanggal kadaluarsa beda
                $obatBaru = Obat::create([
                    'nama_obat' => $obatLama->nama_obat,
                    'stok' => $jumlah,
                    'harga' => $harga_satuan,
                    'tanggal_masuk' => $request->tanggal_transaksi,
                    'tanggal_kadaluarsa' => $tanggalBaru,
                ]);

                // Simpan transaksi masuk
                Transaksi::create([
                    'id_obat' => $obatBaru->id_obat,
                    'jumlah' => $jumlah,
                    'jenis' => 'masuk',
                    'total' => $total_harga,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                ]);
            } else {
                // Tanggal kadaluarsa sama -> update stok saja
                $obatLama->update([
                    'stok' => $obatLama->stok + $jumlah,
                    'tanggal_masuk' => $request->tanggal_transaksi,
                ]);

                Transaksi::create([
                    'id_obat' => $obatLama->id_obat,
                    'jumlah' => $jumlah,
                    'jenis' => 'masuk',
                    'total' => $total_harga,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                ]);
            }
        } elseif ($request->jenis === 'keluar') {
            // Logika transaksi keluar tetap seperti sebelumnya
            $jumlahKeluar = $jumlah;

            $obatList = Obat::where('id_obat', $request->id_obat)
                ->whereDate('tanggal_kadaluarsa', '>=', Carbon::now())
                ->where('stok', '>', 0)
                ->orderBy('tanggal_kadaluarsa', 'asc')
                ->get();

            if ($obatList->isEmpty()) {
                return back()->withErrors(['Obat tidak tersedia atau sudah kadaluarsa.']);
            }

            $obatDipilih = null;

            foreach ($obatList as $obat) {
                if ($obat->stok >= $jumlahKeluar) {
                    $obat->stok -= $jumlahKeluar;
                    $obat->tanggal_keluar = $request->tanggal_transaksi;
                    $obat->save();

                    $obatDipilih = $obat;
                    break;
                }
            }

            if (!$obatDipilih) {
                return back()->withErrors(['Stok tidak mencukupi untuk transaksi keluar.']);
            }

            $total_keluar = $obatDipilih->harga * $jumlah;

            Transaksi::create([
                'id_obat' => $obatDipilih->id_obat,
                'jumlah' => $jumlah,
                'jenis' => 'keluar',
                'total' => $total_keluar,
                'tanggal_transaksi' => $request->tanggal_transaksi,
            ]);
        }

        DB::commit();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal menyimpan transaksi: ' . $e->getMessage());
        return back()->withErrors(['Terjadi kesalahan saat menyimpan transaksi.']);
    }
}

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_obat' => 'required|exists:obat,id_obat',
    //         'jumlah' => 'required|integer|min:1',
    //         'jenis' => 'required|in:masuk,keluar',
    //         'tanggal_transaksi' => 'required|date',
    //         'tanggal_kadaluarsa' => 'nullable|date',
    //         'total' => 'required|numeric|min:0',
    //     ]);

    //     // Gunakan DB transaction agar aman
    //     DB::beginTransaction();

    //     try {
    //         $obat = Obat::findOrFail($request->id_obat);
    //         $tanggalKadaluarsaBaru = date('Y-m-d', strtotime($request->tanggal_kadaluarsa));
    //         $tanggalKadaluarsaLama = date('Y-m-d', strtotime($obat->tanggal_kadaluarsa));


    //         if ($request->jenis === 'masuk') {
    //             if ($tanggalKadaluarsaBaru !== $tanggalKadaluarsaLama) {
    //         Obat::create([
    //             'nama_obat' => $request->nama_obat,
    //             'stok' => $request->stok,
    //             'harga' => $request->harga,
    //             'total' => $request->total,
    //             'tanggal_masuk' => $request->tanggal_masuk,
    //             'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
    //         ]);
    //     } else {
    //         // Jika tanggal kadaluarsa SAMA, update data yang lama
    //         $obat->update([
    //             'nama_obat' => $request->nama_obat,
    //             'stok' => $request->stok,
    //             'harga' => $request->harga,
    //             'total' => $request->total,
    //             'tanggal_masuk' => $request->tanggal_masuk,
    //             'tanggal_kadaluarsa' => $tanggalKadaluarsaBaru,
    //         ]);
    //     }

    //     return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
    //             // // Transaksi Masuk
    //             // $obat->stok += $request->jumlah;
    //             // $obat->harga = $request->total;
    //             // $obat->tanggal_masuk = $request->tanggal_transaksi;
    //             // $obat->tanggal_kadaluarsa = $request->tanggal_kadaluarsa;
    //             // $obat->save();

    //             // Transaksi::create([
    //             //     'id_obat' => $obat->id_obat,
    //             //     'jumlah' => $request->jumlah,
    //             //     'jenis' => 'masuk',
    //             //     'total' => $request->total,
    //             //     'tanggal_transaksi' => $request->tanggal_transaksi,
    //             // ]);

    //         } elseif ($request->jenis === 'keluar') {
    //             $jumlahKeluar = $request->jumlah;

    //             // Ambil semua stok obat dengan id_obat yg sama, belum expired, dan stok > 0
    //             $obatList = Obat::where('id_obat', $request->id_obat)
    //                 ->whereDate('tanggal_kadaluarsa', '>=', Carbon::now())
    //                 ->where('stok', '>', 0)
    //                 ->orderBy('tanggal_kadaluarsa', 'asc')
    //                 ->get();

    //             if ($obatList->isEmpty()) {
    //                 return back()->withErrors(['Obat tidak tersedia atau sudah kadaluarsa.']);
    //             }

    //             $obatDipilih = null;

    //             foreach ($obatList as $obat) {
    //                 if ($obat->stok >= $jumlahKeluar) {
    //                     $obat->stok -= $jumlahKeluar;
    //                     $obat->tanggal_keluar = $request->tanggal_transaksi;
    //                     $obat->save();

    //                     $obatDipilih = $obat;
    //                     break; // hanya satu transaksi dibuat
    //                 }
    //             }

    //             if (!$obatDipilih) {
    //                 return back()->withErrors(['Stok tidak mencukupi untuk transaksi keluar.']);
    //             }

    //             // Simpan transaksi
    //             Transaksi::create([
    //                 'id_obat' => $obatDipilih->id_obat,
    //                 'jumlah' => $request->jumlah,
    //                 'jenis' => 'keluar',
    //                 'total' => $request->total,
    //                 'tanggal_transaksi' => $request->tanggal_transaksi,
    //             ]);
    //         }

    //         DB::commit();
    //         return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan.');
            
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Gagal menyimpan transaksi: ' . $e->getMessage());
    //         return back()->withErrors(['Terjadi kesalahan saat menyimpan transaksi.']);
    //     }
    // }

    //pencarian atau filter
    public function ajaxSearch(Request $request)
    {
        $query = Transaksi::with('obat');

        if ($request->nama) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->dari && $request->sampai) {
            $query->whereBetween('tanggal_transaksi', [$request->dari, $request->sampai]);
        } elseif ($request->dari) {
            $query->whereDate('tanggal_transaksi', '>=', $request->dari);
        } elseif ($request->sampai) {
            $query->whereDate('tanggal_transaksi', '<=', $request->sampai);
        }

        $data = $query->orderBy('tanggal_transaksi', 'asc')->get();

        $result = $data->map(function ($item) {
            return [
                'nama_obat' => $item->obat->nama_obat ?? '-',
                'jenis' => ucfirst($item->jenis),
                'jumlah' => $item->jumlah,
                'total' => $item->total,
                'tanggal_transaksi' => $item->tanggal_transaksi,
            ];
        });

        return response()->json($result);
    }

}
