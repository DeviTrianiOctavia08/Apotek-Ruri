@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crud-obat.css') }}">  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')
<div class="card">
    <div class="filter-header">
        <h2>Daftar Pengguna</h2>
        <div class="actions">
            <a href="{{ route('pengguna.create') }}" class="button-add">+ Tambah Pengguna</a>
        </div>
    </div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Cari nama, username, atau role...">
        <img src="{{ asset('image/search.svg') }}" class="search-icon" alt="Search">
    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="pengguna-table-body">
            @foreach($pengguna as $item)
            <tr>
                <td>{{ $item->username }}</td>
                <td>{{ $item->nama_lengkap }}</td>
                <td>{{ ucfirst($item->role) }}</td>
                <td>{{ $item->kontak }}</td>
                <td>{{ $item->alamat }}</td>
                <td class="aksi">
                    <a href="{{ route('pengguna.edit', $item->id_pengguna) }}" class="icon-button edit" title="Edit">
                        <img src="{{ asset('image/edit.png') }}" alt="Edit">
                    </a>
                    <form action="{{ route('pengguna.destroy', $item->id_pengguna) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?')" class="inline-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-button delete" title="Hapus">
                            <img src="{{ asset('image/delete.png') }}" alt="Hapus">
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

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
