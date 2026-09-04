@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<style>
    .producto-detalle-page { max-width: 620px; margin: 0 auto; }
    .producto-detalle-card { background: #fff; border-radius: 16px; box-shadow: 0 3px 20px rgba(33, 45, 70, .1); border: 1px solid #eef0f4; padding: 24px; }
    .producto-detalle-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .producto-detalle-header h4 { margin: 0; font-weight: 700; color: #15233d; }
    .producto-detalle-close { color: #8e9bb0; font-size: 20px; text-decoration: none; }
    .producto-detalle-close:hover { color: #15233d; }
    .producto-detalle-imagen { height: 220px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 80px; background: linear-gradient(135deg, #fbd6c7, #fdeec2); margin-bottom: 18px; overflow: hidden; }
    .producto-detalle-imagen img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .producto-detalle-tiles { display: grid; grid-template-columns: 1fr; gap: 14px; margin-bottom: 16px; }
    .producto-detalle-tile { background: #f8f9fb; border-radius: 10px; padding: 14px; }
    .producto-detalle-tile span { display: block; color: #8e9bb0; font-size: .78rem; margin-bottom: 4px; }
    .producto-detalle-tile strong { color: #15233d; font-size: 1.15rem; font-weight: 800; }
    .producto-detalle-estado { display: flex; align-items: center; gap: 8px; color: #52617a; font-weight: 600; }
    .producto-stock-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 4px 12px; font-size: .8rem; font-weight: 700; }
    .producto-stock-badge i { font-size: 7px; }
    .producto-stock-badge--optimo { background: #dcfce7; color: #15733a; }
    .producto-stock-badge--bajo { background: #fff5c2; color: #955b00; }
    .producto-stock-badge--critico { background: #fee2e2; color: #b91c1c; }
</style>

@php
    $nombreCategoria = strtolower($producto->categoria->nombre ?? '');
    $emoji = match(true) {
        str_contains($nombreCategoria, 'vainilla') => '🎂',
        str_contains($nombreCategoria, 'chocolate') => '🍫',
        str_contains($nombreCategoria, 'naranja') => '🍊',
        str_contains($nombreCategoria, 'zanahoria') => '🥕',
        default => '🧁',
    };

@endphp

<div class="producto-detalle-page">
    <div class="producto-detalle-card">
        <div class="producto-detalle-header">
            <h4><i class="fas fa-cookie-bite"></i> Detalle del Producto</h4>
            <a href="{{ route('productos.index') }}" class="producto-detalle-close"><i class="fas fa-times"></i></a>
        </div>

        <div class="producto-detalle-imagen">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
            @else
                {{ $emoji }}
            @endif
        </div>

        <div class="producto-detalle-tiles">
            <div class="producto-detalle-tile">
                <span>Precio Unitario</span>
                <strong>Bs {{ number_format($producto->precio_venta, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="producto-detalle-estado">
            Receta:
            <span class="producto-stock-badge {{ $producto->insumos->isNotEmpty() ? 'producto-stock-badge--optimo' : 'producto-stock-badge--bajo' }}"><i class="fas fa-circle"></i> {{ $producto->insumos->isNotEmpty() ? 'Configurada' : 'Pendiente' }}</span>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Información</h5></div>
                <div class="card-body">
                    <p><strong>Sabor:</strong> {{ $producto->categoria->nombre }}</p>
                    <p><strong>Descripción:</strong> {{ $producto->descripcion ?? 'N/A' }}</p>
                    <p><strong>Costo Producción:</strong> Bs {{ number_format($producto->costo_produccion, 0, ',', '.') }}</p>
                    <p><strong>Ganancia:</strong> Bs {{ number_format($producto->calcularGanancia(), 0, ',', '.') }}</p>
                    <p class="mb-0"><strong>Costo de receta:</strong> Bs {{ number_format($producto->costo_produccion, 2, ',', '.') }}</p>
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
                        <small class="text-muted">Usa: {{ $insumo->pivot->cantidad_necesaria }} {{ $insumo->unidad }}</small>
                    </div>
                    @empty
                    <p class="text-muted mb-2">No hay insumos asociados a este producto.</p>
                    @endforelse
                    @if($producto->insumos->isEmpty())
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-list-check"></i> Configurar receta</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <form method="POST" action="{{ route('productos.destroy', $producto) }}" onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Eliminar</button>
        </form>
    </div>
</div>
@endsection
