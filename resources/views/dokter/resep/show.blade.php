@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Resep untuk {{ $resep->nama_pasien }}</h2>

    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resep->detail ?? [] as $d)
            <tr>
                <td>{{ $d->obat->nama_obat }}</td>
                <td>{{ $d->jumlah }}</td>
                <td>{{ $d->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <a href="{{ route('resep.index') }}" class="button-add" style="background-color: #F75A5A;">Kembali</a>
    </div>
</div>
@endsection
