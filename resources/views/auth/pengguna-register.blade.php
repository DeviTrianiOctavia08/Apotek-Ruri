<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <meta charset="UTF-8">
    <title>Register Pengguna</title>
</head>

<body class="auth">
    <div class="overlay"></div>

    <div class="container">
        <img src="/image/logo.jpg" alt="Logo" class="logo">
        <h2>Register Pengguna</h2>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('pengguna.register') }}">
            @csrf
            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
            <input type="tel" name="kontak" placeholder="Kontak" value="{{ old('kontak') }}" required>
            <textarea name="alamat" placeholder="Alamat" rows="3" required>{{ old('alamat') }}</textarea>
            <select name="role" required>
                <option value="">-- Pilih Peran --</option>
                <option value="apoteker" {{ old('role') == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>

</body>
</html>
