<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: url('/image/bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
            backdrop-filter: blur(8px);
            background-color: rgba(0, 0, 0, 0.5);
        }

        .sidebar {
            width: 250px;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 2rem;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h3 {
            margin-top: 0;
            font-size: 22px;
            color: #2e7d32;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 1rem;
        }

        .sidebar ul li a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar ul li a:hover {
            text-decoration: underline;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            color: #fff;
        }

        .logout-link {
            display: block;
            margin-top: 2rem;
            color: red;
            text-decoration: none;
            font-size: 14px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            color: #333;
        }

        .logo {
            width: 100%;
            text-align: center;
            margin-bottom: 1rem;
        }

        .logo img {
            width: 100px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <div>
            <div class="logo">
                <img src="{{ asset('image/logo.jpg') }}" alt="Logo">
            </div>
            <h3>Menu {{ ucfirst(auth('pengguna')->user()->role) }}</h3>
            <ul>
                @if(auth('pengguna')->user()->role === 'admin')
                    <li><a href="#">Kelola Pengguna</a></li>
                    <li><a href="#">Lihat Laporan</a></li>
                    <li><a href="#">Manajemen Sistem</a></li>
                @elseif(auth('pengguna')->user()->role === 'apoteker')
                    <li><a href="#">Data Obat</a></li>
                    <li><a href="#">Stok Gudang</a></li>
                    <li><a href="#">Riwayat Permintaan Obat</a></li>
                @elseif(auth('pengguna')->user()->role === 'dokter')
                    <li><a href="#">Daftar Pasien</a></li>
                    <li><a href="#">Tulis Resep</a></li>
                    <li><a href="#">Riwayat Konsultasi</a></li>
                @endif
            </ul>
        </div>

        <div>
            <a href="{{ route('logout') }}" class="logout-link"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
            </form>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
</div>

</body>
</html>
