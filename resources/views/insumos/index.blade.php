@extends('layouts.app')

@section('title', 'Insumos - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Gestión de Insumos</h1>
    <a href="{{ route('insumos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Insumo</a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Total Insumos</p>
                <h3 class="mb-0">{{ $totalInsumos }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Stock Bajo</p>
                <h3 class="mb-0" style="color: #ffc107;">{{ $stockBajo }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Agotados</p>
                <h3 class="mb-0" style="color: #dc3545;">{{ $agotados }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Buscar insumo o proveedor..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="filter" class="form-select">
                    <option value="todos">Todos los estados</option>
                    <option value="Normal" @if($filter == 'Normal') selected @endif>Normal</option>
                    <option value="Stock bajo" @if($filter == 'Stock bajo') selected @endif>Stock bajo</option>
                    <option value="Agotado" @if($filter == 'Agotado') selected @endif>Agotado</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Stock Actual</th>
                    <th>Unidad</th>
                    <th>Precio Unitario</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($insumos as $insumo)
                <tr>
                    <td><strong>{{ $insumo->nombre }}</strong></td>
                    <td>{{ $insumo->stock_actual }}</td>
                    <td>{{ $insumo->unidad }}</td>
                    <td>${{ number_format($insumo->precio_unitario, 0, ',', '.') }}</td>
                    <td>{{ $insumo->proveedor }}</td>
                    <td>
                        <span class="badge {{ $insumo->estado == 'Normal' ? 'badge-completed' : ($insumo->estado == 'Stock bajo' ? 'badge-pending' : 'badge-cancelled') }}">
                            {{ $insumo->estado }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('insumos.destroy', $insumo) }}" style="display:inline;" onsubmit="return confirm('¿Seguro?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No hay insumos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $insumos->links() }}
    </div>
</div>
@endsection