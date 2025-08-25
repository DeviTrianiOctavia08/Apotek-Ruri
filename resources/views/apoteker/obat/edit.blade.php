@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')

<div class="card form-card">
    <h2>Edit Obat</h2>
    <form method="POST" action="{{ route('obat.update', $obat->id_obat) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Obat</label>
            <input type="text" name="nama_obat" value="{{ $obat->nama_obat }}" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}" class="form-control">
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" step="0.01" name="harga" value="{{$obat->harga}}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" value="{{ \Carbon\Carbon::parse($obat->tanggal_masuk)->format('Y-m-d') }}">

            </div>
            <div class="form-group">
                <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa</label>
                <input type="date" name="tanggal_kadaluarsa" value="{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('Y-m-d') }}">
            </div>
        </div>

        <div class="form-actions">    
            <a href="{{ route('obat.index') }}" class="back">← Kembali</a>
            <button type="submit" class="btn-submit">Update</button>
        </div>
    </form>
</div>
@endsection
