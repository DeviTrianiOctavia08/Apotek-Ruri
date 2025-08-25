@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection

@section('content')
    <h2>Permintaan Resep dari Dokter</h2>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Cari resep">
        <img src="{{ asset('image/search.svg') }}" class="search-icon" alt="Search">
    </div>

@if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert danger">{{ $errors->first() }}</div>
@endif

@if ($resep->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Nama Pasien</th>
                <th>Tanggal Resep</th>
                <th>Daftar Obat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resep as $r)
                <tr>
                    <td>{{ $r->nama_pasien }}</td>
                    <td>{{ $r->tanggal_resep }}</td>
                    <td>
                        <ul>
                            @foreach ($r->detail as $d)
                                <li>{{ $d->obat->nama_obat ?? '-' }} - {{ $d->jumlah }} item</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="aksi">
                        <a href="{{ route('apoteker.resep.show', $r->id_resep) }}" class="icon-button edit">
                            <img src="{{ asset('image/show.png') }}" alt="Lihat">
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>Tidak ada permintaan resep.</p>
@endif

<script>
    $('#searchInput').on('input', function () {
    let keyword = $(this).val();
    $.ajax({
        url: "{{ route('pengguna.ajax.search') }}",
        type: 'GET',
        data: { cari: keyword },
        success: function (data) {
            let rows = '';
            if (data.length > 0) {
                data.forEach(item => {
                    rows += `
                    <tr>
                        <td>${item.username}</td>
                        <td>${item.nama_lengkap}</td>
                        <td>${item.role.charAt(0).toUpperCase() + item.role.slice(1)}</td>
                        <td>${item.kontak}</td>
                        <td>${item.alamat}</td>
                        <td class="aksi">
                            <a href="/pengguna/${item.id_pengguna}/edit" class="icon-button edit" title="Edit">
                                <img src="/image/edit.png" alt="Edit">
                            </a>
                            <form action="/pengguna/${item.id_pengguna}" method="POST" onsubmit="return confirm('Yakin ingin hapus?')" class="inline-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="icon-button delete" title="Hapus">
                                    <img src="/image/delete.png" alt="Hapus">
                                </button>
                            </form>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" style="text-align: center;">Tidak ditemukan.</td></tr>`;
            }
            $('#pengguna-table-body').html(rows);
        }
    });
});

</script>
@endsection
