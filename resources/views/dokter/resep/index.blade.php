@extends('layouts.app')

@section('title', 'Daftar Resep')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endsection

@section('content')
<div class="card">
    <h2>Daftar Resep</h2>

    <a href="{{ route('resep.create') }}" class="button-add" style="margin-bottom: 20px;">+ Buat Resep</a>

    <table>
        <thead>
            <tr>
                <th>Pasien</th>
                <th>Tanggal</th>
                <th>Obat</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reseps as $resep)
                <tr>
                    <td>{{ $resep->nama_pasien }}</td>
                    <td>{{ $resep->tanggal_resep }}</td>
                    <td>
                        <ul>
                            @foreach ($resep->obats as $obat)
                                <li>{{ $obat->nama_obat }} ({{ $obat->pivot->jumlah }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $resep->obats->sum('pivot.jumlah') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
