@extends('layouts.app')

@section('title', 'Mi Perfil - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Mi Perfil</h1>
</div>
<p class="text-muted mb-4">Gestiona tu información personal y seguridad</p>

<div class="row">
    <!-- Tarjeta lateral con resumen -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                    {{ strtoupper(substr($user->nombre, 0, 1)) }}{{ strtoupper(substr($user->apellido, 0, 1)) }}
                </div>
                <h4 class="mb-1">{{ $user->nombre }} {{ $user->apellido }}</h4>
                <p class="text-muted">{{ $user->cargo }}</p>
                <span class="badge" style="background-color: #198754;">✓ Cuenta Activa</span>

                <hr>

                <div class="text-start">
                    <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                    @if($user->telefono)
                        <p><i class="fas fa-phone"></i> {{ $user->telefono }}</p>
                    @endif
                    @if($user->ciudad)
                        <p><i class="fas fa-map-marker-alt"></i> {{ $user->ciudad }}</p>
                    @endif
                    <p><i class="fas fa-clock"></i> Miembro desde {{ $user->created_at->translatedFormat('F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formularios -->
    <div class="col-md-8">
        <!-- Información Personal -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       name="nombre" value="{{ old('nombre', $user->nombre) }}" required>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                       name="apellido" value="{{ old('apellido', $user->apellido) }}" required>
                                @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" value="{{ old('telefono', $user->telefono) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control" name="ciudad" value="{{ old('ciudad', $user->ciudad) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cargo</label>
                        <select class="form-select" name="cargo">
                            <option value="Administradora" {{ $user->cargo == 'Administradora' ? 'selected' : '' }}>Administradora</option>
                            <option value="Administrador" {{ $user->cargo == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="Vendedor" {{ $user->cargo == 'Vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="Repostero" {{ $user->cargo == 'Repostero' ? 'selected' : '' }}>Repostero</option>
                            <option value="Usuario" {{ $user->cargo == 'Usuario' ? 'selected' : '' }}>Usuario</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Biografía</label>
                        <textarea class="form-control" name="biografia" rows="3" 
                                  placeholder="Cuéntanos sobre ti y tu rol en la pastelería...">{{ old('biografia', $user->biografia) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                        <button type="reset" class="btn btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control @error('contrasena_actual') is-invalid @enderror" 
                               name="contrasena_actual" required>
                        @error('contrasena_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control @error('nueva_contrasena') is-invalid @enderror" 
                               name="nueva_contrasena" required>
                        @error('nueva_contrasena')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" name="nueva_contrasena_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Actualizar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection