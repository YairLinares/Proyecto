<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Delicias Dulces</title>
    <style>
        :root { --rosa: #c72d68; --texto: #493743; --borde: #ead7dc; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 24px 16px; display: grid; place-items: center; font-family: Arial, Helvetica, sans-serif; color: var(--texto); background: #fff4f2; background-image: radial-gradient(circle at 10% 15%, #ffe0d5 0 11%, transparent 11.2%), radial-gradient(circle at 92% 87%, #ffe5df 0 13%, transparent 13.2%); }
        .login-card { width: min(100%, 440px); padding: 30px; border: 1px solid var(--borde); border-radius: 18px; background: #fff; box-shadow: 0 10px 28px rgba(119, 65, 84, .09); }
        .brand { text-align: center; }
        .brand-icon { width: 74px; height: 74px; margin: 0 auto 12px; display: grid; place-items: center; border: 2px solid #ffb9a8; border-radius: 50%; background: #fff1e8; font-size: 38px; }
        h1 { margin: 0; color: #80334f; font-size: 27px; }
        .brand p { margin: 6px 0 0; color: #846b79; font-size: 14px; }
        h2 { margin: 28px 0 6px; text-align: center; font-size: 19px; }
        .intro { margin: 0 0 25px; text-align: center; color: #796c75; font-size: 14px; line-height: 1.5; }
        .field { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; }
        input { display: block; width: 100%; height: 46px; padding: 0 14px; border: 1px solid var(--borde); border-radius: 9px; outline: none; background: #fff; color: var(--texto); font: inherit; }
        input::placeholder { color: #a7969e; }
        input:focus { border-color: var(--rosa); box-shadow: 0 0 0 3px #fbe1e9; }
        input[aria-invalid="true"] { border-color: #bb3048; }
        .password-field { position: relative; }
        .password-field input { padding-right: 80px; }
        .show-password { position: absolute; top: 5px; right: 5px; min-width: 70px; height: 36px; border: 0; border-radius: 7px; background: #fff5f7; color: #a71e53; font-size: 12px; font-weight: 700; cursor: pointer; }
        .show-password:hover { background: #fce4ec; }
        .submit { display: block; width: 100%; min-height: 46px; margin-top: 8px; border: 0; border-radius: 9px; background: var(--rosa); color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .submit:hover { background: #a71e53; }
        .message { margin: 18px 0; padding: 11px 13px; border-radius: 8px; font-size: 13px; line-height: 1.4; }
        .message--error { border: 1px solid #f1bcc4; background: #fff0f2; color: #8d2638; }
        .message--success { border: 1px solid #b9e4c0; background: #effaf0; color: #246a34; }
        .field-error { margin: 6px 0 0; color: #a8243b; font-size: 13px; }
        .register-link { margin: 24px 0 0; text-align: center; color: #786b74; font-size: 14px; }
        .register-link a { color: #a71e53; font-weight: 700; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
        @media (max-width: 480px) { .login-card { padding: 24px 20px; } h1 { font-size: 24px; } }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand">
            <div class="brand-icon" aria-hidden="true">🍰</div>
            <h1>Delicias Dulces</h1>
            <p>Sistema de gestión</p>
        </div>

        <h2>¡Bienvenido de nuevo!</h2>
        <p class="intro">Ingresa tus datos para continuar.</p>

        @if(session('success'))
            <div class="message message--success" role="status">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="message message--error" role="alert">Revisa los datos e inténtalo de nuevo.</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" required autofocus>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <div class="password-field">
                    <input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" required>
                    <button class="show-password" type="button" aria-controls="password" aria-pressed="false">Mostrar</button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <button class="submit" type="submit">Iniciar sesión</button>
        </form>

        <p class="register-link">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </main>
    <script>
        const toggle = document.querySelector('.show-password');
        const password = document.getElementById('password');
        toggle.addEventListener('click', () => {
            const visible = password.type === 'password';
            password.type = visible ? 'text' : 'password';
            toggle.textContent = visible ? 'Ocultar' : 'Mostrar';
            toggle.setAttribute('aria-pressed', String(visible));
        });
    </script>
</body>
</html>
