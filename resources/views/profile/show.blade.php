@extends('layouts.app')

@section('title', 'Mi Perfil - Delicias Dulces')

@section('content')
<style>
    .perfil-avatar-wrap { position: relative; width: 90px; height: 90px; margin: 0 auto; }
    .perfil-avatar { width: 90px; height: 90px; border-radius: 22px; background: linear-gradient(135deg, #fbe0cf, #f6c9de); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; color: #c7436f; }
    .perfil-avatar-cam { position: absolute; bottom: -6px; right: -6px; width: 30px; height: 30px; border-radius: 50%; background: var(--primary-color); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 3px solid #fff; }
    .perfil-badge-activa { display: inline-block; margin-top: 8px; padding: 4px 14px; border-radius: 20px; background: #dcfce7; color: #15733a; font-size: 13px; font-weight: 600; }
    .perfil-contacto p { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; color: #52617a; }
    .perfil-contacto i { color: var(--primary-color); width: 16px; }
    .perfil-cerrar-sesion button { color: #dc3545; background: none; border: none; font-weight: 600; }
    .pill-btn { border-radius: 999px; padding: 8px 18px; font-weight: 600; }
    .info-label { color: #8e9bb0; font-size: .78rem; text-transform: uppercase; letter-spacing: .02em; margin-bottom: 4px; }
    .info-value { color: #16233d; font-weight: 700; }
    .perfil-card-header { background: #f3d9c9 !important; border-bottom: 1px solid #ecc9b4; }
</style>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog"></i> Mi Perfil</h1>
</div>
<p class="text-muted mb-4">Administra tu información personal</p>

<div class="row">
    <!-- Tarjeta lateral con resumen -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="perfil-avatar-wrap mb-3">
                    <div class="perfil-avatar">
                        {{ strtoupper(substr($user->nombre, 0, 1)) }}{{ strtoupper(substr($user->apellido, 0, 1)) }}
                    </div>
                    <div class="perfil-avatar-cam"><i class="fas fa-camera"></i></div>
                </div>
                <h4 class="mb-1">{{ $user->nombre }} {{ $user->apellido }}</h4>
                <p class="text-muted mb-0">{{ $user->cargo }}</p>
                <span class="perfil-badge-activa"><i class="fas fa-check"></i> Cuenta Activa</span>

                <hr>

                <div class="text-start perfil-contacto">
                    <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                    @if($user->telefono)
                        <p><i class="fas fa-phone"></i> {{ $user->telefono }}</p>
                    @endif
                    @if($user->ciudad)
                        <p><i class="fas fa-map-marker-alt"></i> {{ $user->ciudad }}</p>
                    @endif
                    <p><i class="fas fa-store"></i> Delicias Dulces</p>
                </div>

                <hr>

                <div class="perfil-cerrar-sesion">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información y formularios -->
    <div class="col-md-8">
        <!-- Información Personal -->
        <div class="card">
            <div class="card-header perfil-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Información Personal</h5>
                <button type="button" class="btn btn-outline-secondary pill-btn btn-sm" id="botonEditarPerfil" onclick="alternarEdicionPerfil()">
                    <i class="fas fa-pen"></i> Editar Perfil
                </button>
            </div>
            <div class="card-body">
                @php
                    $tieneErroresPerfil = $errors->hasAny(['nombre', 'apellido', 'email']);
                @endphp
                <div id="vistaPerfil" style="{{ $tieneErroresPerfil ? 'display: none;' : '' }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Nombre</div>
                            <div class="info-value">{{ $user->nombre }} {{ $user->apellido }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Rol</div>
                            <div class="info-value">{{ $user->cargo }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Correo Electrónico</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value">{{ $user->telefono ?: '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Ciudad</div>
                            <div class="info-value">{{ $user->ciudad ?: '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Emprendimiento</div>
                            <div class="info-value">Delicias Dulces</div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de edición -->
                <div id="formularioPerfil" style="{{ $tieneErroresPerfil ? '' : 'display: none;' }}">
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

                        @if($user->esAdministrador())
                            <div class="mb-3">
                                <label class="form-label">Cargo</label>
                                <select class="form-select" name="cargo" required>
                                    <option value="Administrador" {{ old('cargo', $user->cargo) == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                    <option value="Empleado" {{ old('cargo', $user->cargo) == 'Empleado' ? 'selected' : '' }}>Empleado</option>
                                </select>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                            <button type="button" class="btn btn-secondary" onclick="alternarEdicionPerfil()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Seguridad -->
        <div class="card mt-4">
            <div class="card-header perfil-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-key" style="color: #d4a300;"></i> Seguridad</h5>
                <button type="button" class="btn btn-outline-secondary pill-btn btn-sm" id="botonCambiarPassword" onclick="alternarPassword()">
                    <i class="fas fa-lock"></i> Cambiar Contraseña
                </button>
            </div>
            <div class="card-body">
                <div id="vistaPassword">
                    <p class="text-muted mb-0">Actualiza tu contraseña regularmente para mayor seguridad</p>
                </div>

                <div id="formularioPassword" style="display: none;">
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

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Actualizar Contraseña</button>
                            <button type="button" class="btn btn-secondary" onclick="alternarPassword()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function alternarEdicionPerfil() {
        const vista = document.getElementById('vistaPerfil');
        const formulario = document.getElementById('formularioPerfil');
        const editando = formulario.style.display !== 'none';
        vista.style.display = editando ? 'block' : 'none';
        formulario.style.display = editando ? 'none' : 'block';
    }

    function alternarPassword() {
        const vista = document.getElementById('vistaPassword');
        const formulario = document.getElementById('formularioPassword');
        const editando = formulario.style.display !== 'none';
        vista.style.display = editando ? 'block' : 'none';
        formulario.style.display = editando ? 'none' : 'block';
    }

    @if ($errors->has('contrasena_actual') || $errors->has('nueva_contrasena'))
        alternarPassword();
    @endif
</script>
@endsection
