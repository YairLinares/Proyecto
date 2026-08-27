@extends('layouts.app')

@section('title', 'Dashboard - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- Estadísticas Principales -->
<div class="row">
    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'hoy']) }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-receipt" style="font-size: 22px; color: #c7436f;"></i>
                    <h3 style="color: #c7436f; margin: 8px 0 0;">{{ $pedidosHoy }}</h3>
                    <p class="text-muted mb-0">Pedidos Hoy</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-2">
        <a href="{{ route('clientes.index') }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-users" style="font-size: 22px; color: #6c757d;"></i>
                    <h3 style="color: #6c757d; margin: 8px 0 0;">{{ $clientesTotal }}</h3>
                    <p class="text-muted mb-0">Clientes</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'hoy', 'estado' => 'Completado']) }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-coins" style="font-size: 22px; color: #d4a300;"></i>
                    <h3 style="color: #d4a300; margin: 8px 0 0;">$ {{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Ventas del Día</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-2">
        <a href="{{ route('insumos.index', ['filter' => 'critico']) }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-circle" style="font-size: 22px; color: #dc3545;"></i>
                    <h3 style="color: #dc3545; margin: 8px 0 0;">{{ $stockCritico }}</h3>
                    <p class="text-muted mb-0">Stock Crítico</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['entrega' => 'hoy']) }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-birthday-cake" style="font-size: 22px; color: #c7436f;"></i>
                    <h3 style="color: #c7436f; margin: 8px 0 0;">{{ $produccionHoy }}</h3>
                    <p class="text-muted mb-0">Producción Hoy</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-2">
        <a href="{{ route('pedidos.index', ['fecha' => 'semana']) }}" style="text-decoration: none; color: inherit;">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-week" style="font-size: 22px; color: #198754;"></i>
                    <h3 style="color: #198754; margin: 8px 0 0;">{{ $pedidosSemana }}</h3>
                    <p class="text-muted mb-0">Pedidos Semana</p>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Ventas de la Semana y Alertas -->
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Ventas de la Semana</h5>
            </div>
            <div class="card-body">
                <canvas id="chartVentasSemana"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertas</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger mb-3">
                    <strong>Stock Crítico</strong>
                    <div class="small">{{ $stockCritico }} insumos por debajo del mínimo</div>
                </div>
                <div class="alert alert-warning mb-3">
                    <strong>Stock Bajo</strong>
                    <div class="small">{{ $stockBajo }} insumos cerca del mínimo</div>
                </div>
                @if($productoStockBajo)
                <div class="alert alert-info mb-0">
                    <strong>{{ $productoStockBajo->nombre }}</strong>
                    <div class="small">Solo quedan {{ $productoStockBajo->stock_disponible }} unidades</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y Tablas -->
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Productos más vendidos</h5>
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
</div>

<!-- Pedidos Recientes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Pedidos Recientes</h5>
                <a href="{{ route('pedidos.index') }}" style="color: #c7436f; font-size: 14px; text-decoration: none;">
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
                            <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
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
    // Gráfico de Ventas de la Semana
    const ctxVentasSemana = document.getElementById('chartVentasSemana').getContext('2d');
    new Chart(ctxVentasSemana, {
        type: 'bar',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas',
                data: @json($ventasSemana),
                backgroundColor: '#c7436f',
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

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
