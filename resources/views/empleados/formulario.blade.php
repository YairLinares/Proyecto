<form id="crearEmpleadoForm" method="POST" action="{{ route('usuarios.store') }}">
    @csrf
    <fieldset class="empleado-seccion">
        <legend><i class="fas fa-user" aria-hidden="true"></i> Datos personales</legend>
        <div class="form-grid">
            @foreach(['nombre' => ['Nombre', 'Ej. María', 'given-name'], 'apellido' => ['Apellido', 'Ej. Pérez', 'family-name']] as $campo => $datos)
                <div>
                    <label class="form-label" for="{{ $campo }}">{{ $datos[0] }} <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <input id="{{ $campo }}" type="text" class="form-control @error($campo) is-invalid @enderror" name="{{ $campo }}" value="{{ old($campo) }}" placeholder="{{ $datos[1] }}" autocomplete="{{ $datos[2] }}" maxlength="255" required>
                    </div>
                    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
            <div class="field--full">
                <label class="form-label" for="email">Correo electrónico <span class="text-danger">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" autocomplete="email" required>
                </div>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label" for="telefono">Celular del empleado <span class="text-danger">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-mobile-screen-button" aria-hidden="true"></i>
                    <input id="telefono" type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" autocomplete="tel" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" placeholder="Ej. 70000000" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label" for="ciudad">Ciudad <span class="text-danger">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-location-dot" aria-hidden="true"></i>
                    <input id="ciudad" type="text" class="form-control" name="ciudad" value="Santa Cruz de la Sierra" readonly required>
                </div>
                @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </fieldset>
    <fieldset class="empleado-seccion">
        <legend><i class="fas fa-lock" aria-hidden="true"></i> Datos de acceso</legend>
        <div class="form-grid">
            <div class="field--full">
                <label class="form-label" for="username">Nombre de usuario <span class="text-danger">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Ej. maria_perez" autocomplete="off" minlength="3" maxlength="30" required>
                </div>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="field--full">
                <label class="form-label" for="rol">Rol</label>
                <div class="input-wrap">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <input id="rol" type="text" class="form-control" value="Empleado" readonly aria-describedby="rolAyuda">
                </div>
                <div id="rolAyuda" class="form-text">Acceso asignado para las funciones del empleado.</div>
            </div>
            @foreach(['password' => 'Contraseña', 'password_confirmation' => 'Confirmar contraseña'] as $campo => $etiqueta)
                <div>
                    <label class="form-label" for="{{ $campo }}">{{ $etiqueta }} <span class="text-danger">*</span></label>
                    <div class="input-wrap password-wrap">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <input id="{{ $campo }}" type="password" class="form-control @error($campo) is-invalid @enderror" name="{{ $campo }}" autocomplete="new-password" minlength="8" @if($campo === 'password') aria-describedby="passwordAyuda" @endif required>
                        <button type="button" class="empleado-ver-password" data-password-target="{{ $campo }}" aria-label="Mostrar {{ mb_strtolower($etiqueta) }}" aria-pressed="false"><i class="fas fa-eye" aria-hidden="true"></i></button>
                    </div>
                    @if($campo === 'password')<div id="passwordAyuda" class="form-text">Usa al menos 8 caracteres.</div>@endif
                    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
        </div>
    </fieldset>
</form>