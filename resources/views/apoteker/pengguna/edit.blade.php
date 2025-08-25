@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="card form-card">
    <h2>Edit Pengguna</h2>

    <form action="{{ route('pengguna.update', $pengguna->id_pengguna) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ $pengguna->username }}" required>
        </div>
        <div class="form-group">
            <label>Password Baru (Opsional)</label>
            <input type="password" name="password" class="form-control" >
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" >
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ $pengguna->nama_lengkap }}" required>
        </div>
        <div class="form-group">
            <label>Kontak</label>
            <input type="text" name="kontak" class="form-control" value="{{ $pengguna->kontak }}" required>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required>{{ $pengguna->alamat }}</textarea>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="apoteker" {{ $pengguna->role == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
                <option value="dokter" {{ $pengguna->role == 'dokter' ? 'selected' : '' }}>Dokter</option>
                <option value="pemilik" {{ $pengguna->role == 'pemilik' ? 'selected' : '' }}>Pemilik</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Update</button>
    </form>
</div>
@endsection
