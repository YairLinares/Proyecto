@extends('layouts.app')

@section('title', 'Dashboard - Delicias Dulces')

@section('styles')
<style>
    .dashboard-canvas { position: relative; isolation: isolate; }
    .dashboard-canvas::before { content: ''; position: absolute; z-index: -1; inset: auto -26px -40px; height: 150px; background: radial-gradient(ellipse 52% 80% at 14% 100%, #fff0e2 0 58%, transparent 59%), radial-gradient(ellipse 48% 80% at 82% 100%, #fff0e2 0 58%, transparent 59%); pointer-events: none; }
    .dashboard-corner-art { position: absolute; z-index: -1; right: -45px; bottom: -65px; width: 290px; max-width: 35vw; opacity: .28; pointer-events: none; }
    .dashboard-welcome { position: relative; display: flex; align-items: center; gap: 15px; min-height: 66px; overflow: hidden; margin-bottom: 18px; border: 1px solid #aad5b8; background: #eaf5ec; color: #087249; box-shadow: 0 4px 14px rgba(71, 108, 70, .06); }
    .dashboard-welcome::before { content: '✓'; display: grid; flex: 0 0 27px; width: 27px; height: 27px; place-items: center; border-radius: 50%; background: #078454; color: white; font-weight: 700; }
    .dashboard-welcome::after { content: ''; position: absolute; right: 45px; top: -45px; width: 220px; height: 140px; background: url('{{ asset('images/ilustraciones/queque-entero.png') }}') center/contain no-repeat; opacity: .15; pointer-events: none; }
    .dashboard-welcome .btn-close { margin-left: auto; }
    .dashboard-header { align-items: flex-end; margin-bottom: 23px; }
    .dashboard-header .page-title { position: relative; margin: 0; color: #550d25; font-size: 35px; font-weight: 800; }
    .dashboard-header .page-title::after { content: ''; display: block; width: 96px; height: 4px; margin-top: 5px; border-radius: 4px; background: linear-gradient(90deg, #d31b48 0 62%, #ed7538 62%); }
    .dashboard-motto { margin: 0 0 6px; color: #e2b49f; font-family: 'Lobster', cursive; font-size: clamp(22px, 2.2vw, 35px); white-space: nowrap; }
    .stat-card-link { display: block; height: 100%; color: inherit; text-decoration: none; }
    .stat-card { position: relative; overflow: hidden; height: 100%; min-height: 174px; margin-bottom: 0; border: 1px solid #f1e8e3; border-top: 3px solid var(--accent, #d62348); border-radius: 14px; box-shadow: 0 6px 16px rgba(91, 48, 40, .07); transition: transform .18s ease, box-shadow .18s ease; }
    .stat-card::after { content: ''; position: absolute; right: -12%; bottom: -35px; left: -12%; height: 60px; border-radius: 50% 50% 0 0; transform: rotate(-4deg); background: var(--accent-wave, #fae2e5); opacity: .85; }
    .stat-card--pastry::before { content: ''; position: absolute; right: -8px; bottom: -6px; width: 82px; height: 62px; background: url('{{ asset('images/ilustraciones/queque-entero.png') }}') center/contain no-repeat; opacity: .32; pointer-events: none; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(83, 29, 34, .14); }
    .stat-card .card-body { position: relative; z-index: 1; padding: 16px 8px 20px; }
    .stat-icon { position: relative; width: 59px; height: 59px; margin: 0 auto; display: grid; place-items: center; border-radius: 50%; font-size: 22px; }
    .stat-icon::after { content: ''; position: absolute; right: -22px; top: 13px; width: 19px; height: 3px; border-radius: 3px; background: currentColor; opacity: .35; transform: rotate(-34deg); }
    .stat-value { margin: 12px 0 0; font-size: 29px; font-weight: 800; line-height: 1.1; }
    .stat-label { margin: 4px 0 0; color: #67666f; font-size: 12px; text-transform: uppercase; letter-spacing: .3px; }
    .section-card { overflow: hidden; height: 100%; margin-bottom: 0; border: 1px solid #f0e6df; border-radius: 13px; box-shadow: 0 5px 18px rgba(107, 60, 38, .06); }
    .section-card .card-header { position: relative; display: flex; align-items: center; min-height: 51px; padding: 11px 17px; border-bottom: 1px solid #f2ddd0; background: #fff0e7; color: #540b24; font-weight: 700; }
    .section-card .card-header::after { content: ''; position: absolute; right: 12px; top: -14px; width: 100px; height: 76px; background: url('{{ asset('images/ilustraciones/queque-rebanado.png') }}') center/contain no-repeat; opacity: .28; pointer-events: none; }
    .section-card .card-header > * { position: relative; z-index: 1; }
    .section-card .card-header h5 { font-size: 19px; font-weight: 750; }
    .section-card .card-header i { margin-right: 6px; color: #dc2544; }
    .dashboard-panels .section-card .card-body { display: flex; align-items: center; justify-content: center; min-height: 146px; padding: 16px; }
    .dashboard-panels .section-card .card-body:has(.alert) { display: block; }
    .dashboard-panels canvas { width: 100% !important; max-height: 220px; }
    .empty-state { width: 100%; text-align: center; color: #858995; font-size: 14px; }
    .empty-state img { display: block; width: 180px; height: 100px; margin: -8px auto -5px; object-fit: contain; opacity: .68; }
    .empty-state i { display: block; margin-bottom: 13px; color: #d7ae97; font-size: 42px; }
    .empty-state p { margin: 0; }
    .recent-orders .card-body { padding: 0 18px 14px; }
    .recent-orders .card-header::after { display: none; }
    .recent-orders .table { margin: 0; white-space: nowrap; }
    .recent-orders th { padding: 15px 10px 12px; border-bottom-color: #e8e9ea; color: #243047; font-size: 13px; }
    .recent-orders td { padding: 14px 10px; vertical-align: middle; }
    .recent-orders .empty-state { padding: 25px 0; white-space: normal; }
    .recent-orders .empty-state img { width: 95px; height: 55px; }
    .view-all { color: #d31738; font-size: 14px; font-weight: 700; text-decoration: none; }
    .view-all:hover { color: #9c1230; text-decoration: underline; }
    @media (max-width: 991.98px) { .dashboard-motto { display: none; } }
    @media (max-width: 575.98px) { .dashboard-header .page-title { font-size: 29px; } .stat-card { min-height: 155px; } .stat-icon { width: 48px; height: 48px; font-size: 19px; } .stat-value { font-size: 25px; } .section-card .card-header h5 { font-size: 17px; } .dashboard-corner-art { width: 180px; } }
</style>
@endsection

@section('content')
@php
    $esAdministrador = Auth::user()->esAdministrador();
@endphp

<div class="dashboard-canvas">
@unless(session('success'))
    <div class="alert alert-success alert-dismissible fade show dashboard-welcome" role="status">
        <strong>¡Bienvenido!</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endunless
<div class="page-header dashboard-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="dashboard-motto">Repostería que alegra tus días ♥</p>
</div>

<!-- Estadísticas Principales -->
<div class="row g-3 stat-row">
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'hoy']) }}" class="stat-card-link">
            <div class="card stat-card stat-card--pastry" style="--accent: #d7234d; --accent-wave: #fce0e6;">
                <div class="card-body text-center">
                    <div class="stat-icon" style="background: rgba(199,67,111,0.12); color: #c7436f;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="stat-value" style="color: #c7436f;">{{ $pedidosHoy }}</h3>
                    <p class="stat-label">Pedidos Hoy</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('clientes.index') }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #73716e; --accent-wave: #f6e9d8;">
                <div class="card-body text-center">
                    <div class="stat-icon" style="background: rgba(108,117,125,0.12); color: #6c757d;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-value" style="color: #6c757d;">{{ $clientesTotal }}</h3>
                    <p class="stat-label">Clientes</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('ventas.index', ['desde' => now()->toDateString(), 'hasta' => now()->toDateString()]) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #ee9800; --accent-wave: #fff0cf;">
                <div class="card-body text-center">
                    <div class="stat-icon" style="background: rgba(212,163,0,0.14); color: #d4a300;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 class="stat-value" style="color: #d4a300;">Bs {{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                    <p class="stat-label">Ventas del Día</p>
                </div>
            </div>
        </a>
    </div>

    @if($esAdministrador)
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('insumos.index', ['filter' => 'critico']) }}" class="stat-card-link">
                <div class="card stat-card" style="--accent: #e22a41; --accent-wave: #ffe0e5;">
                    <div class="card-body text-center">
                        <div class="stat-icon" style="background: rgba(220,53,69,0.12); color: #dc3545;">
                        <i class="fas fa-circle-exclamation"></i>
                        </div>
                        <h3 class="stat-value" style="color: #dc3545;">{{ $stockCritico }}</h3>
                        <p class="stat-label">Stock Crítico</p>
                    </div>
                </div>
            </a>
        </div>
    @else
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('pedidos.index', ['estado' => 'Pendiente']) }}" class="stat-card-link">
                <div class="card stat-card" style="--accent: #ffc107; --accent-wave: #fff3d9;">
                    <div class="card-body text-center">
                        <div class="stat-icon" style="background: rgba(255,193,7,0.16); color: #b77900;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <h3 class="stat-value" style="color: #b77900;">{{ $pedidosPendientes }}</h3>
                        <p class="stat-label">Pendientes</p>
                    </div>
                </div>
            </a>
        </div>
    @endif

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('pedidos.index', ['entrega' => 'hoy']) }}" class="stat-card-link">
            <div class="card stat-card stat-card--pastry" style="--accent: #d93165; --accent-wave: #fce3eb;">
                <div class="card-body text-center">
                    <div class="stat-icon" style="background: rgba(199,67,111,0.12); color: #c7436f;">
                        <i class="fas fa-cookie-bite"></i>
                    </div>
                    <h3 class="stat-value" style="color: #c7436f;">{{ $produccionHoy }}</h3>
                    <p class="stat-label">Producción Hoy</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'semana']) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #1a9b67; --accent-wave: #d9f2e8;">
                <div class="card-body text-center">
                    <div class="stat-icon" style="background: rgba(25,135,84,0.12); color: #198754;">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <h3 class="stat-value" style="color: #198754;">{{ $pedidosSemana }}</h3>
                    <p class="stat-label">Pedidos Semana</p>
                </div>
            </div>
        </a>
    </div>
</div>

@unless($esAdministrador)
<div class="row mt-4">
    <div class="col-12">
        <div class="card section-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Trabajo del día</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('pedidos.create') }}" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Nuevo pedido</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('clientes.create') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-user-plus"></i> Nuevo cliente</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('pedidos.index', ['estado' => 'En proceso']) }}" class="btn btn-outline-secondary w-100"><i class="fas fa-sync-alt"></i> En proceso: {{ $pedidosEnProceso }}</a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-coins"></i> Ver ventas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endunless

<!-- Productos más vendidos y Alertas -->
<div class="row g-3 mt-1 dashboard-panels">
    <div class="col-lg-7">
        <div class="card section-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Productos más vendidos</h5>
            </div>
            <div class="card-body">
                @if($productosMasVendidos->isEmpty())
                    <div class="empty-state">
                        <img src="{{ asset('images/ilustraciones/queque-entero.png') }}" alt="" aria-hidden="true">
                        <p>Aún no hay productos vendidos en pedidos completados.</p>
                    </div>
                @else
                    <canvas id="chartProductosMasVendidos"></canvas>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @if($esAdministrador)
            <div class="card section-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas</h5>
                </div>
                <div class="card-body">
                    @if($stockCritico == 0 && $stockBajo == 0)
                        <div class="empty-state">
                            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                            <p>No hay alertas de stock por el momento.</p>
                        </div>
                    @endif

                    @if($stockCritico > 0)
                    <div class="alert alert-danger mb-3">
                        <strong>Stock Crítico</strong>
                        <div class="small">{{ $stockCritico }} insumos por debajo del mínimo</div>
                    </div>
                    @endif
                    @if($stockBajo > 0)
                    <div class="alert alert-warning mb-3">
                        <strong>Stock Bajo</strong>
                        <div class="small">{{ $stockBajo }} insumos cerca del mínimo</div>
                    </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card section-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Resumen de pedidos</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <strong>Pendientes</strong>
                        <div class="small">{{ $pedidosPendientes }} pedido(s) esperando atención</div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <strong>En proceso</strong>
                        <div class="small">{{ $pedidosEnProceso }} pedido(s) en preparación</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pedidos Recientes -->
<div class="row mt-3 recent-orders">
    <div class="col-12">
        <div class="card section-card">
            <div class="card-header" style="justify-content: space-between;">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Pedidos Recientes</h5>
                <a href="{{ route('pedidos.index') }}" class="view-all">
                    Ver todos <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Entrega</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidosRecientes as $pedido)
                        <tr>
                            <td><strong>{{ $pedido->codigo_pedido }}</strong></td>
                            <td>{{ $pedido->cliente->nombre_completo }}</td>
                            <td>
                                @if($pedido->detalles->count() > 0)
                                    {{ $pedido->detalles->first()->producto->nombre }}
                                @endif
                            </td>
                            <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                            <td>Bs {{ number_format($pedido->total, 0, ',', '.') }}</td>
                            <td>
                                @if($pedido->estado == 'Pendiente')
                                    <span class="badge badge-pending"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                                @elseif($pedido->estado == 'En proceso')
                                    <span class="badge badge-process"><i class="fas fa-sync-alt"></i> En proceso</span>
                                @elseif($pedido->estado == 'Completado')
                                    <span class="badge badge-completed"><i class="fas fa-check-circle"></i> Completado</span>
                                @else
                                    <span class="badge badge-cancelled"><i class="fas fa-times-circle"></i> Cancelado</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6"><div class="empty-state"><img src="{{ asset('images/ilustraciones/queque-entero.png') }}" alt="" aria-hidden="true"><p>No hay pedidos registrados.</p></div></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<img class="dashboard-corner-art" src="{{ asset('images/ilustraciones/queque-rebanado.png') }}" alt="" aria-hidden="true">
</div>
@endsection

@section('scripts')
<script>
    const chartProductos = document.getElementById('chartProductosMasVendidos');
    if (chartProductos) {
        new Chart(chartProductos, {
        type: 'bar',
        data: {
            labels: @json($productosMasVendidos->pluck('nombre')),
            datasets: [{
                label: 'Unidades vendidas',
                data: @json($productosMasVendidos->pluck('cantidad')),
                backgroundColor: '#c7436f',
                borderRadius: 6,
                maxBarThickness: 34
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                y: { grid: { display: false } }
            }
        }
        });
    }
</script>
@endsection
