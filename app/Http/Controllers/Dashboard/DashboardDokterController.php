<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Resep;
use App\Models\Obat;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;

class DashboardDokterController extends Controller
{
    public function index()
    {   
        $reseps = Resep::with(['obats'])
            ->where('id_dokter', Auth::guard('pengguna')->id())
            ->orderBy('tanggal_resep', 'desc')
            ->get();

        return view('dashboard.dashboard-dokter', compact('reseps'));
    }
}
