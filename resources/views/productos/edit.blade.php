@extends('layouts.app')

@section('title', 'Editar ' . $producto->nombre)

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Producto: {{ $producto->nombre }}</h1>
</div>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('productos.update', $producto) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="nombre">Nombre *</label>
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria_id">Sabor *</label>
                            <select id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" name="categoria_id" required>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('categoria_id', $producto->categoria_id) == $cat->id)>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripci&oacute;n <span class="text-muted">(opcional)</span></label>
                        <textarea id="descripcion" class="form-control" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="precio_venta">Precio de venta *</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input id="precio_venta" type="number" min="0" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                            </div>
                            @error('precio_venta')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tiempo_preparacion_dias">Tiempo de preparaci&oacute;n (d&iacute;as) *</label>
                            <input id="tiempo_preparacion_dias" type="number" min="1" class="form-control @error('tiempo_preparacion_dias') is-invalid @enderror" name="tiempo_preparacion_dias" value="{{ old('tiempo_preparacion_dias', $producto->tiempo_preparacion_dias) }}" required>
                            @error('tiempo_preparacion_dias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="estado">Estado *</label>
                        <select id="estado" class="form-select" name="estado" required>
                            <option value="activo" @selected(old('estado', $producto->estado) === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $producto->estado) === 'inactivo')>Inactivo</option>
                        </select>
                    </div>

                    @php
                        $recetaActual = [];
                        foreach ($producto->insumos as $insumoReceta) {
                            $recetaActual[$insumoReceta->id] = $insumoReceta->pivot->cantidad_necesaria;
                        }
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="form-label mb-0">Insumos requeridos para una unidad *</label>
                                <div class="text-muted small">El costo se calcula con las cantidades de esta receta.</div>
                            </div>
                            <strong class="text-primary">Costo estimado: <span id="costo_receta">Bs 0,00</span></strong>
                        </div>
                        @error('insumos')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                        <div class="table-responsive border rounded">
                            <table class="table table-sm align-middle mb-0">
                                <thead><tr><th>Insumo</th><th>Unidad</th><th>Cantidad necesaria</th></tr></thead>
                                <tbody>
                                    @foreach($insumos as $insumo)
                                        <tr>
                                            <td>{{ $insumo->nombre }}</td>
                                            <td>{{ $insumo->unidad }}</td>
                                            <td><input type="number" min="0" step="0.01" class="form-control form-control-sm cantidad-insumo" data-precio="{{ $insumo->precio_unitario }}" name="insumos[{{ $insumo->id }}]" value="{{ old('insumos.' . $insumo->id, $recetaActual[$insumo->id] ?? '') }}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cantidades = document.querySelectorAll('.cantidad-insumo');
    const costo = document.getElementById('costo_receta');

    function actualizarCosto() {
        let total = 0;
        cantidades.forEach(function (input) {
            total += (parseFloat(input.value) || 0) * (parseFloat(input.dataset.precio) || 0);
        });
        costo.textContent = 'Bs ' + total.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    cantidades.forEach(function (input) { input.addEventListener('input', actualizarCosto); });
    actualizarCosto();
});
</script>
@endsection
