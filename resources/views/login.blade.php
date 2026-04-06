<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
    <div class="container py-5" style="max-width: 420px;">
        <h1 class="mb-4">Login</h1>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            @error('email')
                <div class="text-danger mb-3">{{ $message }}</div>
            @enderror
            <button class="btn btn-primary w-100" type="submit">Entrar</button>
        </form>
        <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 mt-2">Registrarse</a>
    </div>
</body>
</html>
