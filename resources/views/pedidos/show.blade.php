@extends('layouts.app')

@section('title', 'Pedido ' . $pedido->codigo_pedido)

@section('content')
@php
    $productosPedido = $pedido->detalles->map(fn ($detalle) => $detalle->producto->nombre)->implode(', ');
    $cantidadTotal = $pedido->detalles->sum('cantidad');
    $claseEstado = match ($pedido->estado) {
        'Pendiente' => 'pendiente',
        'En proceso' => 'proceso',
        'Completado' => 'completado',
        default => 'cancelado',
    };
@endphp

<style>
    .pedido-detail { max-width: 600px; margin: 22px auto; }
    .pedido-detail__card { overflow: hidden; border: 0; border-radius: 14px; box-shadow: 0 12px 32px rgba(28, 38, 59, .14); }
    .pedido-detail__header { display: flex; align-items: center; justify-content: space-between; padding: 19px 24px; border-bottom: 1px solid #edf0f4; }
    .pedido-detail__title { margin: 0; color: #15233d; font-size: 1.15rem; font-weight: 700; }
    .pedido-detail__close { color: #7a879b; font-size: 1.1rem; }
    .pedido-detail__grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .pedido-detail__field { min-height: 73px; padding: 14px; background: #f8f9fb; border-radius: 16px; }
    .pedido-detail__field span { display: block; color: #8a97ab; font-size: .8rem; }
    .pedido-detail__field strong { display: block; margin-top: 4px; color: #15233d; font-size: .95rem; }
    .pedido-detail__status { display: inline-block; border-radius: 999px; padding: 4px 11px; font-size: .8rem; font-weight: 600; }
    .pedido-detail__status--pendiente { background: #fff5c2; color: #955b00; }
    .pedido-detail__status--proceso { background: #dbeafe; color: #1d4ed8; }
    .pedido-detail__status--completado { background: #dcfce7; color: #15733a; }
    .pedido-detail__status--cancelado { background: #fee2e2; color: #b91c1c; }
    .pedido-detail__observations { padding: 15px; border: 1px solid #f8e69a; border-radius: 16px; background: #fffdee; }
    .pedido-detail__observations span { display: block; color: #9a7600; font-size: .8rem; }
    .pedido-detail__observations p { margin: 5px 0 0; color: #394861; }
    .pedido-detail__actions { display: flex; gap: 10px; padding: 18px 24px; border-top: 1px solid #edf0f4; }
    .pedido-detail__actions .btn { border-radius: 999px; }
    @media (max-width: 575.98px) { .pedido-detail { margin: 0; } .pedido-detail__card { border-radius: 0; } .pedido-detail__grid { grid-template-columns: 1fr; } }
</style>

<div class="pedido-detail">
    <div class="card pedido-detail__card">
        <div class="pedido-detail__header">
            <h1 class="pedido-detail__title"><i class="fas fa-clipboard-list me-2"></i>Detalle del Pedido</h1>
            <a href="{{ route('pedidos.index') }}" class="pedido-detail__close" title="Cerrar"><i class="fas fa-times"></i></a>
        </div>

        <div class="card-body p-4">
            <div class="pedido-detail__grid">
                <div class="pedido-detail__field"><span>Número de pedido</span><strong>{{ $pedido->codigo_pedido }}</strong></div>
                <div class="pedido-detail__field"><span>Cliente</span><strong>{{ $pedido->cliente->nombre_completo }}</strong></div>
                <div class="pedido-detail__field"><span>Fecha</span><strong>{{ $pedido->fecha_pedido->format('d/m/Y') }}</strong></div>
                <div class="pedido-detail__field"><span>Producto{{ $pedido->detalles->count() === 1 ? '' : 's' }}</span><strong>{{ $productosPedido ?: 'Sin productos' }}</strong></div>
                <div class="pedido-detail__field"><span>Cantidad total</span><strong>{{ $cantidadTotal }}</strong></div>
                <div class="pedido-detail__field"><span>Total</span><strong>Bs {{ number_format($pedido->total, 2, ',', '.') }}</strong></div>
            </div>

            <div class="mt-3"><span class="text-muted me-2">Estado:</span><span class="pedido-detail__status pedido-detail__status--{{ $claseEstado }}">{{ $pedido->estado }}</span></div>

            @if($pedido->descripcion_especificaciones)
                <div class="pedido-detail__observations mt-3">
                    <span><i class="fas fa-edit me-1"></i>Observaciones</span>
                    <p>{{ $pedido->descripcion_especificaciones }}</p>
                </div>
            @endif
        </div>

        <div class="pedido-detail__actions">
            <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-outline-secondary"><i class="fas fa-pen me-1"></i> Editar</a>
            @if($pedido->estado === 'Pendiente')
                <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('¿Cancelar este pedido?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Cancelar pedido</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
