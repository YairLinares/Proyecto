@extends('layouts.app')
 
@section('title', $insumo->nombre)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $insumo->nombre }}</h1>
    <div>
        <a href="{{ route('insumos.movimientos.create', $insumo) }}" class="btn btn-primary"><i class="fas fa-right-left"></i> Movimiento</a>
        <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
 
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Información</h5></div>
            <div class="card-body">
                <p><strong>Stock Actual:</strong> {{ $insumo->stock_actual }} {{ $insumo->unidad }}</p>
                <p><strong>Stock Mínimo:</strong> {{ $insumo->stock_minimo }}</p>
                <p><strong>Precio Unitario:</strong> Bs {{ number_format($insumo->precio_unitario, 2, ',', '.') }}</p>
                <p><strong>Estado:</strong> <span class="badge badge-{{ $insumo->estado == 'Normal' ? 'completed' : 'pending' }}">{{ $insumo->estado }}</span></p>
            </div>
        </div>
    </div>
 
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Productos que Utilizan este Insumo</h5></div>
            <div class="card-body">
                @forelse($productos as $producto)
                <div class="mb-2"><strong>{{ $producto->nombre }}</strong><br>
                <small class="text-muted">Cantidad: {{ $producto->pivot->cantidad_necesaria }} {{ $insumo->unidad }}</small></div>
                @empty
                <p class="text-muted">No hay productos asociados</p>
                @endforelse
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historial de Movimientos</h5>
        <span class="text-muted small">Entradas, salidas y ajustes</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Stock</th>
                        <th>Motivo</th>
                        <th>Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $movimiento->tipo === 'Entrada' ? 'text-bg-success' : ($movimiento->tipo === 'Salida' ? 'text-bg-danger' : 'text-bg-warning') }}">
                                    {{ $movimiento->tipo }}
                                </span>
                            </td>
                            <td>{{ $movimiento->stock_posterior < $movimiento->stock_anterior ? '-' : '+' }}{{ number_format($movimiento->cantidad, 2, ',', '.') }} {{ $insumo->unidad }}</td>
                            <td>{{ number_format($movimiento->stock_anterior, 2, ',', '.') }} a {{ number_format($movimiento->stock_posterior, 2, ',', '.') }}</td>
                            <td>{{ $movimiento->motivo }}</td>
                            <td>{{ $movimiento->usuario?->nombre ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aun no hay movimientos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movimientos->hasPages())
        <div class="card-footer">{{ $movimientos->links() }}</div>
    @endif
</div>
@endsection
 
