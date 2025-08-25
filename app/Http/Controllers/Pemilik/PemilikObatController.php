<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Barryvdh\DomPDF\PDF;

class PemilikObatController extends Controller
{
    public function index()
    {
        $obats = Obat::orderBy('tanggal_kadaluarsa')->get();
        return view('pemilik.obat.index', compact('obats'));
    }

    public function filter(Request $request)
    {
        $query = Obat::query();

        if ($request->nama) {
            $query->where('nama_obat', 'like', '%' . $request->nama . '%');
        }

        if ($request->stok === 'habis') {
            $query->where('stok', '=', 0);
        } elseif ($request->stok === 'hampir_habis') {
            $query->where('stok', '>', 0)->where('stok', '<=', 10);
        } elseif ($request->stok === 'tersedia') {
            $query->where('stok', '>', 10);
        }

        if ($request->kadaluarsa === 'hampir') {
            $query->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(30))
                ->whereDate('tanggal_kadaluarsa', '>', now());
        } elseif ($request->kadaluarsa === 'sudah') {
            $query->whereDate('tanggal_kadaluarsa', '<=', now());
        }

        $obats = $query->orderBy('tanggal_kadaluarsa')->get();

        $rows = '';
        foreach ($obats as $obat) {
            $exp = Carbon::parse($obat->tanggal_kadaluarsa);
            $now = Carbon::now();

            if ($exp->lt($now)) {
                $kadaluarsa = 'Kadaluarsa';
                $kadaluarsaClass = 'badge-merah';
            } elseif ($exp->diffInDays($now) <= 30) {
                $kadaluarsa = 'Hampir Kadaluarsa';
                $kadaluarsaClass = 'badge-kuning';
            } else {
                $kadaluarsa = 'Belum Kadaluarsa';
                $kadaluarsaClass = 'badge-biru';
            }

            if ($obat->stok == 0) {
                $stok = 'Habis';
                $stokClass = 'badge-merah';
            } elseif ($obat->stok <= 10) {
                $stok = 'Hampir Habis';
                $stokClass = 'badge-kuning';
            } else {
                $stok = 'Tersedia';
                $stokClass = 'badge-hijau';
            }

            $rows .= '
            <tr>
                <td>' . $obat->nama_obat . '</td>
                <td>' . $obat->stok . '</td>
                <td>Rp' . number_format($obat->harga, 0, ',', '.') . '</td>
                <td>' . Carbon::parse($obat->tanggal_masuk)->format('d M Y') . '</td>
                <td>' . Carbon::parse($obat->tanggal_kadaluarsa)->format('d M Y') . '</td>
                <td>
                    <span class="status-label ' . $stokClass . '">' . $stok . '</span>
                    <span class="status-label ' . $kadaluarsaClass . '">' . $kadaluarsa . '</span>
                </td>
            </tr>';
        }

        if ($obats->isEmpty()) {
            $rows = '<tr><td colspan="6" style="text-align: center;">Tidak ada data</td></tr>';
        }

        return response($rows);
    }

    public function print(Request $request)
    {
        $query = Obat::query();

        if ($request->filled('nama')) {
            $query->where('nama_obat', 'like', '%' . $request->nama . '%');
        }

        if ($request->stok == 'habis') {
            $query->where('stok', 0);
        } elseif ($request->stok == 'hampir_habis') {
            $query->where('stok', '<=', 10)->where('stok', '>', 0);
        } elseif ($request->stok == 'tersedia') {
            $query->where('stok', '>', 10);
        }

        if ($request->kadaluarsa == 'hampir') {
            $query->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(30))
                ->whereDate('tanggal_kadaluarsa', '>=', now());
        } elseif ($request->kadaluarsa == 'sudah') {
            $query->whereDate('tanggal_kadaluarsa', '<', now());
        }

        $obats = $query->orderBy('tanggal_kadaluarsa')->get();

        return view('pemilik.obat.print', compact('obats'));
    }

    public function download(Request $request)
    {
        $query = Obat::query();

        // filter seperti print
        if ($request->filled('nama')) {
            $query->where('nama_obat', 'like', '%' . $request->nama . '%');
        }

        if ($request->stok == 'habis') {
            $query->where('stok', 0);
        } elseif ($request->stok == 'hampir_habis') {
            $query->where('stok', '<=', 10)->where('stok', '>', 0);
        } elseif ($request->stok == 'tersedia') {
            $query->where('stok', '>', 10);
        }

        if ($request->kadaluarsa == 'hampir') {
            $query->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(30))
                ->whereDate('tanggal_kadaluarsa', '>=', now());
        } elseif ($request->kadaluarsa == 'sudah') {
            $query->whereDate('tanggal_kadaluarsa', '<', now());
        }

        $obats = $query->orderBy('tanggal_kadaluarsa')->get();

        $pdf = FacadePdf::loadView('pemilik.obat.print', compact('obats'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_obat_' . now()->format('Ymd_His') . '.pdf');
    }


}
