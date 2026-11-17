<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar perfil</title>
</head>
<body>
    <main>
        <h1>Editar perfil</h1>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <label for="name">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required>
            @error('name')
                <p role="alert">{{ $message }}</p>
            @enderror

            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
            @error('email')
                <p role="alert">{{ $message }}</p>
            @enderror

            <h2>Cambiar contraseña (opcional)</h2>
            <label for="current_password">Contraseña actual</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            @error('current_password')
                <p role="alert">{{ $message }}</p>
            @enderror

            <label for="password">Nueva contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
            @error('password')
                <p role="alert">{{ $message }}</p>
            @enderror

            <label for="password_confirmation">Confirmar nueva contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">

            <button type="submit">Guardar cambios</button>
        </form>

        <p><a href="{{ route('profile.show') }}">Cancelar</a></p>
    </main>
</body>
</html>
