@extends('layouts.app')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')
<div class="card">
    <div class="filter-header">
        <h2>Manajemen Transaksi</h2>
        <div class="actions">
            <a href="{{ route('transaksi.create') }}" class="button-add">+ Tambah Transaksi</a>
            <button class="btn-filter-toggle" id="toggleFilter">Filter</button>
        </div>    
    </div>

    <div id="filterDropdown" class="filter-dropdown" style="display: none; margin-bottom: 20px;">
        <label>Nama Barang:
            <input type="text" id="filterNamaBarang" placeholder="Masukkan nama obat">
        </label>
        <label>Jenis Transaksi:
            <select id="filterJenis">
                <option value="">-- Semua --</option>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </label>
        <label>Tanggal Dari:
            <input type="date" id="filterTanggalDari">
        </label>
        <label>Sampai:
            <input type="date" id="filterTanggalSampai">
        </label>
        <div class="filter-actions">
            <button id="applyTransaksiFilter" class="btn-filter">Terapkan</button>
            <button id="resetTransaksiFilter" class="btn-filter" style="background-color:#e11d48;">Reset</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody id="transaksi-table-body">
            @foreach ($transaksi as $t)
                <tr>
                    <td>{{ $t->obat->nama_obat ?? '-' }}</td>
                    <td>{{ ucfirst($t->jenis) }}</td>
                    <td>{{ $t->jumlah }}</td>
                    <td>Rp{{ number_format($t->total, 0, ',', '.') }}</td>
                    <td>{{ $t->tanggal_transaksi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    $('#toggleFilter').click(() => $('#filterDropdown').slideToggle());

    $('#applyTransaksiFilter').click(function () {
        $.ajax({
            url: "{{ route('transaksi.ajax.search') }}",
            type: 'GET',
            data: {
                nama: $('#filterNamaBarang').val(),
                jenis: $('#filterJenis').val(),
                dari: $('#filterTanggalDari').val(),
                sampai: $('#filterTanggalSampai').val()
            },
            success: function (data) {
                let rows = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        rows += `
                        <tr>
                            <td>${item.nama_obat}</td>
                            <td>${item.jenis}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp${item.total}</td>
                            <td>${item.tanggal_transaksi}</td>
                        </tr>`;
                    });
                } else {
                    rows = `<tr><td colspan="5" style="text-align:center;">Tidak ditemukan</td></tr>`;
                }
                $('#transaksi-table-body').html(rows);
            }
        });
    });

    $('#resetTransaksiFilter').click(function () {
        $('#filterNamaBarang').val('');
        $('#filterJenis').val('');
        $('#filterTanggalDari').val('');
        $('#filterTanggalSampai').val('');
        $('#applyTransaksiFilter').click();
    });
});
</script>
@endsection
