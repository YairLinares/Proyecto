@extends('layouts.app')

@section('title', 'Clientes - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Lista de Clientes</h1>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Cliente
    </a>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Total Clientes</p>
                <h3 class="mb-0" style="color: #c7436f;">{{ $totalClientes }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Clientes VIP</p>
                <h3 class="mb-0" style="color: #dc3545;">{{ $clientesVIP }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Total en Compras</p>
                <h3 class="mb-0" style="color: #198754;">$ {{ number_format($totalCompras, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Búsqueda y Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Buscar cliente..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="todos">Todos los estados</option>
                    <option value="activo" @if($filter == 'activo') selected @endif>Activo</option>
                    <option value="inactivo" @if($filter == 'inactivo') selected @endif>Inactivo</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Clientes -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Pedidos</th>
                    <th>Total Compras</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                <tr>
                    <td><strong>{{ $cliente->nombre_completo }}</strong></td>
                    <td>{{ $cliente->telefono_principal }}</td>
                    <td>{{ $cliente->pedidos->count() }}</td>
                    <td>${{ number_format($cliente->total_compras, 0, ',', '.') }}</td>
                    <td>
                        @if($cliente->tipo_cliente == 'Corporativo')
                            <span class="badge" style="background-color: #c7436f;">VIP</span>
                        @else
                            <span class="badge" style="background-color: #6c757d;">Regular</span>
                        @endif
                    </td>
                    <td>
                        @if($cliente->estado === 'activo')
                            <span class="badge text-bg-success">Activo</span>
                        @else
                            <span class="badge text-bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" style="display:inline;" 
                              onsubmit="return confirm('¿Estás seguro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay clientes registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $clientes->links() }}
    </div>
</div>
@endsection
