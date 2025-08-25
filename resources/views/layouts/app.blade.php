@php
    $role = Auth::guard('pengguna')->user()->role;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard {{ ucfirst($role) }}</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-apoteker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crud-obat.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CDN Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body>
    <div class="sidebar">
        <!-- Logo di atas menu -->
        <div class="logo-container">
            <img src="{{ asset('image/logo.jpg') }}" alt="Logo" class="logo">
        </div>

        <h3>Menu</h3>
        <ul>
            @if($role == 'apoteker')
                <li><a href="{{ route('apoteker.dashboard') }}" class="{{ request()->routeIs('apoteker.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('obat.index') }}" class="{{ request()->routeIs('obat.index') ? 'active' : '' }}">Kelola Obat</a></li>
                <li><a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.index') ? 'active' : '' }}">Transaksi</a></li>
                <li><a href="{{ route('apoteker.resep.index') }}" class="{{ request()->routeIs('apoteker.resep.index') ? 'active' : '' }}">Permintaan Resep</a></li>
                <li><a href="{{ route('pengguna.index') }}" class="{{ request()->routeIs('pengguna.index') ? 'active' : '' }}">Kelola Pengguna</a></li>
            @elseif($role == 'dokter')
                <li><a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('resep.index') }}" class="{{ request()->routeIs('resep.index') ? 'active' : '' }}">Buat Resep</a></li>
            @elseif($role == 'pemilik')
                <li><a href="{{ route('pemilik.dashboard') }}" class="{{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('pemilik.resep.index') }}" class="{{ request()->routeIs('pemilik.resep.index') ? 'active' : '' }}">Laporan Resep</a></li>
                <li><a href="{{ route('pemilik.transaksi.index') }}" class="{{ request()->routeIs('pemilik.transaksi.index') ? 'active' : '' }}">Laporan Transaksi</a></li>
                <li><a href="{{ route('pemilik.obat.index') }}" class="{{ request()->routeIs('pemilik.obat.index') ? 'active' : '' }}">Laporan Obat</a></li>
                <li><a href="{{ route('pemilik.pengguna.index') }}" class="{{ request()->routeIs('pemilik.pengguna.index') ? 'active' : '' }}">Data Pengguna</a></li>
            @endif

            <li>
                <form method="POST" action="{{ route('pengguna.logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <div class="content">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
