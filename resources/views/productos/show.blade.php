@extends('layouts.app')
 
@section('title', $producto->nombre)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $producto->nombre }}</h1>
    <div>
        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
 
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Información</h5></div>
            <div class="card-body">
                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $producto->descripcion ?? 'N/A' }}</p>
                <p><strong>Precio Venta:</strong> ${{ number_format($producto->precio_venta, 0, ',', '.') }}</p>
                <p><strong>Costo Producción:</strong> ${{ number_format($producto->costo_produccion, 0, ',', '.') }}</p>
                <p><strong>Ganancia:</strong> ${{ number_format($producto->calcularGanancia(), 0, ',', '.') }}</p>
                <p><strong>Stock:</strong> {{ $producto->stock_disponible }} {{ $producto->unidad_medida }}</p>
                <p><strong>Stock Mínimo:</strong> {{ $producto->stock_minimo }}</p>
                <p><strong>Tiempo Preparación:</strong> {{ $producto->tiempo_preparacion_dias }} días</p>
            </div>
        </div>
    </div>
 
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Insumos Necesarios</h5></div>
            <div class="card-body">
                @forelse($producto->insumos as $insumo)
                <div class="mb-2 pb-2" style="border-bottom: 1px solid #eee;">
                    <strong>{{ $insumo->nombre }}</strong><br>
                    <small class="text-muted">Cantidad: {{ $insumo->pivot->cantidad_necesaria }} {{ $insumo->unidad }}</small>
                </div>
                @empty
                <p class="text-muted">No hay insumos asociados</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
 
 
================================
VISTA 4: insumos/index.blade.php
================================
 
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
 