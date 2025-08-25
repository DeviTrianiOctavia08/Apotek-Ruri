<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Pengguna</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="auth"> 

    <div class="overlay"></div>

    <div class="container">
        <img src="/image/logo.jpg" alt="Logo" class="logo"> 
        <h2>Login Pengguna</h2>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('pengguna.login') }}">
            @csrf
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
