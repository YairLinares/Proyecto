<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Delicias Dulces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #c7436f;
            --secondary-color: #f5e6e0;
            --dark-color: #5a3d4f;
        }

        body {
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--primary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }

        .register-header {
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .register-header i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .register-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .register-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .register-form {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(199, 67, 111, 0.25);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-register:hover {
            background-color: #b02d5f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(199, 67, 111, 0.3);
        }

        .register-footer {
            text-align: center;
            padding: 20px 40px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }

        .register-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .alert {
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .register-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-cupcake"></i>
            <h1>Delicias Dulces</h1>
            <p>Crea una nueva cuenta</p>
        </div>

        <div class="register-form">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                               name="apellido" value="{{ old('apellido') }}" required>
                        @error('apellido')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="{{ old('telefono') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ciudad</label>
                        <input type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}">
                    </div>
                </div>

                <button type="submit" class="btn-register">Crear Cuenta</button>
            </form>
        </div>

        <div class="register-footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>