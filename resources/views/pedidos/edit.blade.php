
@extends('layouts.app')
 
@section('title', 'Editar ' . $pedido->numero_pedido)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Pedido: {{ $pedido->numero_pedido }}</h1>
</div>
 
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('pedidos.update', $pedido) }}">
                    @csrf
                    @method('PUT')
 
                    <h5 class="mb-3">Información del Pedido</h5>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cliente *</label>
                                <select class="form-select" name="cliente_id" required>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ $pedido->cliente_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado *</label>
                                <select class="form-select" name="estado" required>
                                    <option value="Pendiente" {{ $pedido->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="En proceso" {{ $pedido->estado == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                    <option value="Completado" {{ $pedido->estado == 'Completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="Cancelado" {{ $pedido->estado == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Entrega *</label>
                                <input type="date" class="form-control" name="fecha_entrega" value="{{ $pedido->fecha_entrega }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" name="prioridad" required>
                                    <option value="Bajo" {{ $pedido->prioridad == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                                    <option value="Normal" {{ $pedido->prioridad == 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Alto" {{ $pedido->prioridad == 'Alto' ? 'selected' : '' }}>Alto</option>
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion_especificaciones" rows="3">{{ $pedido->descripcion_especificaciones }}</textarea>
                    </div>
 
                    <h5 class="mb-3">Dirección de Entrega</h5>
 
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Dirección *</label>
                                <input type="text" class="form-control" name="direccion_entrega" value="{{ $pedido->direccion_entrega }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Teléfono *</label>
                                <input type="text" class="form-control" name="telefono_contacto" value="{{ $pedido->telefono_contacto }}" required>
                            </div>
                        </div>
                    </div>
 
                    <h5 class="mb-3">Método y Costos</h5>
 
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Método Pago *</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="Efectivo" {{ $pedido->metodo_pago == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="Tarjeta" {{ $pedido->metodo_pago == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="Transferencia" {{ $pedido->metodo_pago == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Descuento</label>
                                <input type="number" class="form-control" name="descuento" step="0.01" value="{{ $pedido->descuento }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Envío</label>
                                <input type="number" class="form-control" name="costo_envio" step="0.01" value="{{ $pedido->costo_envio }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Anticipo</label>
                                <input type="number" class="form-control" name="anticipo_recibido" step="0.01" value="{{ $pedido->anticipo_recibido }}">
                            </div>
                        </div>
                    </div>
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 