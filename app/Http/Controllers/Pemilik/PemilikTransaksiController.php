<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Obat;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Barryvdh\DomPDF\PDF;

class PemilikTransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('obat')->orderBy('tanggal_transaksi', 'desc')->get();

        $listTahun = Transaksi::selectRaw('YEAR(tanggal_transaksi) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('pemilik.transaksi.index', compact('transaksi'));
    }

    public function filter(Request $request)
    {
        $query = Transaksi::with('obat');

        if ($request->filled('nama')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_transaksi', $request->tahun);
        }

        $transaksi = $query->orderBy('tanggal_transaksi', 'desc')->get();

        $response = $transaksi->map(function ($item) {
            $harga_satuan = $item->jumlah > 0 ? ($item->total / $item->jumlah) : 0;
            return [
                'nama_obat' => $item->obat->nama_obat ?? '-',
                'jumlah' => $item->jumlah,
                'harga' => $harga_satuan,
                'total' => $item->total,
                'tanggal' => \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y'),
                'jenis' => $item->jenis,
            ];
        });

        return response()->json($response);
    }

    public function print(Request $request)
    {
        $query = Transaksi::query()->with('obat');

        if ($request->filled('nama')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        $transaksis = $query->get();

        return view('pemilik.transaksi.print', compact('transaksis'));
    }

    public function download(Request $request)
    {
        $query = Transaksi::with('obat');

        if ($request->filled('nama')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        $transaksis = $query->get();

        $pdf = FacadePdf::loadView('pemilik.transaksi.print', compact('transaksis'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_transaksi_' . now()->format('Ymd_His') . '.pdf');
    }

}
