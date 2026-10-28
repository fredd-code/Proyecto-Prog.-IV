<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 1rem; background: #f3f4f6; font-family: Arial, sans-serif; color: #111827; }
        main { width: 100%; max-width: 24rem; padding: 2rem; background: #fff; border-radius: .75rem; box-shadow: 0 10px 25px rgb(0 0 0 / 8%); }
        h1 { margin: 0 0 1.5rem; text-align: center; }
        label { display: block; margin: 1rem 0 .4rem; font-weight: 600; }
        input[type="email"], input[type="password"] { width: 100%; padding: .75rem; border: 1px solid #d1d5db; border-radius: .375rem; }
        .remember { display: flex; align-items: center; gap: .5rem; margin: 1rem 0; }
        .remember label { margin: 0; font-weight: 400; }
        button { width: 100%; padding: .75rem; border: 0; border-radius: .375rem; background: #2563eb; color: white; font-size: 1rem; cursor: pointer; }
        .error { margin: .4rem 0 0; color: #b91c1c; font-size: .875rem; }
    </style>
</head>
<body>
    <main>
        <h1>Iniciar sesión</h1>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')
                <p class="error" role="alert">{{ $message }}</p>
            @enderror

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password')
                <p class="error" role="alert">{{ $message }}</p>
            @enderror

            <div class="remember">
                <input id="remember" name="remember" type="checkbox" value="1">
                <label for="remember">Recordarme</label>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p>¿No tienes cuenta? <a href="{{ route('register') }}">Crear cuenta</a></p>
    </main>
</body>
</html>
