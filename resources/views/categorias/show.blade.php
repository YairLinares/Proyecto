
@section('title', $categoria->nombre)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $categoria->nombre }}</h1>
    <div>
        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
 
<div class="card">
    <div class="card-header"><h5>Información</h5></div>
    <div class="card-body">
        <p><strong>Descripción:</strong></p>
        <p>{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
    </div>
</div>
 
<div class="card mt-4">
    <div class="card-header"><h5>Productos ({{ $productos->total() }})</h5></div>
    <div class="card-body">
        @forelse($productos as $producto)
        <div class="row mb-3 pb-3" style="border-bottom: 1px solid #eee;">
            <div class="col-md-8">
                <strong>{{ $producto->nombre }}</strong><br>
                <small class="text-muted">${{ number_format($producto->precio_venta, 0, ',', '.') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
            </div>
        </div>
        @empty
        <p class="text-muted">No hay productos en esta categoría</p>
        @endforelse
        {{ $productos->links() }}
    </div>
</div>
@endsection
 
 
================================
VISTA: pedidos/index.blade.php
================================
 
@extends('layouts.app')
 
@section('title', 'Pedidos')
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Gestión de Pedidos</h1>
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Pedido</a>
</div>
 
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Total Pedidos</p>
                <h3 class="mb-0">{{ $totalPedidos }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Activos</p>
                <h3 class="mb-0" style="color: #0dcaf0;">{{ $pedidosActivos }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Clientes Nuevos</p>
                <h3 class="mb-0">{{ $clientesNuevos }}</h3>
            </div>
        </div>
    </div>
</div>
 
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Buscar pedido o cliente..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="estado" class="form-select">
                    <option value="todos">Todos los estados</option>
                    <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="En proceso" {{ $estado == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="Completado" {{ $estado == 'Completado' ? 'selected' : '' }}>Completado</option>
                    <option value="Cancelado" {{ $estado == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Entrega</th>
                    <th>Total</th>
                    <th>Anticipo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                <tr>
                    <td><strong>{{ $pedido->numero_pedido }}</strong></td>
                    <td>{{ $pedido->cliente->nombre_completo }}</td>
                    <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                    <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
                    <td>${{ number_format($pedido->anticipo_recibido, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-{{ $pedido->estado == 'Pendiente' ? 'pending' : ($pedido->estado == 'En proceso' ? 'process' : ($pedido->estado == 'Completado' ? 'completed' : 'cancelled')) }}">
                            {{ $pedido->estado }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No hay pedidos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $pedidos->links() }}
    </div>
</div>
@endsection
 