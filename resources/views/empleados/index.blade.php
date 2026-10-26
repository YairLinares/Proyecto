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
</style>
@endsection

@section('content')
<div class="empleados-page">
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title"><i class="fas fa-user-group" aria-hidden="true"></i> Empleados registrados</h1>
            <p class="text-muted mb-0">Consulta las cuentas de empleados del sistema.</p>
        </div>
        <a href="{{ route('register') }}" class="btn btn-primary"><i class="fas fa-user-plus me-1" aria-hidden="true"></i> Nuevo empleado</a>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
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
@endsection
