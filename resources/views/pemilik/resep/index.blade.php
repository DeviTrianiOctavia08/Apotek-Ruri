@extends('layouts.app')

@section('title', 'Laporan Resep')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="header-filter">
        <h2>Laporan Resep</h2>
    </div>

    <div class="filter-wrapper" style="display: flex; justify-content: space-between; align-items: center;">
        <button class="btn-filter-toggle" onclick="toggleFilter()">🔍 Filter</button>

        <div style="display: flex; gap: 10px;">
            <a href="#" onclick="downloadResepPDF()" class="btn btn-success">
                <i class="fa fa-download"></i> Download PDF
            </a>
            <a id="printBtn" target="_blank" class="btn-filter" style="background-color:#3b82f6;">
                <i class="fa fa-print"></i> Cetak
            </a>
        </div>
    </div>

    <form id="filterForm" class="filter-dropdown">
        @csrf
        <h4>Filter Resep</h4>

        <div class="form-group">
            <label>Nama Pasien</label>
            <input type="text" name="nama_pasien" placeholder="Cari nama pasien">
        </div>

        <div class="form-group">
            <label>Nama Dokter</label>
            <input type="text" name="nama_dokter" placeholder="Cari nama dokter">
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label for="tanggal_dari">Tanggal Dari</label>
                <input type="date" name="tanggal_dari">
            </div>
            <div class="form-group">
                <label for="tanggal_sampai">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai">
            </div>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter">Terapkan</button>
            <button type="button" class="btn-filter" onclick="resetFilter()" style="background-color:#ef4444;">Reset</button>
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Obat</th>
                </tr>
            </thead>
            <tbody id="resepTable">
                @foreach ($reseps as $resep)
                <tr>
                    <td>{{ $resep->nama_pasien }}</td>
                    <td>{{ $resep->dokter->nama_lengkap ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($resep->tanggal_resep)->format('d M Y') }}</td>
                    <td style="text-transform: capitalize;">{{ $resep->status }}</td>
                    <td>
                        <ul>
                            @foreach ($resep->detail as $item)
                                <li>{{ $item->obat->nama_obat ?? '-' }} ({{ $item->jumlah }})</li>
                            @endforeach
                        </ul>
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

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = new FormData(this);

        fetch("{{ route('pemilik.resep.filter') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': data.get('_token') },
            body: data
        })
        .then(res => res.text())
        .then(html => document.getElementById('resepTable').innerHTML = html);
    });

    function resetFilter() {
        document.getElementById('filterForm').reset();
        document.getElementById('filterForm').dispatchEvent(new Event('submit'));
    }

    document.getElementById('printBtn').addEventListener('click', function () {
        const params = new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString();
        this.href = "{{ route('pemilik.resep.print') }}?" + params;
    });

    function downloadResepPDF() {
        const form = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams();

        for (let [key, value] of form.entries()) {
            if (value) params.append(key, value);
        }

        window.open("{{ route('pemilik.resep.download') }}?" + params.toString(), '_blank');
    }
</script>
@endsection
