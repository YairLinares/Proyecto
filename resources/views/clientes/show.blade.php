@extends('layouts.app')

@section('title', $cliente->nombre_completo . ' - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $cliente->nombre_completo }}</h1>
    <div>
        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <p><strong>Documento:</strong> {{ $cliente->tipo_documento }} - {{ $cliente->numero_documento }}</p>
                <p><strong>Email:</strong> {{ $cliente->email }}</p>
                <p><strong>Teléfono:</strong> {{ $cliente->telefono_principal }}</p>
                <p><strong>Ciudad:</strong> {{ $cliente->ciudad }}</p>
                <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
                <p><strong>Tipo:</strong> 
                    @if($cliente->tipo_cliente == 'Corporativo')
                        <span class="badge" style="background-color: #c7436f;">VIP</span>
                    @else
                        <span class="badge" style="background-color: #6c757d;">Regular</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <p><strong>Total Pedidos:</strong> {{ $cliente->pedidos->count() }}</p>
                <p><strong>Total Compras:</strong> ${{ number_format($cliente->total_compras, 0, ',', '.') }}</p>
                <p><strong>Cliente Desde:</strong> {{ $cliente->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pedidos del Cliente</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Fecha</th>
                            <th>Entrega</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                        <tr>
                            <td><strong>{{ $pedido->numero_pedido }}</strong></td>
                            <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
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
                            <td>
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay pedidos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $pedidos->links() }}
            </div>
        </div>

        @if($cliente->notas_preferencias)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Notas y Preferencias</h5>
            </div>
            <div class="card-body">
                <p>{{ $cliente->notas_preferencias }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection