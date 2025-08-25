@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="card form-card">
    <h2>Detail Resep</h2>

    <div class="form-row">
        <div class="form-group">
            <label>Nama Pasien</label>
            <div class="readonly-field">{{ $resep->nama_pasien }}</div>
        </div>

        <div class="form-group">
            <label>Tanggal Resep</label>
            <div class="readonly-field">{{ $resep->tanggal_resep }}</div>
        </div>
    </div>

    <div class="form-group">
        <label>Daftar Obat</label>
        <ul class="readonly-list">
            @foreach ($resep->detail as $item)
                <li>
                    <strong>{{ $item->obat->nama_obat ?? 'Obat tidak ditemukan' }}</strong>
                    — {{ $item->jumlah }}—{{$item->keterangan ?? ''}} 
                </li>
            @endforeach
        </ul>

    </div>

    @if ($resep->status === 'belum')
        <form method="POST" action="{{ route('apoteker.resep.proses', $resep->id_resep) }}">
            @csrf
            <div class="form-actions">
                <button type="submit" class="btn-submit" onclick="return confirm('Yakin proses resep ini?')">
                    Proses Resep
                </button>
            </div>
        </form>
    @else
        <p class="text-success">Resep sudah diproses.</p>
    @endif

    <div class="form-actions">
        <a href="{{ route('apoteker.resep.index') }}" class="btn-back">← Kembali</a>
    </div>
</div>
@endsection
