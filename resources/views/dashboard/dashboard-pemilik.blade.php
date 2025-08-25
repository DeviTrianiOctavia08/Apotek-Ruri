@extends('layouts.app')

@section('title', 'Dashboard Pemilik')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection

@section('content')
<div class="card">
    <h2>Halo Pemilik</h2>

    <div class="form-row">
        <div class="card" style="flex: 1;">
            <h4>Total Transaksi Keluar</h4>
            <p><strong>{{ $totalTransaksi }}</strong></p>
        </div>

        <div class="card" style="flex: 1;">
            <h4>Jumlah Obat</h4>
            <p><strong>{{ $jumlahObat }}</strong></p>
        </div>

        <div class="card" style="flex: 1;">
            <h4>Obat Hampir Habis</h4>
            <p><strong>{{ $obatHampirHabis }}</strong></p>
        </div>
    </div>
</div>

<div class="card">
    <h3>Resep Terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>Pasien</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recentResep as $resep)
            <tr>
                <td>{{ $resep->nama_pasien }}</td>
                <td>{{ $resep->tanggal_resep }}</td>
                <td>
                    <span class="badge {{ $resep->status == 'selesai' ? 'bg-success' : ($resep->status == 'diproses' ? 'bg-warning' : 'bg-danger') }}">
                        {{ ucfirst($resep->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
