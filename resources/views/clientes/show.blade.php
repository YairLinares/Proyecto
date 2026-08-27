@extends('layouts.app')

@section('title', $cliente->nombre_completo . ' - Delicias Dulces')

@section('content')
@php
    $iniciales = collect(preg_split('/\s+/', trim($cliente->nombre_completo)))
        ->take(2)
        ->map(fn ($nombre) => mb_strtoupper(mb_substr($nombre, 0, 1)))
        ->implode('');
@endphp

<style>
    .cliente-detail { max-width: 920px; margin: 12px auto; }
    .cliente-detail__card { overflow: hidden; border: 0; border-radius: 14px; box-shadow: 0 12px 32px rgba(28, 38, 59, .14); }
    .cliente-detail__header { display: flex; align-items: center; justify-content: space-between; padding: 19px 24px; border-bottom: 1px solid #edf0f4; }
    .cliente-detail__title { margin: 0; color: #15233d; font-size: 1.15rem; font-weight: 700; }
    .cliente-detail__close { color: #7a879b; font-size: 1.1rem; }
    .cliente-summary { display: flex; align-items: center; gap: 18px; padding: 22px; background: #fff2f8; border-radius: 14px; }
    .cliente-summary__initials { display: grid; width: 58px; height: 58px; place-items: center; flex: 0 0 58px; border-radius: 16px; background: #fde0ed; color: #e91e63; font-size: 1.25rem; font-weight: 700; }
    .cliente-summary h2 { margin: 0 0 6px; color: #15233d; font-size: 1.1rem; font-weight: 700; }
    .cliente-pill { display: inline-block; border-radius: 999px; padding: 3px 11px; font-size: .78rem; font-weight: 600; }
    .cliente-pill--regular { background: #edf4ff; color: #2563eb; }
    .cliente-pill--corporativo { background: #f5e8ff; color: #7e22ce; }
    .cliente-pill--activo { background: #dcfce7; color: #15733a; }
    .cliente-pill--inactivo { background: #eef0f3; color: #697586; }
    .cliente-info { display: grid; grid-template-columns: 1fr 1fr; column-gap: 30px; }
    .cliente-info__item { display: flex; gap: 13px; padding: 17px 0; border-bottom: 1px solid #edf0f4; }
    .cliente-info__item i { color: #e91e63; width: 16px; margin-top: 4px; }
    .cliente-info__label { display: block; color: #8a97ab; font-size: .8rem; }
    .cliente-info__value { color: #15233d; font-weight: 600; }
    .cliente-stat { padding: 15px; border: 1px solid #edf0f4; border-radius: 10px; background: #fbfcfe; text-align: center; }
    .cliente-stat strong { display: block; color: #e91e63; font-size: 1.2rem; }
    .cliente-stat span { color: #71809a; font-size: .8rem; }
    .cliente-orders { border-top: 1px solid #edf0f4; }
    .cliente-orders h3 { color: #15233d; font-size: 1rem; font-weight: 700; }
    @media (max-width: 767.98px) { .cliente-detail { margin: 0; } .cliente-info { grid-template-columns: 1fr; } .cliente-detail__card { border-radius: 0; } }
</style>

<div class="cliente-detail">
    <div class="card cliente-detail__card">
        <div class="cliente-detail__header">
            <h1 class="cliente-detail__title"><i class="fas fa-user me-2"></i>Detalle del Cliente</h1>
            <a href="{{ route('clientes.index') }}" class="cliente-detail__close" title="Cerrar"><i class="fas fa-times"></i></a>
        </div>

        <div class="card-body p-4">
            <div class="cliente-summary">
                <span class="cliente-summary__initials">{{ $iniciales }}</span>
                <div>
                    <h2>{{ $cliente->nombre_completo }}</h2>
                    <span class="cliente-pill {{ $cliente->tipo_cliente === 'Corporativo' ? 'cliente-pill--corporativo' : 'cliente-pill--regular' }}">{{ $cliente->tipo_cliente }}</span>
                    <span class="cliente-pill cliente-pill--{{ $cliente->estado }} ms-1">{{ ucfirst($cliente->estado) }}</span>
                </div>
            </div>

            <div class="cliente-info mt-2">
                <div class="cliente-info__item">
                    <i class="fas fa-phone"></i>
                    <div><span class="cliente-info__label">Teléfono</span><span class="cliente-info__value">{{ $cliente->telefono_principal }}</span></div>
                </div>
                <div class="cliente-info__item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div><span class="cliente-info__label">Dirección</span><span class="cliente-info__value">{{ $cliente->direccion }}</span></div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-4"><div class="cliente-stat"><strong>{{ $cliente->pedidos->count() }}</strong><span>Total de pedidos</span></div></div>
                <div class="col-md-4"><div class="cliente-stat"><strong>Bs {{ number_format($cliente->total_compras, 0, ',', '.') }}</strong><span>Total en compras</span></div></div>
                <div class="col-md-4"><div class="cliente-stat"><strong>{{ $cliente->created_at->format('d/m/Y') }}</strong><span>Cliente desde</span></div></div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary"><i class="fas fa-pen me-1"></i> Editar cliente</a>
            </div>
        </div>

        <div class="cliente-orders p-4">
            <h3 class="mb-3">Pedidos del cliente</h3>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Pedido</th><th>Fecha</th><th>Entrega</th><th>Total</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                            <tr>
                                <td><strong>{{ $pedido->numero_pedido }}</strong></td>
                                <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                                <td>Bs {{ number_format($pedido->total, 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $pedido->estado == 'Pendiente' ? 'pending' : ($pedido->estado == 'En proceso' ? 'process' : ($pedido->estado == 'Completado' ? 'completed' : 'cancelled')) }}">{{ $pedido->estado }}</span></td>
                                <td><a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-info" title="Ver pedido"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay pedidos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $pedidos->links() }}</div>
        </div>
    </div>
</div>
@endsection
