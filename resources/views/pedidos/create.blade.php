
@extends('layouts.app')
 
@section('title', 'Nuevo Pedido')
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Registrar Nuevo Pedido</h1>
</div>
 
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('pedidos.store') }}">
                    @csrf
 
                    <h5 class="mb-3">Información del Pedido</h5>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cliente *</label>
                                <select class="form-select @error('cliente_id') is-invalid @enderror" name="cliente_id" required>
                                    <option value="">Selecciona un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Pedido *</label>
                                <select class="form-select" name="tipo_pedido" required>
                                    <option value="Personalizado">Personalizado</option>
                                    <option value="Predefinido">Predefinido</option>
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridad *</label>
                                <select class="form-select" name="prioridad" required>
                                    <option value="Bajo">Bajo</option>
                                    <option value="Normal" selected>Normal</option>
                                    <option value="Alto">Alto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Entrega *</label>
                                <input type="date" class="form-control @error('fecha_entrega') is-invalid @enderror" name="fecha_entrega" required>
                                @error('fecha_entrega')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción y Especificaciones</label>
                        <textarea class="form-control" name="descripcion_especificaciones" rows="3" placeholder="Decoraciones, ingredientes especiales, etc."></textarea>
                    </div>
 
                    <h5 class="mb-3 mt-4">Dirección de Entrega</h5>
 
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Dirección *</label>
                                <input type="text" class="form-control @error('direccion_entrega') is-invalid @enderror" name="direccion_entrega" required>
                                @error('direccion_entrega')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Teléfono Contacto *</label>
                                <input type="text" class="form-control @error('telefono_contacto') is-invalid @enderror" name="telefono_contacto" required>
                                @error('telefono_contacto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
 
                    <h5 class="mb-3">Productos</h5>
 
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" style="width: 100px;" name="productos[{{ $producto->id }}][cantidad]" value="0" min="0">
                                    </td>
                                    <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                                    <td><span class="subtotal">$0</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
 
                    <h5 class="mb-3">Costo y Pago</h5>
 
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Descuento</label>
                                <input type="number" class="form-control" name="descuento" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Costo Envío</label>
                                <input type="number" class="form-control" name="costo_envio" step="0.01" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Anticipo Recibido</label>
                                <input type="number" class="form-control" name="anticipo_recibido" step="0.01" value="0">
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Método de Pago *</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Pedido</button>
                        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 
<script>
// Calculador simple de subtotales
document.querySelectorAll('input[name*="cantidad"]').forEach(input => {
    input.addEventListener('change', function() {
        // Aquí iría la lógica de cálculo
    });
});
</script>
@endsection
 