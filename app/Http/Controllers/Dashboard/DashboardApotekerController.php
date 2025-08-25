<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Obat;
use App\Models\Transaksi;
use App\Models\Resep;

class DashboardApotekerController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('obat')->latest()->take(10)->get();

        $statResep = [
            'Belum Diproses' => Resep::where('status', 'belum')->count(),
            'Diproses'       => Resep::where('status', 'diproses')->count(),
            'Selesai'        => Resep::where('status', 'selesai')->count(),
        ];
        $obat = Obat::fefo();

        return view('dashboard.dashboard-apoteker', compact('transaksi', 'statResep', 'obat'));
    }
}
