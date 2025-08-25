@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Halo Dokter</h2>
    <p>Berikut adalah daftar resep yang pernah Anda buat:</p>

    <table>
        <thead>
            <tr>
                <th>Nama Pasien</th>
                <th>Tanggal Resep</th>
                <th>Keterangan</th>
                <th>Jumlah Obat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reseps as $resep)
            <tr>
                <td>{{ $resep->nama_pasien }}</td>
                <td>{{ $resep->tanggal_resep }}</td>
                <td>{{ $resep->keterangan_sakit }}</td>
                <td>{{ $resep->detail->count() }}</td>
                <td>
                    <a href="{{ route('resep.show', $resep->id_resep) }}" class="button-add" style="padding: 6px 12px; font-size: 13px;">Lihat Resep</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Belum ada resep.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
