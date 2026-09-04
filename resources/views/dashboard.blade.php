@extends('layouts.app')

@section('title', 'Dashboard - Delicias Dulces')

@section('styles')
<style>
    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .stat-card {
        border-top: 3px solid var(--accent, #c7436f);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 18px;
    }

    .stat-value {
        margin: 12px 0 2px;
        font-weight: 700;
        font-size: 26px;
    }

    .stat-label {
        color: #8a8a8a;
        font-size: 13px;
        margin-bottom: 0;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .section-card .card-header {
        font-weight: 600;
    }

    .section-card .card-header i {
        color: var(--primary-color);
    }
</style>
@endsection

@section('content')
@php
    $esAdministrador = Auth::user()->esAdministrador();
@endphp

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- Estadísticas Principales -->
<div class="row g-3 stat-row">
    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'hoy']) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #c7436f;">
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

    <div class="col-md-2">
        <a href="{{ route('clientes.index') }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #6c757d;">
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

    <div class="col-md-2">
        <a href="{{ route('ventas.index', ['desde' => now()->toDateString(), 'hasta' => now()->toDateString()]) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #d4a300;">
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
        <div class="col-md-2">
            <a href="{{ route('insumos.index', ['filter' => 'critico']) }}" class="stat-card-link">
                <div class="card stat-card" style="--accent: #dc3545;">
                    <div class="card-body text-center">
                        <div class="stat-icon" style="background: rgba(220,53,69,0.12); color: #dc3545;">
                            <i class="fas fa-circle"></i>
                        </div>
                        <h3 class="stat-value" style="color: #dc3545;">{{ $stockCritico }}</h3>
                        <p class="stat-label">Stock Crítico</p>
                    </div>
                </div>
            </a>
        </div>
    @else
        <div class="col-md-2">
            <a href="{{ route('pedidos.index', ['estado' => 'Pendiente']) }}" class="stat-card-link">
                <div class="card stat-card" style="--accent: #ffc107;">
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

    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['entrega' => 'hoy']) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #c7436f;">
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

    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'semana']) }}" class="stat-card-link">
            <div class="card stat-card" style="--accent: #198754;">
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
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card section-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Productos más vendidos</h5>
            </div>
            <div class="card-body">
                @if($productosMasVendidos->isEmpty())
                    <p class="text-muted mb-0">Aún no hay productos vendidos en pedidos completados.</p>
                @else
                    <canvas id="chartProductosMasVendidos"></canvas>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($esAdministrador)
            <div class="card section-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas</h5>
                </div>
                <div class="card-body">
                    @if($stockCritico == 0 && $stockBajo == 0)
                        <p class="text-muted mb-0">No hay alertas de stock por el momento.</p>
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
<div class="row mt-4">
    <div class="col-12">
        <div class="card section-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Pedidos Recientes</h5>
                <a href="{{ route('pedidos.index') }}" class="fw-semibold" style="color: #c7436f; font-size: 14px; text-decoration: none;">
                    Ver todos <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <div class="card-body">
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
                            <td colspan="6" class="text-center text-muted">No hay pedidos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
