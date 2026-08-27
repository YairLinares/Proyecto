<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - Delicias Dulces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --pink: #e91e63; --ink: #15233d; --muted: #8794a9; --line: #dde2e9; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #fff8fc; color: var(--ink); font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
        .register-card { width: min(100%, 640px); border: 1px solid #eef0f4; border-radius: 8px; background: #fff; box-shadow: 0 12px 32px rgba(44, 55, 78, .11); overflow: hidden; }
        .register-brand { padding: 30px 32px 24px; border-bottom: 1px solid #eef0f4; text-align: center; }
        .register-brand__icon { display: inline-grid; width: 68px; height: 68px; place-items: center; border-radius: 20px; background: #fff0c7; color: var(--pink); font-size: 1.8rem; }
        .register-brand h1 { margin: 14px 0 4px; color: var(--ink); font-size: 1.55rem; font-weight: 700; }
        .register-brand p { margin: 0; color: var(--muted); font-size: .92rem; }
        .register-form { padding: 28px 32px 32px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field--full { grid-column: 1 / -1; }
        .form-label { margin-bottom: 7px; color: var(--ink); font-size: .9rem; font-weight: 600; }
        .input-wrap { position: relative; }
        .input-wrap > i { position: absolute; top: 50%; left: 15px; color: #9aa6b8; transform: translateY(-50%); }
        .form-control { min-height: 47px; padding-left: 42px; border-color: var(--line); border-radius: 999px; }
        .form-control:focus { border-color: var(--pink); box-shadow: 0 0 0 .2rem rgba(233, 30, 99, .12); }
        .register-submit { width: 100%; min-height: 48px; margin-top: 26px; border: 0; border-radius: 999px; background: var(--pink); box-shadow: 0 5px 12px rgba(233, 30, 99, .22); color: #fff; font-weight: 700; }
        .register-submit:hover { background: #c91853; color: #fff; }
        .register-footer { padding: 18px 32px; border-top: 1px solid #eef0f4; color: var(--muted); font-size: .88rem; text-align: center; }
        .register-footer a { color: var(--pink); font-weight: 700; text-decoration: none; }
        .register-footer a:hover { text-decoration: underline; }
        .alert { border-radius: 8px; font-size: .9rem; }
        .invalid-feedback { margin-left: 12px; }
        @media (max-width: 575px) { body { padding: 16px; } .register-brand, .register-form { padding-right: 20px; padding-left: 20px; } .register-footer { padding-right: 20px; padding-left: 20px; } .form-grid { grid-template-columns: 1fr; gap: 14px; } .field--full { grid-column: auto; } }
    </style>
</head>
<body>
    <main class="register-card">
        <header class="register-brand">
            <div class="register-brand__icon"><i class="fas fa-birthday-cake"></i></div>
            <h1>Delicias Dulces</h1>
            <p>Crea una nueva cuenta</p>
        </header>

        <section class="register-form">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-grid">
                    <div>
                        <label class="form-label" for="nombre"><i class="fas fa-user me-1"></i>Nombre</label>
                        <div class="input-wrap">
                            <i class="fas fa-user"></i>
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" autocomplete="given-name" required autofocus>
                        </div>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="apellido"><i class="fas fa-user me-1"></i>Apellido</label>
                        <div class="input-wrap">
                            <i class="fas fa-user"></i>
                            <input id="apellido" type="text" class="form-control @error('apellido') is-invalid @enderror" name="apellido" value="{{ old('apellido') }}" autocomplete="family-name" required>
                        </div>
                        @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="field--full">
                        <label class="form-label" for="email"><i class="fas fa-envelope me-1"></i>Correo electr&oacute;nico</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required>
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="password"><i class="fas fa-lock me-1"></i>Contrase&ntilde;a</label>
                        <div class="input-wrap">
                            <i class="fas fa-key"></i>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" required>
                        </div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation"><i class="fas fa-lock me-1"></i>Confirmar contrase&ntilde;a</label>
                        <div class="input-wrap">
                            <i class="fas fa-key"></i>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="telefono"><i class="fas fa-phone me-1"></i>Tel&eacute;fono</label>
                        <div class="input-wrap">
                            <i class="fas fa-phone"></i>
                            <input id="telefono" type="tel" class="form-control" name="telefono" value="{{ old('telefono') }}" autocomplete="tel">
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="ciudad"><i class="fas fa-location-dot me-1"></i>Ciudad</label>
                        <div class="input-wrap">
                            <i class="fas fa-location-dot"></i>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" autocomplete="address-level2">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn register-submit">Crear cuenta</button>
            </form>
        </section>

        <footer class="register-footer">&iquest;Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesi&oacute;n</a></footer>
    </main>
</body>
</html>
