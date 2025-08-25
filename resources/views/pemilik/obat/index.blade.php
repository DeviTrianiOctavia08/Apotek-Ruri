@extends('layouts.app')

@section('title', 'Kelola Obat')

@section('head')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="header-filter">
        <h2>Kelola Obat</h2>
    </div>

    <div class="filter-wrapper">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
            <button class="btn-filter-toggle" onclick="toggleFilter()">🔍 Filter</button>
            <div style="display: flex; gap: 10px;">
                <a href="#" onclick="downloadObatPDF()" class="btn btn-success" style="background-color: #10b981; color: white; padding: 8px 16px; border-radius: 5px;">
                    <i class="fa fa-download"></i> Download PDF
                </a>
                <a id="printBtn" target="_blank" class="btn-filter" style="background-color: #6b7280; color: white; padding: 8px 16px; border-radius: 5px;">
                    <i class="fa fa-print"></i> Cetak
                </a>
            </div>
        </div>
        <form id="filterForm" class="filter-dropdown">
            @csrf
            <h4>Filter Data Obat</h4>

            <div class="form-group">
                <label>Nama Obat</label>
                <input type="text" name="nama" placeholder="Masukkan nama obat">
            </div>

            <div class="form-group">
                <label>Stok</label>
                <select name="stok">
                <option value="">-- Semua --</option>
                <option value="habis" {{ request('stok') == 'habis' ? 'selected' : '' }}>Habis</option>
                <option value="hampir_habis" {{ request('stok') == 'hampir_habis' ? 'selected' : '' }}>Hampir Habis (≤ 10)</option>
                <option value="tersedia" {{ request('stok') == 'tersedia' ? 'selected' : '' }}>Tersedia (&gt; 10)</option>
            </select>
            </div>

            <div class="form-group">
                <label>Kadaluarsa</label>
                <select name="kadaluarsa">
                    <option value="">-- Semua --</option>
                    <option value="hampir">Hampir Kadaluarsa</option>
                    <option value="sudah">Sudah Kadaluarsa</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Terapkan</button>
                <button type="button" class="btn-filter" style="background-color: #ef4444;" onclick="resetFilter()">Reset</button>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Tgl Masuk</th>
                <th>Tgl Kadaluarsa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="obatTable">
            @foreach ($obats as $obat)
            @php
                $days = \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->diffInDays(now(), false);
            @endphp
            <tr>
                <td>{{ $obat->nama_obat }}</td>
                <td>{{ $obat->stok }}</td>
                <td>Rp{{ number_format($obat->harga, 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($obat->tanggal_masuk)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d M Y') }}</td>
                <td>
                    {{-- Gabungan Status Stok --}}
                    @if ($obat->stok == 0)
                        <span class="status-label danger">Habis</span>
                    @elseif ($obat->stok <= 10)
                        <span class="status-label warning">Hampir Habis</span>
                    @else
                        <span class="status-label success">Tersedia</span>
                    @endif

                    {{-- Gabungan Status Kadaluarsa --}}
                    @if ($days < 0 && $days >= -30)
                        <span class="status-label warning">Hampir Kadaluarsa</span>
                    @elseif ($days >= 0)
                        <span class="status-label danger">Kadaluarsa</span>
                    @else
                        <span class="status-label success">Aman</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection

@section('scripts')
<script>
    function toggleFilter() {
        const form = document.getElementById('filterForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }

    // Handle submit filter
    document.getElementById('filterForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const data = new FormData(this);

        fetch("{{ route('pemilik.obat.filter') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': data.get('_token') // Ambil token dari input form
            },
            body: data
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('obatTable').innerHTML = html;
        })
        .catch(err => console.error('Filter gagal:', err));
    });

    function resetFilter() {
        const form = document.getElementById('filterForm');
        form.reset();

        const data = new FormData(); 
        data.append('_token', document.querySelector('input[name="_token"]').value);

        fetch("{{ route('pemilik.obat.filter') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': data.get('_token')
            },
            body: data
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('obatTable').innerHTML = html;
        })
        .catch(err => console.error('Reset gagal:', err));
    }

    document.getElementById('printBtn').addEventListener('click', function () {
        const params = new URLSearchParams(new FormData(document.getElementById('filterForm'))).toString();
        this.href = "{{ route('pemilik.obat.print') }}?" + params;
    });

    function downloadObatPDF() {
        const form = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams();

        for (let [key, value] of form.entries()) {
            if (value) params.append(key, value);
        }

        window.open("{{ route('pemilik.obat.download') }}?" + params.toString(), '_blank');
    }
</script>
@endsection
