@extends('layouts.app')

@section('title', 'Dashboard - Delicias Dulces')

@section('styles')
<style>
    .dashboard-canvas { --wine: #8f0929; --gold: #ac8123; --line: #e2cfba; max-width: 1280px; margin: 0 auto; color: #454951; }
    .dashboard-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 23px; }
    .dashboard-header h1 { margin: 0 0 2px; color: var(--wine); font-size: clamp(1.9rem, 3vw, 2.65rem); font-weight: 800; }
    .dashboard-header p { margin: 0; font-size: 1rem; }
    .dashboard-actions { display: flex; gap: 15px; flex-shrink: 0; }
    .dashboard-button { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 42px; padding: 9px 18px; border: 1px solid var(--wine); border-radius: 8px; color: var(--wine); background: transparent; font-size: .9rem; font-weight: 650; text-decoration: none; }
    .dashboard-button:hover { color: #720720; background: #fff0e8; }
    .dashboard-button--primary { color: #fff; background: var(--wine); }
    .dashboard-button--primary:hover { color: #fff; background: #720720; }
    .dashboard-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 17px 20px; margin-bottom: 26px; }
    .dashboard-stat { display: flex; align-items: center; justify-content: space-between; gap: 15px; min-height: 105px; padding: 17px 23px; border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: 0 4px 7px rgba(83, 49, 29, .08); color: inherit; text-decoration: none; transition: box-shadow .15s ease; }
    .dashboard-stat:hover { color: inherit; box-shadow: 0 6px 14px rgba(83, 49, 29, .14); }
    .dashboard-stat-label { display: block; margin-bottom: 4px; font-size: .94rem; font-weight: 650; }
    .dashboard-stat-value { display: block; color: var(--wine); font-size: clamp(1.65rem, 2.7vw, 2.35rem); font-weight: 800; line-height: 1.1; overflow-wrap: anywhere; }
    .dashboard-stat-icon { display: grid; place-items: center; flex-shrink: 0; width: 65px; min-height: 57px; padding-left: 16px; border-left: 1px solid #e3ccb0; color: var(--gold); font-size: 2rem; }
    .dashboard-stat--critical { background: linear-gradient(110deg, #fff2f3, #fcebed); }
    .dashboard-stat--critical .dashboard-stat-icon { color: #df2441; }
    .dashboard-stat-note { display: block; margin-top: 4px; color: #c32238; font-size: .82rem; font-style: italic; }
    .dashboard-panels { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); gap: 22px; margin-bottom: 25px; }
    .dashboard-panel { min-width: 0; border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: 0 5px 9px rgba(83, 49, 29, .07); }
    .dashboard-panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin: 0 20px; padding: 14px 0 12px; border-bottom: 1px solid #eee3d8; }
    .dashboard-section-title { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .dashboard-section-title > i { flex-shrink: 0; color: var(--gold); font-size: 1.65rem; }
    .dashboard-section-title h2 { margin: 0; color: var(--wine); font-size: 1.17rem; font-weight: 750; }
    .dashboard-section-subtitle { margin: 3px 0 0; color: #666971; font-size: .78rem; line-height: 1.4; }
    .dashboard-panel-body { padding: 17px 20px 20px; }
    .bestseller-product { display: flex; align-items: center; gap: 22px; min-height: 85px; }
    .bestseller-photo { display: grid; place-items: center; flex: 0 0 120px; min-height: 78px; padding-right: 18px; border-right: 1px solid #e3ccb0; }
    .bestseller-photo img { display: block; width: 100px; height: 85px; object-fit: contain; border-radius: 7px; }
    .bestseller-no-photo { display: grid; justify-items: center; gap: 5px; color: #96816d; font-size: .74rem; }
    .bestseller-no-photo i { font-size: 30px; }
    .bestseller-details { flex: 1; min-width: 0; }
    .bestseller-details h3 { margin: 0 0 4px; color: #262321; font-size: 1rem; font-weight: 700; overflow-wrap: anywhere; }
    .bestseller-units { margin: 0 0 10px; font-size: .85rem; }
    .bestseller-accent { height: 14px; border-radius: 999px; background: linear-gradient(90deg, #8f0929, #b21e4a); }
    .dashboard-alert-count { flex-shrink: 0; padding: 4px 10px; border-radius: 999px; background: #ffdedf; color: var(--wine); font-size: .78rem; font-weight: 700; }
    .dashboard-inventory-alert { display: flex; align-items: center; gap: 14px; padding: 15px 13px; border: 1px solid #f0cdd0; border-radius: 9px; background: #fff0f1; }
    .dashboard-inventory-alert + .dashboard-inventory-alert { margin-top: 12px; }
    .dashboard-inventory-alert > i { color: #dd2440; padding-right: 14px; border-right: 1px solid #e8bec0; font-size: 1.6rem; }
    .dashboard-alert-copy { flex: 1; min-width: 0; }
    .dashboard-alert-copy strong { display: block; color: var(--wine); font-size: .88rem; }
    .dashboard-alert-copy p { margin: 3px 0 0; font-size: .8rem; }
    .dashboard-inventory-alert .dashboard-button { min-height: 32px; padding: 5px 10px; font-size: .76rem; white-space: nowrap; }
    .dashboard-inventory-alert--warning { background: #fff7e7; border-color: #edd9a9; }
    .dashboard-inventory-alert--warning > i { color: var(--gold); border-color: #e4c985; }
    .dashboard-expiry-list { max-height: 245px; margin: 10px 0 0; padding: 0 4px 0 0; overflow-y: auto; list-style: none; }
    .dashboard-expiry-list li { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 10px 0; border-top: 1px solid #eadbc0; }
    .dashboard-expiry-list .dashboard-alert-copy { overflow-wrap: anywhere; }
    .dashboard-expiry-list .dashboard-button { flex-shrink: 0; margin: 0; }
    .dashboard-expiry-heading { display: flex; align-items: center; gap: 9px; color: #8f0929; font-size: .9rem; font-weight: 700; }
    .dashboard-expiry-heading i { color: #ac8123; }
    .dashboard-orders .dashboard-panel-heading { border-bottom: 0; }
    .dashboard-view-all { flex-shrink: 0; color: var(--wine); text-decoration: none; font-size: .85rem; font-weight: 650; }
    .dashboard-view-all:hover { color: #720720; text-decoration: underline; }
    .dashboard-orders .dashboard-panel-body { padding-top: 0; }
    .dashboard-orders table { margin: 0; font-size: .88rem; white-space: nowrap; }
    .dashboard-orders th { padding: 11px 9px; color: #4e5158; font-size: .8rem; border-bottom-color: #cfcac4; }
    .dashboard-orders td { padding: 14px 9px; vertical-align: middle; border-color: #ece5dd; }
    .dashboard-empty { padding: 18px 10px; text-align: center; white-space: normal; }
    .dashboard-empty > i { display: block; margin-bottom: 9px; color: #96958f; font-size: 38px; }
    .dashboard-empty h3 { margin: 0 0 4px; color: #55585e; font-size: 1.15rem; font-weight: 750; }
    .dashboard-empty p { margin: 0; color: #6c6c71; font-size: .83rem; }
    .dashboard-empty .dashboard-button { margin-top: 10px; min-height: 34px; padding: 5px 16px; font-size: .83rem; }
    .dashboard-workday { margin-bottom: 25px; }
    .dashboard-workday .dashboard-panel-body { display: flex; flex-wrap: wrap; gap: 12px; }
    .dashboard-canvas a:focus-visible { outline: 3px solid #dca62b; outline-offset: 3px; }
    .dashboard-topbar { background: #fffaf6; border-bottom-color: #e2cfba; }
    .dashboard-topbar .user-info > div:last-child { display: flex; flex-direction: column-reverse; }
    .dashboard-topbar .user-info .font-weight-bold { color: #282323; font-weight: 700; }
    @media (max-width: 1100px) { .dashboard-inventory-alert { flex-wrap: wrap; } .dashboard-inventory-alert .dashboard-button { margin-left: auto; } }
    @media (max-width: 900px) { .dashboard-header { align-items: flex-start; flex-direction: column; } .dashboard-panels { grid-template-columns: minmax(0, 1fr); } .dashboard-stats { gap: 12px; } .dashboard-stat { padding: 15px; } .dashboard-stat-icon { width: 48px; padding-left: 10px; font-size: 1.6rem; } }
    @media (max-width: 575px) { .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dashboard-stat { min-height: 110px; gap: 7px; padding: 13px 11px; } .dashboard-stat-label { font-size: .8rem; } .dashboard-stat-value { font-size: 1.7rem; } .dashboard-stat-icon { width: 35px; font-size: 1.35rem; } .dashboard-actions { flex-wrap: wrap; gap: 10px; } .dashboard-button { font-size: .83rem; padding: 8px 12px; } .dashboard-panel-heading { margin: 0 14px; } .dashboard-panel-body { padding: 15px 14px; } .dashboard-section-title h2 { font-size: 1rem; } .dashboard-section-title > i { font-size: 1.4rem; } .dashboard-orders table { min-width: 620px; } .bestseller-photo { flex-basis: 86px; padding-right: 12px; } .bestseller-photo img { width: 73px; height: 75px; } .bestseller-product { gap: 14px; } }
</style>
@endsection

@section('content')
@php
    $esAdministrador = Auth::user()->esAdministrador();
    $totalAlertasInventario = $stockCritico + $stockBajo + $lotesPorVencer->count();
    $indicadores = [
        ['titulo' => 'Pedidos hoy', 'valor' => $pedidosHoy, 'icono' => 'fa-calendar-days', 'url' => route('pedidos.index', ['fecha' => 'hoy'])],
        ['titulo' => 'Clientes', 'valor' => $clientesTotal, 'icono' => 'fa-users', 'url' => route('clientes.index')],
        ['titulo' => 'Ventas del día', 'valor' => 'Bs '.number_format($ventasHoy, 2, ',', '.'), 'icono' => 'fa-coins', 'url' => route('ventas.index', ['desde' => today()->toDateString(), 'hasta' => today()->toDateString()])],
        $esAdministrador
            ? ['titulo' => 'Stock crítico', 'valor' => $stockCritico, 'icono' => 'fa-triangle-exclamation', 'url' => route('insumos.index', ['filter' => 'critico']), 'critico' => $stockCritico > 0]
            : ['titulo' => 'Pendientes', 'valor' => $pedidosPendientes, 'icono' => 'fa-hourglass-half', 'url' => route('pedidos.index', ['estado' => 'Pendiente'])],
        ['titulo' => 'Producción hoy', 'valor' => $produccionHoy, 'icono' => 'fa-utensils', 'url' => route('pedidos.index', ['entrega' => 'hoy'])],
        ['titulo' => 'Pedidos de la semana', 'valor' => $pedidosSemana, 'icono' => 'fa-calendar-week', 'url' => route('pedidos.index', ['fecha' => 'semana'])],
    ];
@endphp
<div class="dashboard-canvas">
    <header class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <p>Hola, {{ Auth::user()->nombre ?: Auth::user()->name }}. Este es el resumen de tu negocio.</p>
        </div>
        <div class="dashboard-actions">
            @if($esAdministrador)
                <a href="{{ route('reportes.index') }}" class="dashboard-button"><i class="fas fa-chart-simple" aria-hidden="true"></i> Ver reportes</a>
            @endif
            <a href="{{ route('pedidos.create') }}" class="dashboard-button dashboard-button--primary"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo pedido</a>
        </div>
    </header>

    <div class="dashboard-stats">
        @foreach($indicadores as $indicador)
            <a href="{{ $indicador['url'] }}" class="dashboard-stat {{ !empty($indicador['critico']) ? 'dashboard-stat--critical' : '' }}">
                <div>
                    <span class="dashboard-stat-label">{{ $indicador['titulo'] }}</span>
                    <strong class="dashboard-stat-value">{{ $indicador['valor'] }}</strong>
                    @if(!empty($indicador['critico']))<span class="dashboard-stat-note">Requiere atención</span>@endif
                </div>
                <span class="dashboard-stat-icon"><i class="fas {{ $indicador['icono'] }}" aria-hidden="true"></i></span>
            </a>
        @endforeach
    </div>

    @unless($esAdministrador)
        <section class="dashboard-panel dashboard-workday" aria-labelledby="workday-title">
            <div class="dashboard-panel-heading"><div class="dashboard-section-title"><i class="fas fa-clipboard-check" aria-hidden="true"></i><h2 id="workday-title">Trabajo del día</h2></div></div>
            <div class="dashboard-panel-body">
                <a href="{{ route('clientes.create') }}" class="dashboard-button"><i class="fas fa-user-plus" aria-hidden="true"></i> Nuevo cliente</a>
                <a href="{{ route('pedidos.index', ['estado' => 'En proceso']) }}" class="dashboard-button">En proceso: {{ $pedidosEnProceso }}</a>
                <a href="{{ route('ventas.index') }}" class="dashboard-button">Ver ventas</a>
            </div>
        </section>
    @endunless

    <div class="dashboard-panels">
        <section class="dashboard-panel" aria-labelledby="bestseller-title">
            <div class="dashboard-panel-heading">
                <div class="dashboard-section-title">
                    <i class="fas fa-award" aria-hidden="true"></i>
                    <div><h2 id="bestseller-title">Producto más vendido</h2><p class="dashboard-section-subtitle">Según los pedidos completados · Todo el historial</p></div>
                </div>
            </div>
            <div class="dashboard-panel-body">
                @if($productoMasVendido)
                    <div class="bestseller-product">
                        <div class="bestseller-photo">
                            @php($fotoProducto = \App\Services\ImagenProducto::url($productoMasVendido->imagen))
                            @if($fotoProducto)
                                <img src="{{ $fotoProducto }}" alt="{{ $productoMasVendido->nombre }}" width="100" height="85" decoding="async">
                            @else
                                <span class="bestseller-no-photo"><i class="fas fa-cake-candles" aria-hidden="true"></i>Sin foto</span>
                            @endif
                        </div>
                        <div class="bestseller-details">
                            <h3>{{ $productoMasVendido->nombre }}</h3>
                            <p class="bestseller-units">{{ number_format($productoMasVendido->cantidad, 0, ',', '.') }} {{ (int) $productoMasVendido->cantidad === 1 ? 'unidad vendida' : 'unidades vendidas' }}</p>
                            <div class="bestseller-accent" aria-hidden="true"></div>
                        </div>
                    </div>
                @else
                    <div class="dashboard-empty"><i class="fas fa-cake-candles" aria-hidden="true"></i><p>Aún no hay productos vendidos en pedidos completados.</p></div>
                @endif
            </div>
        </section>

        <section class="dashboard-panel" aria-labelledby="dashboard-alerts-title">
            <div class="dashboard-panel-heading">
                <div class="dashboard-section-title">
                    <i class="fas {{ $esAdministrador ? 'fa-triangle-exclamation' : 'fa-list-check' }}" aria-hidden="true"></i>
                    <h2 id="dashboard-alerts-title">{{ $esAdministrador ? 'Alertas de inventario' : 'Resumen de pedidos' }}</h2>
                </div>
                @if($esAdministrador && $totalAlertasInventario > 0)
                    <span class="dashboard-alert-count">{{ $totalAlertasInventario }} {{ $totalAlertasInventario === 1 ? 'alerta' : 'alertas' }}</span>
                @endif
            </div>
            <div class="dashboard-panel-body">
                @if($esAdministrador)
                    @if($stockCritico > 0)
                        <div class="dashboard-inventory-alert">
                            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                            <div class="dashboard-alert-copy"><strong>Stock en el mínimo o por debajo</strong><p>{{ $stockCritico }} {{ $stockCritico === 1 ? 'insumo requiere reposición.' : 'insumos requieren reposición.' }}</p></div>
                            <a href="{{ route('insumos.index', ['filter' => 'critico']) }}" class="dashboard-button">Revisar insumos <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    @endif
                    @if($stockBajo > 0)
                        <div class="dashboard-inventory-alert dashboard-inventory-alert--warning">
                            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                            <div class="dashboard-alert-copy"><strong>Stock cerca del mínimo</strong><p>{{ $stockBajo }} {{ $stockBajo === 1 ? 'insumo necesita revisión.' : 'insumos necesitan revisión.' }}</p></div>
                            <a href="{{ route('insumos.index') }}" class="dashboard-button">Ver inventario <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    @endif
                    @if($lotesPorVencer->isNotEmpty())
                        <div class="dashboard-inventory-alert dashboard-inventory-alert--warning">
                            <div class="dashboard-alert-copy">
                                <div class="dashboard-expiry-heading"><i class="fas fa-calendar-days" aria-hidden="true"></i> Insumos por vencer</div>
                                <p>Lotes con existencias que vencen hoy o en los próximos 7 días.</p>
                                <ul class="dashboard-expiry-list" aria-label="Lotes próximos a vencer" tabindex="0">
                                    @foreach($lotesPorVencer as $lote)
                                        <li>
                                            <div class="dashboard-alert-copy">
                                                <strong>{{ $lote->insumo->nombre }}</strong>
                                                <p>Lote {{ $lote->codigo }} · {{ number_format($lote->cantidad, 2, ',', '.') }} {{ $lote->insumo->unidad }}</p>
                                                <p>{{ $lote->fecha_vencimiento->isToday() ? 'Vence hoy' : 'Vence el '.$lote->fecha_vencimiento->format('d/m/Y') }}</p>
                                            </div>
                                            <a href="{{ route('insumos.show', $lote->insumo_id) }}" class="dashboard-button" aria-label="Revisar {{ $lote->insumo->nombre }}, lote {{ $lote->codigo }}">Revisar <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    @if($totalAlertasInventario === 0)
                        <div class="dashboard-empty"><i class="fas fa-circle-check" aria-hidden="true"></i><p>No hay alertas de inventario por el momento.</p></div>
                    @endif
                @else
                    <div class="dashboard-inventory-alert dashboard-inventory-alert--warning"><div class="dashboard-alert-copy"><strong>Pendientes</strong><p>{{ $pedidosPendientes }} pedido(s) esperando atención</p></div><a href="{{ route('pedidos.index', ['estado' => 'Pendiente']) }}" class="dashboard-button">Ver pedidos</a></div>
                    <div class="dashboard-inventory-alert dashboard-inventory-alert--warning"><div class="dashboard-alert-copy"><strong>En proceso</strong><p>{{ $pedidosEnProceso }} pedido(s) en preparación</p></div><a href="{{ route('pedidos.index', ['estado' => 'En proceso']) }}" class="dashboard-button">Ver pedidos</a></div>
                @endif
            </div>
        </section>
    </div>

    <section class="dashboard-panel dashboard-orders" aria-labelledby="recent-orders-title">
        <div class="dashboard-panel-heading">
            <div class="dashboard-section-title">
                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                <div><h2 id="recent-orders-title">Pedidos recientes</h2><p class="dashboard-section-subtitle">Últimos 5 pedidos con entrega este mes o de meses anteriores que siguen pendientes o en proceso.</p></div>
            </div>
            <a href="{{ route('pedidos.index') }}" class="dashboard-view-all">Ver todos <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="dashboard-panel-body">
            <div class="table-responsive" role="region" aria-label="Pedidos recientes" tabindex="0">
                <table class="table">
                    <thead><tr><th scope="col">Pedido</th><th scope="col">Cliente</th><th scope="col">Producto</th><th scope="col">Entrega</th><th scope="col">Total</th><th scope="col">Estado</th></tr></thead>
                    <tbody>
                        @forelse($pedidosRecientes as $pedido)
                            <tr>
                                <td><strong>{{ $pedido->codigo_pedido }}</strong></td>
                                <td>{{ $pedido->cliente->nombre_completo }}</td>
                                <td>{{ $pedido->detalles->first()?->producto?->nombre ?? 'Sin producto' }}</td>
                                <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                                <td>Bs {{ number_format($pedido->total, 2, ',', '.') }}</td>
                                <td>
                                    @if($pedido->estado === 'Pendiente')<span class="badge badge-pending">Pendiente</span>
                                    @elseif($pedido->estado === 'En proceso')<span class="badge badge-process">En proceso</span>
                                    @elseif($pedido->estado === 'Completado')<span class="badge badge-completed">Completado</span>
                                    @else<span class="badge badge-cancelled">Cancelado</span>@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="dashboard-empty"><i class="far fa-clipboard" aria-hidden="true"></i><h3>No hay pedidos para mostrar</h3><p>Los pedidos con entrega este mes y los atrasados pendientes o en proceso aparecerán aquí.</p><a href="{{ route('pedidos.create') }}" class="dashboard-button"><i class="fas fa-plus" aria-hidden="true"></i> Registrar pedido</a></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
