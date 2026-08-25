
@extends('layouts.app')
 
@section('title', $pedido->numero_pedido)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $pedido->numero_pedido }}</h1>
    <div>
        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
 
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Cliente</h5></div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $pedido->cliente->nombre_completo }}</p>
                <p><strong>Email:</strong> {{ $pedido->cliente->email }}</p>
                <p><strong>Teléfono:</strong> {{ $pedido->cliente->telefono_principal }}</p>
            </div>
        </div>
    </div>
 
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Información del Pedido</h5></div>
            <div class="card-body">
                <p><strong>Estado:</strong> <span class="badge badge-{{ $pedido->estado == 'Pendiente' ? 'pending' : ($pedido->estado == 'En proceso' ? 'process' : ($pedido->estado == 'Completado' ? 'completed' : 'cancelled')) }}">{{ $pedido->estado }}</span></p>
                <p><strong>Fecha Pedido:</strong> {{ $pedido->fecha_pedido->format('d/m/Y') }}</p>
                <p><strong>Fecha Entrega:</strong> {{ $pedido->fecha_entrega->format('d/m/Y') }}</p>
                <p><strong>Prioridad:</strong> {{ $pedido->prioridad }}</p>
            </div>
        </div>
    </div>
</div>
 
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5>Productos</h5></div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pedido->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                            <td>${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No hay productos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Resumen Financiero</h5></div>
            <div class="card-body">
                <p><strong>Subtotal:</strong> ${{ number_format($pedido->subtotal, 0, ',', '.') }}</p>
                <p><strong>Descuento:</strong> -${{ number_format($pedido->descuento, 0, ',', '.') }}</p>
                <p><strong>Envío:</strong> +${{ number_format($pedido->costo_envio, 0, ',', '.') }}</p>
                <hr>
                <h5><strong>Total:</strong> ${{ number_format($pedido->total, 0, ',', '.') }}</h5>
                <p><strong>Anticipo Recibido:</strong> ${{ number_format($pedido->anticipo_recibido, 0, ',', '.') }}</p>
                <p><strong>Saldo Pendiente:</strong> ${{ number_format($pedido->getSaldoPendiente(), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
 
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Entrega</h5></div>
            <div class="card-body">
                <p><strong>Dirección:</strong> {{ $pedido->direccion_entrega }}</p>
                <p><strong>Teléfono Contacto:</strong> {{ $pedido->telefono_contacto }}</p>
                <p><strong>Método Pago:</strong> {{ $pedido->metodo_pago }}</p>
            </div>
        </div>
    </div>
</div>
 
@if($pedido->descripcion_especificaciones)
<div class="card mt-4">
    <div class="card-header"><h5>Especificaciones</h5></div>
    <div class="card-body">
        {{ $pedido->descripcion_especificaciones }}
    </div>
</div>
@endif
@endsection
 