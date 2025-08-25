<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Obat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 40px;
        }

        .kop {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .kop img {
            width: 60px;
            height: auto;
            margin-right: 15px;
        }

        .kop .text {
            text-align: left;
        }

        .kop .text h2 {
            margin: 0;
            font-size: 20px;
        }

        .kop .text p {
            margin: 2px 0;
            font-size: 12px;
        }

        .judul {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .filter-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }

        hr {
            margin: 15px 0;
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            font-size: 12px;
        }

        .ttd-kiri,
        .ttd-kanan {
            width: 40%;
            text-align: center;
        }


    </style>
</head>
<body>

<div class="kop">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('image/logo.jpg'))) }}" alt="Logo Apotek" width="80">
    <div class="text">
        <h2>Apotek Ruri</h2>
        <p>Jl. Gempol Sari No. 4, Bandung</p>
        <p>Telp: 021-12345678 | Email: apotekruri@email.com</p>
    </div>
</div>

<hr>

<h3 class="judul">Laporan Obat</h3>

{{-- Filter Info --}}
@if (request('tanggal_dari') && request('tanggal_sampai'))
    <p class="filter-info">
        Tanggal: {{ \Carbon\Carbon::parse(request('tanggal_dari'))->translatedFormat('d M Y') }}
        – {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->translatedFormat('d M Y') }}
    </p>
@endif

<table>
    <thead>
        <tr>
            <th>Nama Obat</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Tgl Masuk</th>
            <th>Tgl Kadaluarsa</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($obats as $obat)
        <tr>
            <td>{{ $obat->nama_obat }}</td>
            <td>{{ $obat->stok }}</td>
            <td>Rp{{ number_format($obat->harga, 0, ',', '.') }}</td>
            <td>{{ \Carbon\Carbon::parse($obat->tanggal_masuk)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($obat->tanggal_kadaluarsa)->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width: 100%; margin-top: 80px; font-size: 12px;">
    <tr>
        <td style="width: 50%; text-align: center;">
            Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}<br>
            Pemilik<br><br><br><br><br>
            (____________________)
        </td>
        <td style="width: 50%; text-align: center;">
            <br>
            Apoteker<br><br><br><br><br>
            (____________________)
        </td>
    </tr>
</table>

</body>
</html>
