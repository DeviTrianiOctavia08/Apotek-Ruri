@extends('layouts.app')

@section('title', 'Dashboard Apoteker')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dashboard-apoteker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection

@section('content')
    {{-- HEADER KHUSUS DASHBOARD --}}
    <div class="dashboard-top">
        <div class="dashboard-left">
            <h2>Halo, {{ auth()->user()->nama_lengkap }}</h2>
        </div>
        <div class="dashboard-right">
            <img src="{{ asset('image/bell.png') }}" alt="Notifikasi" class="icon">
            <img src="{{ asset('image/user.png') }}" alt="Profil" class="icon">
        </div>
    </div>

    {{-- STATUS PERMINTAAN RESEP --}}
    <div class="card status-container">
        <h3>Status Permintaan Resep</h3>
        <div class="status-row">
            @foreach ($statResep as $status => $jumlah)
                <div class="status-box">
                    <h4>{{ $status }}</h4>
                    <p>{{ $jumlah }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RIWAYAT TRANSAKSI OBAT --}}
    <div class="card">
        <h3>Riwayat Transaksi Obat</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $trx)
                    <tr>
                        <td>{{ $trx->obat->nama_obat }}</td>
                        <td>{{ ucfirst($trx->jenis) }}</td>
                        <td>{{ $trx->jumlah }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
