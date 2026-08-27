@extends('layouts.app')

@section('title', 'Pedido ' . $pedido->numero_pedido)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title mb-1">Pedido {{ $pedido->numero_pedido }}</h1>
        <span class="text-muted">Registrado el {{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Productos del pedido</h5>
                <span class="badge badge-{{ $pedido->estado == 'Pendiente' ? 'pending' : ($pedido->estado == 'En proceso' ? 'process' : ($pedido->estado == 'Completado' ? 'completed' : 'cancelled')) }}">{{ $pedido->estado }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedido->detalles as $detalle)
                                <tr>
                                    <td><strong>{{ $detalle->producto->nombre }}</strong></td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">Bs {{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                                    <td class="text-end">Bs {{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Este pedido no tiene productos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Observaciones</h5></div>
            <div class="card-body">
                <p class="mb-0">{{ $pedido->descripcion_especificaciones ?: 'Sin observaciones adicionales.' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Cliente</h5></div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ $pedido->cliente->nombre_completo }}</strong></p>
                <p class="mb-0 text-muted"><i class="fas fa-phone me-1"></i>{{ $pedido->telefono_contacto }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Resumen de pago</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>Bs {{ number_format($pedido->subtotal, 2, ',', '.') }}</span></div>
                @if($pedido->costo_envio > 0)
                    <div class="d-flex justify-content-between mb-2"><span>Envío</span><span>Bs {{ number_format($pedido->costo_envio, 2, ',', '.') }}</span></div>
                @endif
                @if($pedido->descuento > 0)
                    <div class="d-flex justify-content-between mb-2 text-success"><span>Descuento</span><span>- Bs {{ number_format($pedido->descuento, 2, ',', '.') }}</span></div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>Bs {{ number_format($pedido->total, 2, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between mt-3"><span>Anticipo</span><span>Bs {{ number_format($pedido->anticipo_recibido, 2, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between mt-2 text-danger fw-bold"><span>Saldo pendiente</span><span>Bs {{ number_format(max(0, $pedido->total - $pedido->anticipo_recibido), 2, ',', '.') }}</span></div>
                <hr>
                <p class="mb-0"><strong>Método:</strong> {{ $pedido->metodo_pago }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Entrega</h5></div>
            <div class="card-body">
                <p class="mb-2"><strong>Fecha:</strong> {{ $pedido->fecha_entrega->format('d/m/Y') }}</p>
                <p class="mb-0"><strong>Dirección:</strong> {{ $pedido->direccion_entrega }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
