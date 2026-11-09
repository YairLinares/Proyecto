@extends('layouts.app')

@section('title', 'Pedidos - Delicias Dulces')

@section('styles')
<style>
    .pedidos-page { --wine: #8f0929; --gold: #ad813b; max-width: 1280px; margin: 0 auto; color: #383031; }
    .pedidos-breadcrumb { display: flex; gap: 10px; margin-bottom: 25px; color: #665b55; font-size: .95rem; }
    .pedidos-breadcrumb strong { color: var(--wine); }
    .pedidos-heading { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 30px; }
    .pedidos-heading-copy { display: flex; align-items: center; gap: 22px; }
    .pedidos-heading-icon { padding-right: 22px; border-right: 3px solid var(--gold); color: var(--wine); font-size: 2.6rem; }
    .pedidos-heading h1 { margin: 0 0 5px; color: var(--wine); font-size: clamp(1.6rem, 3vw, 2.5rem); font-weight: 800; }
    .pedidos-heading p { margin: 0; color: #70635c; font-size: 1rem; }
    .pedidos-heading-actions { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
    .pedidos-count { padding: 8px 15px; border: 1px solid #bb904b; border-radius: 18px; color: #645343; background: #faf0de; font-size: .85rem; }
    .pedidos-new-btn { display: inline-flex; align-items: center; gap: 10px; padding: 12px 20px; border: 1px solid var(--wine); border-radius: 12px; color: #fff; background: var(--wine); font-weight: 700; text-decoration: none; white-space: nowrap; }
    .pedidos-new-btn:hover { background: #720720; color: #fff; }
    .pedidos-card { overflow: hidden; border: 1px solid #e8ddd2; border-radius: 18px; background: #fff; box-shadow: 0 9px 22px rgba(95, 47, 22, .1); }
    .pedidos-filters { padding: 24px 24px 22px; }
    .pedidos-search { position: relative; margin-bottom: 20px; }
    .pedidos-search button { position: absolute; left: 8px; top: 50%; transform: translateY(-50%); width: 37px; height: 37px; border: 0; border-radius: 8px; color: #956d2f; background: transparent; font-size: 1.1rem; }
    .pedidos-search button:hover { background: #f8eee0; }
    .pedidos-search input { min-height: 54px; padding-left: 55px; border: 1px solid #d3cac5; border-radius: 14px; background: #fdfcfb; }
    .pedidos-search input:focus { border-color: var(--wine); box-shadow: 0 0 0 3px rgba(143, 9, 41, .1); }
    .pedidos-tabs { display: flex; flex-wrap: wrap; gap: 10px; }
    .pedido-filter { padding: 9px 23px; border: 0; border-radius: 20px; color: #504541; background: #f6f1e9; font-size: .95rem; font-weight: 650; }
    .pedido-filter:hover { background: #efe2d3; }
    .pedido-filter--active, .pedido-filter--active:hover { background: var(--wine); color: #fff; }
    .pedidos-table-wrap { margin: 0 8px 20px; overflow: auto; border: 1px solid #eaddcf; border-radius: 15px; }
    .pedidos-table { margin: 0; min-width: 950px; vertical-align: middle; font-size: .9rem; }
    .pedidos-table thead th { padding: 16px 12px; color: var(--wine); background: #f8ecdb; border-bottom: 1px solid #e7d9c9; font-weight: 750; }
    .pedidos-table tbody td { padding: 18px 12px; border-color: #eee5dc; }
    .pedidos-table tbody tr:last-child td { border-bottom: 0; }
    .pedido-number, .pedido-total { font-weight: 750; white-space: nowrap; }
    .pedido-product { min-width: 140px; max-width: 220px; }
    .pedido-status { display: inline-block; padding: 5px 11px; border-radius: 999px; font-size: .8rem; font-weight: 700; white-space: nowrap; }
    .pedido-status--pendiente { background: #fff2c6; color: #8b620d; }
    .pedido-status--proceso { background: #e3edff; color: #3458a1; }
    .pedido-status--completado { background: #dcf5d7; color: #2a8530; }
    .pedido-status--cancelado { background: #fee2e2; color: #b52c37; }
    .pedido-actions { display: flex; justify-content: flex-end; gap: 8px; white-space: nowrap; }
    .pedido-actions .btn { display: inline-flex; align-items: center; gap: 6px; justify-content: center; min-height: 34px; border: 1px solid #bc975c; border-radius: 9px; color: var(--wine); background: #fffdf9; font-size: .8rem; }
    .pedido-actions .btn:hover { background: #fff1de; }
    .pedido-actions .pedido-delete { border-color: #efb7b7; color: #c42a36; }
    .pedidos-footer { padding: 18px 24px; border-top: 1px solid #e8dfd5; }
    .pedidos-footer .app-pagination { justify-content: center; margin: 0; padding: 0; }
    .pedidos-footer .app-pagination__summary { color: #685e59; font-size: .9rem; }
    .pedidos-footer .app-pagination__link { border-color: #e5cfaf; border-radius: 9px; color: #a17c44; background: #fffdf9; }
    .pedidos-footer .app-pagination__link--active, .pedidos-footer a.app-pagination__link:hover { border-color: var(--wine); background: var(--wine); color: #fff; }
    .pedidos-footer .app-pagination__link--disabled { color: #b5a896; background: #faf7f2; }
    .pedidos-page a:focus-visible, .pedidos-page button:focus-visible { outline: 3px solid #d5a751; outline-offset: 3px; }
    @media (max-width: 1150px) { .pedidos-heading { align-items: flex-start; flex-direction: column; } .pedidos-heading-actions { align-self: flex-end; } }
    @media (max-width: 575px) { .pedidos-heading-copy { gap: 13px; } .pedidos-heading-icon { padding-right: 13px; font-size: 2rem; } .pedidos-heading-actions { align-self: stretch; flex-wrap: wrap; gap: 12px; } .pedidos-filters { padding: 18px 15px; } .pedido-filter { padding: 8px 14px; font-size: .85rem; } .pedidos-footer { padding: 17px 15px; } .pedidos-footer .app-pagination { align-items: center; } .pedidos-count { font-size: .8rem; } }
</style>
@endsection

@section('content')
<div class="pedidos-page">
    <nav class="pedidos-breadcrumb" aria-label="Ruta de navegación"><strong>Pedidos</strong><span aria-hidden="true">/</span><span aria-current="page">Listado</span></nav>
    <header class="pedidos-heading">
        <div class="pedidos-heading-copy">
            <i class="fas fa-receipt pedidos-heading-icon" aria-hidden="true"></i>
            <div><h1>Gestión de pedidos</h1><p>Consulta y administra los pedidos de tus clientes.</p></div>
        </div>
        <div class="pedidos-heading-actions">
            <span class="pedidos-count">{{ $pedidos->total() }} {{ $pedidos->total() === 1 ? 'pedido' : 'pedidos' }} {{ $search || $estado !== 'todos' || request('fecha') || request('entrega') ? ($pedidos->total() === 1 ? 'encontrado' : 'encontrados') : ($pedidos->total() === 1 ? 'registrado' : 'registrados') }}</span>
            <a href="{{ route('pedidos.create') }}" class="pedidos-new-btn"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo pedido</a>
        </div>
    </header>
    <div class="pedidos-card">
        <form method="GET" action="{{ route('pedidos.index') }}" class="pedidos-filters" role="search">
            @foreach(['fecha', 'entrega'] as $filtroFecha)
                @if(request($filtroFecha))<input type="hidden" name="{{ $filtroFecha }}" value="{{ request($filtroFecha) }}">@endif
            @endforeach
            <div class="pedidos-search">
                <label for="buscar-pedido" class="visually-hidden">Buscar por pedido o cliente</label>
                <button type="submit" name="estado" value="{{ $estado }}" aria-label="Buscar pedidos"><i class="fas fa-search" aria-hidden="true"></i></button>
                <input id="buscar-pedido" type="search" name="search" class="form-control" placeholder="Buscar por pedido o cliente..." value="{{ $search }}">
            </div>
            <div class="pedidos-tabs" role="group" aria-label="Filtrar pedidos por estado">
                @foreach(['todos' => 'Todos', 'Pendiente' => 'Pendiente', 'En proceso' => 'En proceso', 'Completado' => 'Completado', 'Cancelado' => 'Cancelado'] as $valor => $etiqueta)
                    <button type="submit" name="estado" value="{{ $valor }}" aria-pressed="{{ $estado === $valor ? 'true' : 'false' }}" class="pedido-filter {{ $estado === $valor ? 'pedido-filter--active' : '' }}">{{ $etiqueta }}</button>
                @endforeach
            </div>
        </form>
        <div class="pedidos-table-wrap" role="region" aria-label="Listado de pedidos" tabindex="0">
            <table class="table pedidos-table">
                <thead><tr><th scope="col">Pedido</th><th scope="col">Cliente</th><th scope="col">Fecha de registro</th><th scope="col">Entrega</th><th scope="col">Producto</th><th scope="col" class="text-center">Cantidad</th><th scope="col">Total</th><th scope="col">Estado</th><th scope="col" class="text-end">Acciones</th></tr></thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                        @php
                            $primerProducto = $pedido->detalles->first();
                            $claseEstado = match ($pedido->estado) { 'Pendiente' => 'pendiente', 'En proceso' => 'proceso', 'Completado' => 'completado', default => 'cancelado' };
                        @endphp
                        <tr>
                            <td class="pedido-number">{{ $pedido->codigo_pedido }}</td>
                            <td>{{ $pedido->cliente->nombre_completo }}</td>
                            <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                            <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                            <td class="pedido-product">{{ $primerProducto?->producto?->nombre ?? 'Sin productos' }}{{ $pedido->detalles->count() > 1 ? ' +'.($pedido->detalles->count() - 1) : '' }}</td>
                            <td class="text-center fw-semibold">{{ $pedido->detalles->sum('cantidad') }}</td>
                            <td class="pedido-total">Bs {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td><span class="pedido-status pedido-status--{{ $claseEstado }}">{{ $pedido->estado }}</span></td>
                            <td><div class="pedido-actions">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm" aria-label="Ver pedido {{ $pedido->codigo_pedido }}"><i class="far fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-sm" aria-label="Editar pedido {{ $pedido->codigo_pedido }}"><i class="fas fa-pen" aria-hidden="true"></i> Editar</a>
                                @if(in_array($pedido->estado, ['Pendiente', 'Cancelado'], true) && !$pedido->produccion_iniciada_at && !$pedido->movimientos_insumo_exists)
                                    <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('¿Eliminar este pedido? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm pedido-delete" aria-label="Eliminar pedido {{ $pedido->codigo_pedido }}"><i class="far fa-trash-can" aria-hidden="true"></i></button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5">No hay pedidos para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pedidos-footer">
            @if($pedidos->hasPages())
                @include('components.pagination', ['paginator' => $pedidos->withQueryString()])
            @else
                <nav class="app-pagination" aria-label="Paginación de pedidos">
                    <ul class="app-pagination__links">
                        <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">Anterior</span></span></li>
                        <li><span class="app-pagination__link app-pagination__link--active" aria-current="page">1</span></li>
                        <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">Siguiente</span></span></li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection
