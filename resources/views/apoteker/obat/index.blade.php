@extends('layouts.app')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/crud-obat.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('content')
<div class="card">
    <div class="filter-header">
        <h2>Manajemen Obat</h2>
        <div class="actions">
            <a href="{{ route('obat.create') }}" class="button-add">+ Tambah Obat</a>
            <button class="btn-filter-toggle" id="toggleFilter">Filter</button>
        </div>
    </div>

    <div id="filterDropdown" class="filter-dropdown" style="display: none; margin-bottom: 20px;">
        <h4>Filter Obat</h4>
        <label>Nama Obat:
            <input type="text" id="filterNama" placeholder="Masukkan nama obat">
        </label>
        <label>Status Stok:
            <select id="filterStatusStok">
                <option value="">-- Semua --</option>
                <option value="tersedia">Tersedia</option>
                <option value="menipis">Stok Menipis</option>
                <option value="habis">Habis</option>
            </select>
        </label>
        <label>Status Kadaluarsa
            <select id="filterStatusKadaluarsa">
                <option value="">-- Semua --</option>
                <option value="belum">Belum Kadaluarsa</option>
                <option value="hampir">Hampir Kadaluarsa</option>
                <option value="kadaluarsa">Kadaluarsa</option>
            </select>
        </label>
        </label>
        <div class="filter-actions">
            <button id="applyFilter" class="btn-filter">Terapkan</button>
            <button id="resetFilter" class="btn-filter" style="background-color:#e11d48;">Reset</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Kadaluarsa</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="obat-table-body">
            @foreach($obat as $item)
            @php
                $now = \Carbon\Carbon::now();
                $exp = \Carbon\Carbon::parse($item->tanggal_kadaluarsa);

                if ($exp->lt($now)) {
                    $kadaluarsa = 'Kadaluarsa';
                    $kadaluarsaClass = 'badge-merah';
                } elseif ($exp->diffInDays($now) <= 30) {
                    $kadaluarsa = 'Hampir Kadaluarsa';
                    $kadaluarsaClass = 'badge-kuning';
                } else {
                    $kadaluarsa = 'Belum Kadaluarsa';
                    $kadaluarsaClass = 'badge-biru';
                }

                $stok = $item->stok;
                $stokLabel = $stok == 0 ? 'Habis' : ($stok <= 10 ? 'Stok Menipis' : 'Tersedia');
                $stokClass = $stok == 0 ? 'badge-merah' : ($stok <= 10 ? 'badge-kuning' : 'badge-biru');
            @endphp
            <tr>
                <td>{{ $item->nama_obat }}</td>
                <td>{{ $stok }}</td>
                <td>Rp{{ number_format($item->harga) }}</td>
                <td>{{ $item->tanggal_masuk }}</td>
                <td>{{ $item->tanggal_kadaluarsa }}</td>
                <td>
                    <span class="badge {{ $stokClass }}">{{ $stokLabel }}</span>
                    <span class="badge {{ $kadaluarsaClass }}">{{ $kadaluarsa }}</span>
                </td>
                <td class="aksi">
                    <a href="{{ route('obat.edit', $item->id_obat) }}" class="icon-button edit"><img src="/image/edit.png" alt="Edit"></a>
                    <form action="{{ route('obat.destroy', $item->id_obat) }}" method="POST" class="inline-form" onsubmit="return confirm('Yakin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-button delete"><img src="/image/delete.png" alt="Hapus"></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    $('#toggleFilter').click(() => $('#filterDropdown').slideToggle());

    $('#applyFilter').click(function () {
        $.ajax({
            url: "{{ route('obat.ajax.search') }}",
            type: 'GET',
            data: {
                nama: $('#filterNama').val(),
                status_stok: $('#filterStatusStok').val(),
                status_kadaluarsa: $('#filterStatusKadaluarsa').val()
            },
            success: function (data) {
                let rows = '';
                if (Object.keys(data).length > 0) {
                    Object.values(data).forEach(item => {
                        rows += `
                        <tr>
                            <td>${item.nama_obat}</td>
                            <td>${item.stok}</td>
                            <td>Rp${parseInt(item.harga).toLocaleString('id-ID')}</td>
                            <td>${item.tanggal_masuk ?? '-'}</td>
                            <td>${item.tanggal_kadaluarsa ?? '-'}</td>
                            <td>
                                <span class="badge ${item.stok_class}">${item.stok_label}</span>
                                <span class="badge ${item.kadaluarsa_class}">${item.kadaluarsa_label}</span>
                            </td>
                            <td class="aksi">
                                <a href="/obat/${item.id_obat}/edit" class="icon-button edit"><img src="/image/edit.png" alt="Edit"></a>
                                <form action="/obat/${item.id_obat}" method="POST" class="inline-form" onsubmit="return confirm('Yakin?')">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="icon-button delete"><img src="/image/delete.png" alt="Hapus"></button>
                                </form>
                            </td>
                        </tr>`;
                    });
                } else {
                    rows = `<tr><td colspan="7" style="text-align:center;">Tidak ditemukan</td></tr>`;
                }
                $('#obat-table-body').html(rows);
            }
        });
    });

    $('#resetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterStatusStok').val('');
        $('#filterStatusKadaluarsa').val('');
        $('#applyFilter').click();
    });
});
</script>
@endsection
