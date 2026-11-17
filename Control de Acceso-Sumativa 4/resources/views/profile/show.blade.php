<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi perfil</title>
</head>
<body>
    <main>
        <h1>Mi perfil</h1>

        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif

        <dl>
            <dt>Nombre</dt>
            <dd>{{ $user->name }}</dd>
            <dt>Correo electrónico</dt>
            <dd>{{ $user->email }}</dd>
        </dl>

        <p><a href="{{ route('profile.edit') }}">Editar perfil</a></p>
        <p><a href="{{ route('dashboard') }}">Volver al panel</a></p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <label for="delete-password">Confirma tu contraseña para eliminar la cuenta</label>
            <input id="delete-password" name="password" type="password" autocomplete="current-password" required>
            @error('password')
                <p role="alert">{{ $message }}</p>
            @enderror
            <button type="submit">Eliminar mi cuenta</button>
        </form>
    </main>
</body>
</html>
