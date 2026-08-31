@extends('layouts.app')

@section('title', 'Ventas - Delicias Dulces')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title mb-1"><i class="fas fa-coins me-2"></i>Ventas</h1>
        <p class="text-muted mb-0">Pedidos completados y cobrados en efectivo.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Total vendido</div><div class="fs-3 fw-bold" style="color: #198754;">Bs {{ number_format($totalVentas, 2, ',', '.') }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Ventas completadas</div><div class="fs-3 fw-bold" style="color: #c7436f;">{{ $cantidadVentas }}</div></div></div></div>
    <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted small">Ticket promedio</div><div class="fs-3 fw-bold" style="color: #d4a300;">Bs {{ number_format($ticketPromedio, 2, ',', '.') }}</div></div></div></div>
</div>

<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4"><label for="buscar" class="form-label">Buscar venta</label><input id="buscar" name="buscar" type="search" class="form-control" value="{{ $buscar }}" placeholder="Pedido o cliente"></div>
        <div class="col-md-3"><label for="desde" class="form-label">Desde</label><input id="desde" name="desde" type="date" class="form-control" value="{{ $desde }}"></div>
        <div class="col-md-3"><label for="hasta" class="form-label">Hasta</label><input id="hasta" name="hasta" type="date" class="form-control" value="{{ $hasta }}"></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" title="Filtrar ventas"><i class="fas fa-filter"></i></button><a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="fas fa-rotate-left"></i></a></div>
    </form>
</div></div>

<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">
    <thead><tr><th>Pedido</th><th>Cliente</th><th>Fecha del pedido</th><th>Productos</th><th>Pago</th><th class="text-end">Total</th><th></th></tr></thead>
    <tbody>
        @forelse($ventas as $venta)
            <tr>
                <td class="fw-semibold">{{ $venta->codigo_pedido }}</td><td>{{ $venta->cliente->nombre_completo }}</td><td>{{ $venta->fecha_pedido->format('d/m/Y') }}</td>
                <td>{{ $venta->detalles->pluck('producto.nombre')->filter()->implode(', ') }}</td><td><span class="badge text-bg-success">Efectivo</span></td>
                <td class="text-end fw-semibold">Bs {{ number_format($venta->total, 2, ',', '.') }}</td><td class="text-end"><a href="{{ route('pedidos.show', $venta) }}" class="btn btn-sm btn-outline-primary" title="Ver pedido"><i class="fas fa-eye"></i></a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-5 text-center text-muted">No hay ventas completadas para este periodo.</td></tr>
        @endforelse
    </tbody>
</table></div></div>
@if($ventas->hasPages())<div class="card-footer">{{ $ventas->links() }}</div>@endif
</div>
@endsection
