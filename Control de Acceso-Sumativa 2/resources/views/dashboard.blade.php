<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel</title>
</head>
<body>
    <main>
        <h1>Bienvenido, {{ auth()->user()->name }}</h1>
        <p>Has iniciado sesión correctamente.</p>
        <p><a href="{{ route('profile.show') }}">Mi perfil</a></p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </main>
</body>
</html>
