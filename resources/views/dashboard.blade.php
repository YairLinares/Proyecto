@extends('layouts.app')

@section('title', 'Dashboard - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- Estadísticas Principales -->
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p class="text-muted mb-1">Ventas del Mes</p>
                        <h3 style="color: #c7436f; margin: 0;">$ {{ number_format($ventasDelMes, 0, ',', '.') }}</h3>
                        <small class="text-success">+8.4% este mes</small>
                    </div>
                    <i class="fas fa-dollar-sign" style="font-size: 30px; color: #c7436f; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p class="text-muted mb-1">Pedidos Activos</p>
                        <h3 style="color: #0dcaf0; margin: 0;">{{ $pedidosActivos }}</h3>
                        <small class="text-info">+3 hoy</small>
                    </div>
                    <i class="fas fa-receipt" style="font-size: 30px; color: #0dcaf0; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p class="text-muted mb-1">Clientes Nuevos</p>
                        <h3 style="color: #198754; margin: 0;">{{ $clientesNuevos }}</h3>
                        <small class="text-success">+15.2% este mes</small>
                    </div>
                    <i class="fas fa-user-plus" style="font-size: 30px; color: #198754; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p class="text-muted mb-1">Alertas de Stock</p>
                        <h3 style="color: #dc3545; margin: 0;">{{ $alertasStock }}</h3>
                        <small class="text-danger">Requiere atención</small>
                    </div>
                    <i class="fas fa-exclamation-circle" style="font-size: 30px; color: #dc3545; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y Tablas -->
<div class="row mt-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ventas Mensuales 2026</h5>
                <small class="text-muted">En millones COP</small>
            </div>
            <div class="card-body">
                <canvas id="chartVentas"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ventas por Categoría</h5>
            </div>
            <div class="card-body">
                <canvas id="chartCategoria"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Recientes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pedidos Recientes</h5>
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
                            <td><strong>{{ $pedido->numero_pedido }}</strong></td>
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
                                    <span class="badge badge-pending">Pendiente</span>
                                @elseif($pedido->estado == 'En proceso')
                                    <span class="badge badge-process">En proceso</span>
                                @elseif($pedido->estado == 'Completado')
                                    <span class="badge badge-completed">Completado</span>
                                @else
                                    <span class="badge badge-cancelled">Cancelado</span>
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
    // Gráfico de Ventas Mensuales
    const ctxVentas = document.getElementById('chartVentas').getContext('2d');
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Ventas (Millones)',
                data: [3, 2.8, 3.2, 3.5, 4, 4.2, 3.8],
                borderColor: '#c7436f',
                backgroundColor: 'rgba(199, 67, 111, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#c7436f',
                pointRadius: 5,
                pointHoverRadius: 7
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

    // Gráfico de Categorías
    const ctxCategoria = document.getElementById('chartCategoria').getContext('2d');
    new Chart(ctxCategoria, {
        type: 'doughnut',
        data: {
            labels: ['Tortas', 'Cupcakes', 'Macarons'],
            datasets: [{
                data: [42, 28, 15],
                backgroundColor: [
                    '#c7436f',
                    '#d4a5c4',
                    '#e8c4d8'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection