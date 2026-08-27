@extends('layouts.app')

@section('title', 'Pedidos - Delicias Dulces')

@section('content')
<style>
    .pedidos-page { max-width: 1280px; margin: 0 auto; }
    .pedidos-heading { display: flex; justify-content: space-between; gap: 20px; align-items: center; margin-bottom: 28px; }
    .pedidos-heading h1 { margin: 0; color: #15233d; font-size: 1.45rem; font-weight: 700; }
    .pedidos-heading p { margin: 4px 0 0; color: #71809a; }
    .pedidos-new-btn { border-radius: 999px; background: #e91e63; border-color: #e91e63; font-weight: 700; padding: 10px 18px; white-space: nowrap; }
    .pedidos-new-btn:hover { background: #c91853; border-color: #c91853; }
    .pedidos-card { overflow: hidden; border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 3px 14px rgba(33, 45, 70, .06); }
    .pedidos-filters { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid #eef0f4; }
    .pedidos-search { position: relative; width: min(320px, 100%); margin-right: 2px; }
    .pedidos-search i { position: absolute; top: 50%; left: 14px; color: #8e9bb0; transform: translateY(-50%); }
    .pedidos-search input { border-radius: 999px; padding-left: 36px; background: #fbfcfe; }
    .pedido-filter { border: 0; border-radius: 999px; padding: 7px 14px; background: #f3f5f8; color: #52617a; font-size: .82rem; font-weight: 600; white-space: nowrap; }
    .pedido-filter:hover, .pedido-filter--active { background: #e91e63; color: #fff; }
    .pedidos-table { margin: 0; vertical-align: middle; }
    .pedidos-table thead th { padding: 16px; color: #63708a; background: #fbfcfe; border-bottom: 1px solid #eef0f4; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
    .pedidos-table tbody td { padding: 14px 16px; border-color: #f0f2f5; color: #394861; }
    .pedido-number, .pedido-client { color: #15233d; font-weight: 700; }
    .pedido-product { max-width: 240px; }
    .pedido-total { color: #15233d; font-weight: 700; white-space: nowrap; }
    .pedido-status { display: inline-block; border-radius: 999px; padding: 4px 11px; font-size: .78rem; font-weight: 600; white-space: nowrap; }
    .pedido-status--pendiente { background: #fff5c2; color: #955b00; }
    .pedido-status--proceso { background: #dbeafe; color: #1d4ed8; }
    .pedido-status--completado { background: #dcfce7; color: #15733a; }
    .pedido-status--cancelado { background: #fee2e2; color: #b91c1c; }
    .pedido-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .pedido-actions .btn { border-radius: 999px; }
    @media (max-width: 991.98px) { .pedidos-filters { flex-wrap: wrap; } .pedidos-card { overflow-x: auto; } .pedidos-table { min-width: 950px; } }
    @media (max-width: 575.98px) { .pedidos-heading { align-items: flex-start; flex-direction: column; } }
</style>

<div class="pedidos-page">
    <div class="pedidos-heading">
        <div>
            <h1><i class="fas fa-receipt me-2"></i>Gestión de Pedidos</h1>
            <p>Registra y administra todos los pedidos del emprendimiento</p>
        </div>
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary pedidos-new-btn"><i class="fas fa-plus me-1"></i> Nuevo Pedido</a>
    </div>

    <div class="card pedidos-card">
        <form method="GET" class="pedidos-filters">
            <div class="pedidos-search">
                <i class="fas fa-search"></i>
                <input type="search" name="search" class="form-control" placeholder="Buscar pedido o cliente..." value="{{ $search }}">
            </div>
            @foreach(['todos' => 'Todos', 'Pendiente' => 'Pendiente', 'En proceso' => 'En proceso', 'Completado' => 'Completado', 'Cancelado' => 'Cancelado'] as $valor => $etiqueta)
                <button type="submit" name="estado" value="{{ $valor }}" class="pedido-filter {{ $estado === $valor ? 'pedido-filter--active' : '' }}">{{ $etiqueta }}</button>
            @endforeach
        </form>

        <table class="table pedidos-table">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Entrega</th>
                    <th>Producto</th>
                    <th class="text-center">Cant.</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                    @php
                        $primerProducto = $pedido->detalles->first();
                        $cantidadTotal = $pedido->detalles->sum('cantidad');
                        $claseEstado = match ($pedido->estado) {
                            'Pendiente' => 'pendiente',
                            'En proceso' => 'proceso',
                            'Completado' => 'completado',
                            default => 'cancelado',
                        };
                    @endphp
                    <tr>
                        <td><span class="pedido-number">{{ $pedido->codigo_pedido }}</span></td>
                        <td><span class="pedido-client">{{ $pedido->cliente->nombre_completo }}</span></td>
                        <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                        <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                        <td class="pedido-product">
                            @if($primerProducto)
                                {{ $primerProducto->producto->nombre }}{{ $pedido->detalles->count() > 1 ? ' +' . ($pedido->detalles->count() - 1) : '' }}
                            @else
                                <span class="text-muted">Sin productos</span>
                            @endif
                        </td>
                        <td class="text-center fw-semibold">{{ $cantidadTotal }}</td>
                        <td><span class="pedido-total">Bs {{ number_format($pedido->total, 0, ',', '.') }}</span></td>
                        <td><span class="pedido-status pedido-status--{{ $claseEstado }}">{{ $pedido->estado }}</span></td>
                        <td>
                            <div class="pedido-actions">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-info" title="Ver pedido"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-sm btn-light" title="Editar pedido"><i class="fas fa-pen"></i></a>
                                @if($pedido->estado === 'Pendiente')
                                    <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('¿Eliminar este pedido? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar pedido"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay pedidos para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-3 pt-3">{{ $pedidos->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
