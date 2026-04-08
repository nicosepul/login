<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Nunito';
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-light bg-white shadow">
            <div class="container">
                @if (Auth::guard('user')->check())
                    <a class="navbar-brand" href="{{ url('/') }}">Inicio</a>
                    <div class="d-flex gap-3">
                        <a class="nav-link" href="{{ url('/registro') }}">Registro</a>
                        <a class="nav-link" href="{{ url('/buscador') }}">Buscador</a>
                        <a class="nav-link" href="{{ url('/registro-ingreso') }}">Nuevo Ingreso</a>
                        <a class="nav-link" href="{{ url('/ingresos') }}">Historial</a>
                        <a class="nav-link" href="{{ url('/usuarios') }}">Usuarios</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link p-0 align-baseline">Cerrar sesión</button>
                        </form>
                    </div>
                @else
                    <a class="navbar-brand" href="{{ url('/login') }}">Acceder</a>
                    <div class="d-flex gap-3">
                        <a class="nav-link" href="{{ route('registro-usuario') }}">Crear cuenta</a>
                    </div>
                @endif
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>

</html>
