<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use App\Models\DetailResep;
use App\Models\Obat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function index()
    {
        $reseps = Resep::with(['detail.obat'])->where('id_dokter', Auth::guard('pengguna')->id())->get();
        return view('dokter.resep.index', compact('reseps'));
    }

    public function create()
    {
        $obat = Obat::all();
        return view('dokter.resep.create', compact('obat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pasien' => 'required|string',
            'tanggal_resep' => 'required|date',
            'keterangan_sakit' => 'nullable|string',
            'id_obat' => 'required|array|min:1',
            'id_obat.*' => 'required|exists:obat,id_obat',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
            'keterangan' => 'required|array',
            'keterangan.*' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Simpan data resep utama
            $resep = Resep::create([
                'nama_pasien' => $request->input('nama_pasien'),
                'id_dokter' => Auth::guard('pengguna')->id(),
                'tanggal_resep' => $request->input('tanggal_resep'),
                'keterangan_sakit' => $request->input('keterangan_sakit'),
                'status' => 'belum',
            ]);

            // Simpan detail resep
            foreach ($request->id_obat as $i => $obatId) {
                DetailResep::create([
                    'id_resep' => $resep->id_resep,
                    'id_obat' => $obatId,
                    'jumlah' => $request->jumlah[$i],
                    'keterangan' => $request->keterangan[$i],
                ]);
            }

            DB::commit();
            return redirect()->route('resep.index')->with('success', 'Resep berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $resep = Resep::with(['detail.obat'])->findOrFail($id);
        return view('dokter.resep.show', compact('resep'));
    }

    public function destroy($id)
    {
        $resep = Resep::findOrFail($id);
        $resep->detail()->delete();
        $resep->delete();

        return redirect()->route('resep.index')->with('success', 'Resep berhasil dihapus.');
    }
}
