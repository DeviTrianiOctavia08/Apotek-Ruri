<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Resep;

class DashboardPemilikController extends Controller
{
    public function index()
    {
        $totalResep = Resep::count();
        $totalTransaksi = Transaksi::where('jenis', 'keluar')->count();
        $jumlahObat = Obat::count();
        $obatHampirHabis = Obat::where('stok', '<=', 5)->count();
        $obatHabis = Obat::where('stok', 0)->get(); 

        $recentResep = Resep::latest()->take(5)->get();

        $bulanLabel = [];
        $dataBulanan = [];

        for ($i = 1; $i <= 12; $i++) {
            $label = Carbon::create()->month($i)->translatedFormat('F');
            $bulanLabel[] = $label;

            $jumlah = Transaksi::whereMonth('tanggal_transaksi', $i)
                ->whereYear('tanggal_transaksi', now()->year)
                ->where('jenis', 'keluar')
                ->count();

            $dataBulanan[] = $jumlah;
        }

        $tahunLabel = [];
        $dataTahunan = [];

        for ($i = 0; $i < 5; $i++) {
            $tahun = now()->subYears($i)->year;
            $tahunLabel[] = $tahun;

            $jumlah = Transaksi::whereYear('tanggal_transaksi', $tahun)
                ->where('jenis', 'keluar')
                ->count();

            $dataTahunan[] = $jumlah;
        }

        return view('dashboard.dashboard-pemilik', compact(
            'totalResep',
            'totalTransaksi',
            'jumlahObat',
            'obatHampirHabis',
            'recentResep',
            'obatHabis',
            'bulanLabel',
            'dataBulanan',
            'tahunLabel',
            'dataTahunan' 
        ));
    }


}
