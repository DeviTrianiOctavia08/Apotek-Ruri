@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" haref="{{ asset('css/print.css')}}">
@endsection

@section('content')
<div class="card">
    <div class="header-filter">
        <h2>Laporan Transaksi</h2>
    </div>

    <div class="filter-wrapper">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
            <button class="btn-filter-toggle" onclick="toggleFilter()">🔍 Filter</button>
            <div style="display: flex; gap: 10px;">
                <a href="#" onclick="downloadTransaksiPDF()" class="btn btn-success" style="background-color: #10b981; color: white; padding: 8px 16px; border-radius: 5px;">
                    <i class="fa fa-download"></i> Download PDF
                </a>
                <a id="printBtn" target="_blank" class="btn-filter" style="background-color: #6b7280; color: white; padding: 8px 16px; border-radius: 5px;">
                    <i class="fa fa-print"></i> Cetak
                </a>
            </div>
        </div>

        <form id="filterForm" class="filter-dropdown">
            @csrf
            <h4>Filter Transaksi</h4>

            <div class="form-group">
                <label>Nama Obat</label>
                <input type="text" name="nama" placeholder="Cari nama obat">
            </div>

            <div class="form-group">
                <label>Jenis Transaksi</label>
                <select name="jenis">
                    <option value="">-- Semua --</option>
                    <option value="masuk">Masuk</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="form-group-row">
                <div class="form-group">
                    <label for="tanggal_dari">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" id="tanggal_dari">
                </div>

                <div class="form-group">
                    <label for="tanggal_sampai">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" id="tanggal_sampai">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Terapkan</button>
                <button type="button" class="btn-filter" style="background-color:#ef4444;" onclick="resetFilter()">Reset</button>
            </div>

        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total (Rp)</th>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                </tr>
            </thead>
            <tbody id="transaksiTable">
                @foreach ($transaksi as $item)
                <tr>
                    <td>{{ $item->obat->nama_obat ?? '-' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp{{ number_format(($item->total / max($item->jumlah, 1)), 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $item->jenis === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($item->jenis) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFilter() {
        const form = document.getElementById('filterForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }

    document.getElementById('filterForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(this);

        fetch("{{ route('pemilik.transaksi.filter') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': data.get('_token')
            },
            body: data
        })
        .then(res => res.json())
        .then(data => {
            let rows = '';

            if (data.length === 0) {
                rows = '<tr><td colspan="5" style="text-align:center;">Tidak ada data</td></tr>';
            } else {
                data.forEach(item => {
                    const badgeClass = item.jenis === 'masuk' ? 'bg-success' : 'bg-danger';
                    rows += `
                        <tr>
                            <td>${item.nama_obat}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp${parseInt(item.harga).toLocaleString('id-ID')}</td>
                            <td>Rp${parseInt(item.total).toLocaleString('id-ID')}</td>
                            <td>${item.tanggal}</td>
                            <td><span class="badge ${badgeClass}">${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)}</span></td>
                        </tr>
                    `;
                });
            }

            document.getElementById('transaksiTable').innerHTML = rows;
        })
        .catch(err => console.error('Filter gagal:', err));
    });

    function resetFilter() {
        const form = document.getElementById('filterForm');
        form.reset();

        fetch("{{ route('pemilik.transaksi.filter') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            let rows = '';

            if (data.length === 0) {
                rows = '<tr><td colspan="5" style="text-align:center;">Tidak ada data</td></tr>';
            } else {
                data.forEach(item => {
                    const badgeClass = item.jenis === 'masuk' ? 'bg-success' : 'bg-danger';
                    rows += `
                        <tr>
                            <td>${item.nama_obat}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp${parseInt(item.harga).toLocaleString('id-ID')}</td>
                            <td>Rp${parseInt(item.total).toLocaleString('id-ID')}</td>
                            <td>${item.tanggal}</td>
                            <td><span class="badge ${badgeClass}">${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)}</span></td>
                        </tr>
                    `;
                });
            }

            document.getElementById('transaksiTable').innerHTML = rows;
        });
    }

    function downloadTransaksiPDF() {
    const form = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams();

    for (let [key, value] of form.entries()) {
        if (value) params.append(key, value);
    }

    window.open("{{ route('pemilik.transaksi.download') }}?" + params.toString(), '_blank');
    document.getElementById('printBtn').addEventListener('click', function (e) {
        const form = document.getElementById('filterForm');
        const params = new URLSearchParams(new FormData(form)).toString();
        this.href = "{{ route('pemilik.transaksi.print') }}?" + params;
    });

}
</script>
@endsection
