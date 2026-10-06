@extends('layouts.app')

@section('title', 'Reportes - Delicias Dulces')

@section('styles')
<style>
    .reporte-card { border-top: 3px solid var(--accento, #c7436f); }
    .reporte-card .valor { font-size: 1.65rem; font-weight: 700; color: #4b1838; }
    .reporte-card .etiqueta { color: #677184; font-size: .88rem; }
    .reporte-grafico { height: 280px; }
    .reporte-estado { padding: 12px 15px; border-radius: 9px; background: #faf5f7; }
    .reporte-estado strong { color: #4b1838; }
    .reporte-estado span { float: right; font-weight: 700; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title mb-1"><i class="fas fa-chart-bar me-2"></i>Reportes</h1>
        <p class="text-muted mb-0">Resumen de pedidos completados y movimientos de inventario.</p>
    </div>
</div>

<div class="card mb-4"><div class="card-body">
    <form method="GET" action="{{ route('reportes.index') }}" class="row g-3 align-items-end">
        <div class="col-sm-4 col-lg-3">
            <label for="desde" class="form-label">Desde</label>
            <input id="desde" name="desde" type="date" class="form-control" value="{{ old('desde', $desde) }}" required>
        </div>
        <div class="col-sm-4 col-lg-3">
            <label for="hasta" class="form-label">Hasta</label>
            <input id="hasta" name="hasta" type="date" class="form-control" value="{{ old('hasta', $hasta) }}" required>
        </div>
        <div class="col-sm-4 col-lg-6 d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Ver reporte</button>
            <a class="btn btn-outline-secondary" href="{{ route('reportes.index') }}">Este mes</a>
            <a class="btn btn-outline-success" href="{{ route('reportes.ventas.csv', ['desde' => $desde, 'hasta' => $hasta]) }}"><i class="fas fa-download me-1"></i>Descargar ventas CSV</a>
        </div>
    </form>
    <p class="small text-muted mt-3 mb-0">Las ventas se agrupan por fecha del pedido y cuentan solo pedidos completados. El total representa ventas registradas, no pagos recibidos.</p>
</div></div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card reporte-card h-100" style="--accento: #c7436f"><div class="card-body"><div class="etiqueta">Ventas registradas</div><div class="valor">Bs {{ number_format($totalVentas, 2, ',', '.') }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card reporte-card h-100" style="--accento: #dfab23"><div class="card-body"><div class="etiqueta">Pedidos completados</div><div class="valor">{{ $cantidadVentas }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card reporte-card h-100" style="--accento: #419c7a"><div class="card-body"><div class="etiqueta">Promedio por pedido completado</div><div class="valor">Bs {{ number_format($ticketPromedio, 2, ',', '.') }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card reporte-card h-100" style="--accento: #6686b5"><div class="card-body"><div class="etiqueta">Pedidos pendientes</div><div class="valor">{{ $estados['Pendiente'] ?? 0 }}</div></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card h-100"><div class="card-header"><h5 class="mb-0">Ventas por día</h5></div><div class="card-body">
        @if($ventasPorDia->isEmpty())
            <p class="text-muted mb-0">No hay pedidos completados en estas fechas.</p>
        @else
            <div class="reporte-grafico"><canvas id="ventasPorDia" aria-label="Gráfico de ventas por día"></canvas></div>
        @endif
    </div></div></div>
    <div class="col-lg-4"><div class="card h-100"><div class="card-header"><h5 class="mb-0">Estado de pedidos</h5></div><div class="card-body d-grid gap-2">
        @foreach(['Pendiente', 'En proceso', 'Completado', 'Cancelado'] as $estado)
            <div class="reporte-estado"><strong>{{ $estado }}</strong><span>{{ $estados[$estado] ?? 0 }}</span></div>
        @endforeach
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-header"><h5 class="mb-0">Productos más vendidos</h5></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Producto</th><th class="text-end">Unidades</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody>@forelse($productos as $producto)
            <tr><td>{{ $producto->nombre }}</td><td class="text-end">{{ $producto->unidades }}</td><td class="text-end">Bs {{ number_format($producto->subtotal, 2, ',', '.') }}</td></tr>
        @empty<tr><td colspan="3" class="text-muted text-center py-4">Sin ventas en este período.</td></tr>@endforelse</tbody>
    </table></div></div></div></div>
    <div class="col-lg-6"><div class="card h-100"><div class="card-header"><h5 class="mb-0">Salidas manuales de insumos</h5></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Fecha</th><th>Insumo y motivo</th><th class="text-end">Cantidad</th></tr></thead>
        <tbody>@forelse($salidasManuales as $salida)
            <tr><td>{{ $salida->created_at->format('d/m/Y') }}</td><td>{{ $salida->insumo?->nombre ?? 'Insumo eliminado' }}<div class="small text-muted">{{ $salida->motivo }}</div></td><td class="text-end">{{ number_format($salida->cantidad, 2, ',', '.') }} {{ $salida->insumo?->unidad }}</td></tr>
        @empty<tr><td colspan="3" class="text-muted text-center py-4">Sin salidas manuales en este período.</td></tr>@endforelse</tbody>
    </table></div></div></div></div>
</div>
@endsection

@section('scripts')
@if($ventasPorDia->isNotEmpty())
<script>
    new Chart(document.getElementById('ventasPorDia'), {
        type: 'bar',
        data: {
            labels: @json($ventasPorDia->map(fn ($dia) => $dia->fecha_pedido->format('d/m/Y'))),
            datasets: [{ label: 'Bs vendidos', data: @json($ventasPorDia->pluck('total')), backgroundColor: '#c7436f', borderRadius: 5 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endif
@endsection
