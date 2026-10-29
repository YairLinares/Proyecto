            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="field--full">
                        <label class="form-label" for="username"><i class="fas fa-user-tag me-1"></i>Usuario</label>
                        <div class="input-wrap">
                            <i class="fas fa-user-tag"></i>
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" autocomplete="username" required>
                        </div>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="field--full">
                        <label class="form-label" for="rol"><i class="fas fa-user-shield me-1"></i>Rol</label>
                        <div class="input-wrap">
                            <i class="fas fa-user-shield"></i>
                            <input id="rol" type="text" class="form-control" value="Empleado" readonly aria-describedby="rolAyuda">
                        </div>
                        <div id="rolAyuda" class="form-text">Esta cuenta tendrá acceso de empleado.</div>
                    </div>
                    <div>
                        <label class="form-label" for="nombre"><i class="fas fa-user me-1"></i>Nombre</label>
                        <div class="input-wrap">
                            <i class="fas fa-user"></i>
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" autocomplete="given-name" required>
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
                        <label class="form-label" for="telefono"><i class="fas fa-mobile-screen-button me-1"></i>Celular del empleado <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-mobile-screen-button"></i>
                            <input id="telefono" type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}" autocomplete="tel" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" placeholder="8 dígitos" required>
                        </div>
                        @error('telefono')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="ciudad"><i class="fas fa-location-dot me-1"></i>Ciudad <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="fas fa-location-dot"></i>
                            <input id="ciudad" type="text" class="form-control @error('ciudad') is-invalid @enderror" name="ciudad" value="{{ old('ciudad') }}" autocomplete="address-level2" maxlength="100" required>
                        </div>
                        @error('ciudad')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                </div>

                <button type="submit" class="btn register-submit">Crear cuenta</button>
            </form>
