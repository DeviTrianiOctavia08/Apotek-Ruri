<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class PemilikResepController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $status = $request->input('status');

        $query = Resep::with(['detail.obat', 'dokter']);

        if ($tanggal) {
            $query->whereDate('tanggal_resep', $tanggal);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reseps = $query->latest()->get();

        return view('pemilik.resep.index', compact('reseps', 'tanggal', 'status'));
    }

    public function filter(Request $request)
    {
        $query = Resep::with(['detail.obat', 'dokter']);

        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        if ($request->filled('nama_lengkap')) {
            $query->whereHas('dokter', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_resep', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_resep', '<=', $request->tanggal_sampai);
        }

        $reseps = $query->latest()->get();

        $html = '';
        foreach ($reseps as $resep) {
            $html .= '<tr>';
            $html .= '<td>' . $resep->nama_pasien . '</td>';
            $html .= '<td>' . ($resep->dokter->nama_lengkap ?? '-') . '</td>';
            $html .= '<td>' . \Carbon\Carbon::parse($resep->tanggal_resep)->format('d M Y') . '</td>';
            $html .= '<td style="text-transform: capitalize;">' . $resep->status . '</td>';
            $html .= '<td><ul>';
            foreach ($resep->detail as $item) {
                $html .= '<li>' . ($item->obat->nama_obat ?? '-') . ' (' . $item->jumlah . ')</li>';
            }
            $html .= '</ul></td>';
            $html .= '</tr>';
        }

        return response($html);
    }

    public function print(Request $request)
    {
        $query = Resep::with(['detail.obat', 'dokter']);

        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        if ($request->filled('nama_lengkap')) {
            $query->whereHas('dokter', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_resep', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_resep', '<=', $request->tanggal_sampai);
        }

        $reseps = $query->orderBy('tanggal_resep', 'desc')->get();

        return view('pemilik.resep.print', compact('reseps'));
    }

    public function download(Request $request)
    {
        $query = Resep::with(['detail.obat', 'dokter']);

        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        if ($request->filled('nama_lengkap')) {
            $query->whereHas('dokter', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_resep', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_resep', '<=', $request->tanggal_sampai);
        }

        $reseps = $query->orderBy('tanggal_resep', 'desc')->get();

        $pdf = FacadePdf::loadView('pemilik.resep.print', compact('reseps'))->setPaper('A4', 'portrait');
        return $pdf->download('laporan_resep_' . now()->format('Ymd_His') . '.pdf');
    }
}
