<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Delicias Dulces</title>
    <style>
        @font-face { font-family: "Lobster"; src: url("{{ asset('fonts/Lobster-Regular.ttf') }}") format("truetype"); font-style: normal; font-weight: 400; font-display: swap; }
        :root { --naranja: #ff701c; --rojo: #c91639; --texto: #3d211e; --suave: #806b6a; --borde: #e9c9c4; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; min-height: 100svh; margin: 0; padding: 28px 16px; display: grid; grid-template-columns: minmax(0, 1fr); place-items: center; position: relative; isolation: isolate; overflow-x: hidden; font-family: "Segoe UI", Arial, sans-serif; color: var(--texto); background: #fffaf5; }
        body::before, body::after { content: ""; position: fixed; z-index: -1; pointer-events: none; }
        body::before { top: -235px; left: -180px; width: min(82vw, 960px); height: 455px; border-radius: 40% 60% 60% 45%; transform: rotate(-13deg); background: #ffdab7; }
        body::after { right: -220px; bottom: -230px; width: min(78vw, 880px); height: 470px; border-radius: 58% 44% 40% 65%; transform: rotate(-20deg); background: #ffe0c1; box-shadow: -95px -54px 0 20px #ffefdf; }
        .login-card { width: 100%; max-width: 650px; min-width: 0; padding: 42px 53px 32px; border: 1px solid #fff0e8; border-radius: 32px; background: #fff; box-shadow: 0 20px 50px rgba(133, 65, 33, .16); }
        .brand { text-align: center; }
        .brand-logo { display: block; width: 190px; height: 190px; margin: 0 auto 6px; object-fit: contain; }
        .brand-placeholder { width: 110px; height: 110px; margin: 0 auto 15px; display: grid; place-items: center; border: 4px solid var(--naranja); border-radius: 50%; color: var(--rojo); font: 700 38px Georgia, serif; }
        h1 { margin: 0; color: #bf0028; font-family: "Lobster", "Brush Script MT", cursive; font-size: clamp(40px, 8vw, 58px); font-weight: 400; line-height: 1.16; }
        .brand p { display: flex; align-items: center; justify-content: center; gap: 15px; margin: 2px 0 0; color: var(--suave); font-size: 17px; }
        .brand p::before, .brand p::after { content: ""; width: 52px; border-top: 1px solid #ffc7a4; }
        h2 { margin: 34px 0 7px; text-align: center; font-size: 28px; line-height: 1.25; }
        .intro { margin: 0 0 31px; text-align: center; color: var(--suave); font-size: 17px; line-height: 1.5; }
        .field { margin-bottom: 21px; }
        label { display: block; margin-bottom: 9px; font-size: 15px; font-weight: 700; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; top: 50%; left: 17px; width: 20px; height: 20px; color: #8e7b7b; transform: translateY(-50%); pointer-events: none; }
        .field input { display: block; width: 100%; height: 58px; padding: 0 17px 0 49px; border: 1px solid var(--borde); border-radius: 12px; outline: none; background: #fff; color: var(--texto); font: inherit; }
        .field input::placeholder { color: #a99596; }
        .field input:focus { border-color: var(--naranja); box-shadow: 0 0 0 3px #ffeadc; }
        .field input[aria-invalid="true"] { border-color: var(--rojo); }
        .password-field input { padding-right: 58px; }
        .show-password { position: absolute; top: 7px; right: 6px; width: 44px; height: 44px; display: grid; place-items: center; border: 0; border-radius: 9px; background: transparent; color: #8e7b7b; cursor: pointer; }
        .show-password:hover, .show-password:focus-visible { background: #fff0e6; color: var(--rojo); }
        .show-password svg { width: 22px; height: 22px; }
        .show-password[aria-pressed="true"] .eye-open, .show-password[aria-pressed="false"] .eye-closed { display: none; }
        .form-options { display: flex; align-items: center; justify-content: space-between; margin: 2px 0 26px; }
        .remember { display: inline-flex; align-items: center; gap: 9px; margin: 0; font-size: 14px; font-weight: 500; cursor: pointer; }
        .remember input { width: 18px; height: 18px; margin: 0; accent-color: var(--naranja); cursor: pointer; }
        .submit { display: flex; align-items: center; justify-content: center; gap: 16px; width: 100%; min-height: 58px; border: 0; border-radius: 12px; background: linear-gradient(105deg, #ff8429, #d0173c); box-shadow: 0 10px 20px rgba(211, 55, 37, .19); color: #fff; font: inherit; font-size: 20px; font-weight: 700; cursor: pointer; }
        .submit span { font-size: 29px; line-height: 1; font-weight: 400; }
        .submit:hover { filter: brightness(.95); }
        .submit:focus-visible, a:focus-visible, .remember input:focus-visible { outline: 3px solid #ffac72; outline-offset: 3px; }
        .message { margin: 18px 0; padding: 11px 13px; border-radius: 8px; font-size: 13px; line-height: 1.4; }
        .message--error { border: 1px solid #f1bcc4; background: #fff0f2; color: #8d2638; }
        .message--success { border: 1px solid #b9e4c0; background: #effaf0; color: #246a34; }
        .field-error { margin: 6px 0 0; color: #a8243b; font-size: 13px; }
        .register-link { margin: 27px 0 0; padding-top: 22px; border-top: 1px solid #f2ded5; text-align: center; color: var(--suave); font-size: 14px; }
        .register-link a { color: var(--rojo); font-weight: 700; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
        @media (max-width: 600px) { body::before { top: -180px; left: -170px; width: 570px; height: 300px; } body::after { right: -190px; bottom: -150px; width: 480px; height: 290px; box-shadow: -60px -35px 0 5px #ffefdf; } .login-card { padding: 30px 24px 24px; border-radius: 22px; } .brand-logo { width: 135px; height: 135px; } h2 { margin-top: 28px; font-size: 23px; } .intro { font-size: 15px; } }
        @media (max-width: 380px) { .login-card { padding: 26px 18px 20px; } h1 { font-size: 31px; } }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand">
            @if(is_file(public_path('images/logo-delicias-dulces.png')))
                <img class="brand-logo" src="{{ asset('images/logo-delicias-dulces.png') }}" alt="Logo de Delicias Dulces" width="158" height="158">
            @else
                <div class="brand-placeholder" aria-hidden="true">DD</div>
            @endif
            <h1>Delicias Dulces</h1>
            <p>Sistema de gestión</p>
        </div>

        <h2>¡Bienvenido nuevamente!</h2>
        <p class="intro">Ingresa tus credenciales para acceder al sistema.</p>

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
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" required autofocus>
                </div>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <div class="input-wrap password-field">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    <input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" required>
                    <button class="show-password" type="button" aria-controls="password" aria-label="Mostrar contraseña" aria-pressed="false">
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 3 21 21M10.6 6.1A11 11 0 0 1 12 6c6.4 0 10 6 10 6a15 15 0 0 1-3.1 3.5M6.2 6.9C3.5 8.7 2 12 2 12s3.6 6 10 6c1.7 0 3.2-.4 4.4-1"/></svg>
                    </button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-options">
                <label class="remember" for="remember"><input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))> Recordarme</label>
            </div>
            <button class="submit" type="submit">Iniciar sesión <span aria-hidden="true">→</span></button>
        </form>

        <p class="register-link">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
    </main>
    <script>
        const toggle = document.querySelector('.show-password');
        const password = document.getElementById('password');
        toggle.addEventListener('click', () => {
            const visible = password.type === 'password';
            password.type = visible ? 'text' : 'password';
            toggle.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            toggle.setAttribute('aria-pressed', String(visible));
        });
    </script>
</body>
</html>
