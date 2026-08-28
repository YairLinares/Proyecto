@extends('layouts.app')
 
@section('title', $insumo->nombre)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $insumo->nombre }}</h1>
    <div>
        <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
 
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Información</h5></div>
            <div class="card-body">
                <p><strong>Stock Actual:</strong> {{ $insumo->stock_actual }} {{ $insumo->unidad }}</p>
                <p><strong>Stock Mínimo:</strong> {{ $insumo->stock_minimo }}</p>
                <p><strong>Precio Unitario:</strong> ${{ number_format($insumo->precio_unitario, 0, ',', '.') }}</p>
                <p><strong>Estado:</strong> <span class="badge badge-{{ $insumo->estado == 'Normal' ? 'completed' : 'pending' }}">{{ $insumo->estado }}</span></p>
            </div>
        </div>
    </div>
 
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Productos que Utilizan este Insumo</h5></div>
            <div class="card-body">
                @forelse($productos as $producto)
                <div class="mb-2"><strong>{{ $producto->nombre }}</strong><br>
                <small class="text-muted">Cantidad: {{ $producto->pivot->cantidad_necesaria }} {{ $insumo->unidad }}</small></div>
                @empty
                <p class="text-muted">No hay productos asociados</p>
                @endforelse
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
 
