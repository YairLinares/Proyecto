@extends('layouts.app')

@section('title', 'Empleados registrados - Delicias Dulces')

@section('styles')
<style>
    .empleados-page { max-width: 1280px; margin: 0 auto; }
    .empleados-resumen { border: 1px solid #f0e6df; border-radius: 14px; background: #fff7f1; }
    .empleados-resumen strong { color: #a62450; font-size: 1.8rem; line-height: 1; }
    .empleados-card { border: 1px solid #f0e6df; border-radius: 14px; overflow: hidden; }
    .empleados-card th { color: #5f6470; background: #fff5ef; font-size: .8rem; white-space: nowrap; }
    .empleados-card td { vertical-align: middle; }
    .empleados-card .nombre { color: #550d25; font-weight: 700; }
    .empleados-card .dato-vacio { color: #858995; }
    #nuevoEmpleado { --bs-offcanvas-width: 650px; color: #202735; }
    #nuevoEmpleado .offcanvas-header { padding: 26px 24px; align-items: flex-start; }
    #nuevoEmpleado .offcanvas-header h2 { font-size: 1.7rem; font-weight: 750; }
    #nuevoEmpleado .empleado-titulo-icono { color: #780c2c; font-size: 2.2rem; }
    #nuevoEmpleado .offcanvas-body { padding: 4px 24px 24px; }
    #nuevoEmpleado .empleado-seccion { border: 1px solid #eee4de; border-radius: 8px; overflow: hidden; padding: 0 20px 20px; margin-bottom: 20px; min-width: 0; }
    #nuevoEmpleado legend { float: none; width: calc(100% + 40px); margin: 0 -20px 16px; padding: 14px 20px; background: #faf6f2; font-size: 1rem; font-weight: 700; }
    #nuevoEmpleado legend i { color: #780c2c; margin-right: 10px; }
    #nuevoEmpleado .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    #nuevoEmpleado .field--full { grid-column: 1 / -1; }
    #nuevoEmpleado .input-wrap { position: relative; }
    #nuevoEmpleado .input-wrap > i { position: absolute; top: 50%; left: 12px; transform: translateY(-50%); color: #8794a9; }
    #nuevoEmpleado .form-label { font-size: .9rem; font-weight: 600; }
    #nuevoEmpleado .form-control { padding-left: 36px; min-height: 44px; border-radius: 6px; font-size: .9rem; }
    #nuevoEmpleado .form-control[readonly] { background: #f1f2f4; color: #5f6875; }
    #nuevoEmpleado .password-wrap .form-control { padding-right: 42px; background-image: none; }
    #nuevoEmpleado .empleado-ver-password { position: absolute; right: 3px; top: 2px; bottom: 2px; width: 38px; background: transparent; border: 0; color: #737d8a; border-radius: 4px; }
    #nuevoEmpleado .empleado-ver-password:focus-visible { outline: 2px solid #780c2c; }
    #nuevoEmpleado .empleado-footer { flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; border-top: 1px solid #eee4de; background: white; }
    #nuevoEmpleado .empleado-footer .btn { padding: 11px 18px; }
    #nuevoEmpleado .invalid-feedback { display: block; }
    @media (max-width: 575px) { #nuevoEmpleado { --bs-offcanvas-width: 100vw; } #nuevoEmpleado .form-grid { grid-template-columns: 1fr; } #nuevoEmpleado .empleado-footer { flex-wrap: wrap; } #nuevoEmpleado .offcanvas-header h2 { font-size: 1.4rem; } }
</style>
@endsection

@section('content')
<div class="empleados-page">
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title"><i class="fas fa-user-group" aria-hidden="true"></i> Empleados registrados</h1>
            <p class="text-muted mb-0">Consulta las cuentas de empleados del sistema.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#nuevoEmpleado" aria-controls="nuevoEmpleado"><i class="fas fa-user-plus me-1" aria-hidden="true"></i> Nuevo empleado</button>
    </div>

    <div class="empleados-resumen d-inline-flex align-items-center gap-3 p-3 mb-4">
        <strong>{{ $empleadosTotal }}</strong>
        <span>Empleados registrados</span>
    </div>

    <div class="card empleados-card">
        <div class="card-body">
            <form method="GET" action="{{ route('empleados.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-6">
                    <label for="buscarEmpleado" class="form-label">Buscar empleado</label>
                    <input id="buscarEmpleado" name="buscar" type="search" class="form-control" value="{{ $buscar }}" placeholder="Nombre, usuario o correo">
                </div>
                <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Buscar</button></div>
                @if($buscar !== '')
                    <div class="col-auto"><a href="{{ route('empleados.index') }}" class="btn btn-outline-secondary">Limpiar</a></div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nombre completo</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Correo electrónico</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Ciudad</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Registrado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $empleado)
                            <tr>
                                <td class="nombre">{{ trim($empleado->nombre.' '.$empleado->apellido) ?: $empleado->name }}</td>
                                <td>{{ $empleado->username ?: '—' }}</td>
                                <td>{{ $empleado->email }}</td>
                                <td class="{{ $empleado->telefono ? '' : 'dato-vacio' }}">{{ $empleado->telefono ?: 'No registrado' }}</td>
                                <td class="{{ $empleado->ciudad ? '' : 'dato-vacio' }}">{{ $empleado->ciudad ?: 'No registrada' }}</td>
                                <td><span class="badge text-bg-light border">{{ $empleado->cargo }}</span></td>
                                <td>{{ $empleado->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('empleados.destroy', $empleado) }}" onsubmit="return confirm('¿Eliminar esta cuenta? Sus pedidos y movimientos se conservarán, pero dejarán de mostrar a este empleado como responsable. Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Eliminar cuenta de {{ trim($empleado->nombre.' '.$empleado->apellido) ?: $empleado->name }}">
                                            <i class="fas fa-trash-can me-1" aria-hidden="true"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    {{ $buscar !== '' ? 'No se encontraron empleados con esa búsqueda.' : 'Todavía no hay empleados registrados.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('components.pagination', ['paginator' => $empleados])
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="nuevoEmpleado" aria-labelledby="nuevoEmpleadoTitulo">
    <div class="offcanvas-header">
        <div class="d-flex gap-3">
            <i class="fas fa-user-plus empleado-titulo-icono" aria-hidden="true"></i>
            <div>
                <h2 class="h5 mb-1" id="nuevoEmpleadoTitulo">Nuevo empleado</h2>
                <p class="text-muted small mb-0">Completa los datos para crear una cuenta de empleado.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif
        @include('empleados.formulario')
    </div>
    <div class="empleado-footer">
        <span class="text-muted small"><span class="text-danger">*</span> Campos obligatorios</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
            <button type="submit" form="crearEmpleadoForm" class="btn btn-primary"><i class="fas fa-user-plus me-2" aria-hidden="true"></i>Crear empleado</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-password-target]').forEach(button => {
        const label = button.getAttribute('aria-label');
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordTarget);
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? label.replace('Mostrar', 'Ocultar') : label);
            button.querySelector('i').className = visible ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });
    const panelEmpleado = document.getElementById('nuevoEmpleado');
    panelEmpleado.addEventListener('shown.bs.offcanvas', () => {
        (panelEmpleado.querySelector('.is-invalid') || document.getElementById('nombre')).focus();
    });
    panelEmpleado.addEventListener('hidden.bs.offcanvas', () => {
        panelEmpleado.querySelectorAll('[data-password-target][aria-pressed="true"]').forEach(button => button.click());
    });
</script>
@if(request()->boolean('nuevo') || $errors->hasAny(['username', 'nombre', 'apellido', 'email', 'password', 'telefono', 'ciudad']))
<script>
    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('nuevoEmpleado')).show();
</script>
@endif
@endsection
