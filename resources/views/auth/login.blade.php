<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Delicias Dulces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --pink: #e91e63; --ink: #15233d; --muted: #8794a9; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #fff8fc; color: var(--ink); font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
        .login-card { width: min(100%, 450px); padding: 32px; border: 1px solid #eef0f4; border-radius: 8px; background: #fff; box-shadow: 0 12px 32px rgba(44, 55, 78, .11); }
        .login-brand { text-align: center; margin-bottom: 30px; }
        .login-brand__icon { display: inline-grid; width: 80px; height: 80px; place-items: center; border-radius: 22px; background: #fff0c7; color: #e91e63; font-size: 2rem; }
        .login-brand h1 { margin: 18px 0 4px; color: var(--ink); font-size: 1.55rem; font-weight: 700; }
        .login-brand p { margin: 0; color: var(--muted); font-size: .92rem; }
        .form-label { margin-bottom: 7px; color: var(--ink); font-size: .92rem; font-weight: 600; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; top: 50%; left: 15px; color: #9aa6b8; transform: translateY(-50%); }
        .form-control { min-height: 47px; padding-left: 42px; border-color: #dde2e9; border-radius: 999px; }
        .form-control:focus { border-color: var(--pink); box-shadow: 0 0 0 .2rem rgba(233, 30, 99, .12); }
        .login-submit { width: 100%; min-height: 47px; margin-top: 23px; border: 0; border-radius: 999px; background: var(--pink); box-shadow: 0 5px 12px rgba(233, 30, 99, .22); color: #fff; font-weight: 700; }
        .login-submit:hover { background: #c91853; color: #fff; }
        .login-footer { margin: 24px -32px -32px; padding: 18px 32px; border-top: 1px solid #eef0f4; color: var(--muted); font-size: .86rem; text-align: center; }
        .login-footer a { color: var(--pink); font-weight: 700; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }
        .alert { border-radius: 8px; font-size: .9rem; }
        .invalid-feedback { margin-left: 12px; }
        @media (max-width: 480px) { body { padding: 16px; } .login-card { padding: 26px 20px; } .login-footer { margin-right: -20px; margin-bottom: -26px; margin-left: -20px; padding-right: 20px; padding-left: 20px; } }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="login-brand">
            <div class="login-brand__icon"><i class="fas fa-birthday-cake"></i></div>
            <h1>Delicias Dulces</h1>
            <p>Sistema de gestión</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="email"><i class="fas fa-user me-1"></i>Correo electrónico</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="password"><i class="fas fa-lock me-1"></i>Contraseña</label>
                <div class="input-wrap">
                    <i class="fas fa-key"></i>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password" required>
                </div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn login-submit">Iniciar sesión</button>
        </form>

        <div class="login-footer">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></div>
    </main>
</body>
</html>
