@extends('layouts.app')

@section('title', $categoria->nombre)

@section('content')
<div class="page-header">
    <h1 class="page-title">Sabor: {{ $categoria->nombre }}</h1>
    <div>
        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">Información</h5></div>
    <div class="card-body">
        <p class="mb-0">{{ $categoria->descripcion ?: 'Sin descripción' }}</p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Queques de este sabor ({{ $productos->total() }})</h5>
        <a href="{{ route('productos.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Nuevo
        </a>
    </div>
    <div class="card-body">
        @forelse($productos as $producto)
        <div class="row mb-3 pb-3 align-items-center" style="border-bottom: 1px solid #eee;">
            <div class="col-md-6">
                <strong>{{ $producto->nombre }}</strong><br>
                <small class="text-muted">Bs {{ number_format($producto->precio_venta, 2) }}</small>
            </div>
            <div class="col-md-3">
                <span class="badge {{ $producto->isStockBajo() ? 'badge-pending' : 'badge-completed' }}">
                    Stock: {{ $producto->stock_disponible }}
                </span>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
        @empty
        <p class="text-muted mb-0">Todavía no hay queques de este sabor</p>
        @endforelse
        {{ $productos->links() }}
    </div>
</div>
@endsection