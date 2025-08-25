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
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; position: relative; flex: 1; max-width: 400px;">
            <input type="text" id="searchInput" placeholder="Cari nama, username, atau role..." style="padding-left: 35px; width: 100%;">
            <img src="{{ asset('image/search.svg') }}" alt="Search" style="position: absolute; left: 10px; width: 16px;">
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="#" onclick="downloadPenggunaPDF()" class="btn btn-success" style="background-color: #10b981; color: white; padding: 8px 16px; border-radius: 5px;">
                <i class="fa fa-download"></i> Download PDF
            </a>
            <a id="printBtn" target="_blank" class="btn-filter" style="background-color: #3b82f6; color: white; padding: 8px 16px; border-radius: 5px;">
                <i class="fa fa-print"></i> Cetak
            </a>
        </div>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $('#searchInput').on('input', function () {
    let keyword = $(this).val();
    $.ajax({
        url: "{{ route('pemilik.pengguna.ajax.search') }}",
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
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="6" style="text-align: center;">Tidak ditemukan.</td></tr>`;
            }
            $('#pengguna-table-body').html(rows);
        }
    });
});

function downloadPenggunaPDF() {
    const keyword = $('#searchInput').val();
    const url = "{{ route('pemilik.pengguna.download') }}" + "?cari=" + encodeURIComponent(keyword);
    window.open(url, '_blank');
}
    document.getElementById('printBtn').addEventListener('click', function () {
        const keyword = $('#searchInput').val();
        const url = "{{ route('pemilik.pengguna.print') }}" + "?cari=" + encodeURIComponent(keyword);
        this.href = url;
    });

</script>
@endsection
