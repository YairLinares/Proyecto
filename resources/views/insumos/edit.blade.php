@extends('layouts.app')
 
@section('title', 'Editar Insumo')
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Editar: {{ $insumo->nombre }}</h1>
</div>
 
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('insumos.update', $insumo) }}">
                    @csrf
                    @method('PUT')
 
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="nombre" value="{{ $insumo->nombre }}" required>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3">{{ $insumo->descripcion }}</textarea>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unidad *</label>
                                <select id="unidad" class="form-select" name="unidad" required>
                                    <option value="Kg" {{ $insumo->unidad == 'Kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="Gramos" {{ $insumo->unidad == 'Gramos' ? 'selected' : '' }}>Gramos</option>
                                    <option value="Litros" {{ $insumo->unidad == 'Litros' ? 'selected' : '' }}>Litros</option>
                                    <option value="Mililitros" {{ $insumo->unidad == 'Mililitros' ? 'selected' : '' }}>Mililitros</option>
                                    <option value="Unidad" {{ $insumo->unidad == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="stock_actual">Stock físico *</label>
                                <input id="stock_actual" type="number" class="form-control" name="stock_actual" step="0.01" min="0" value="{{ old('stock_actual', $insumo->stock_actual) }}" required>
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control" name="stock_minimo" step="0.01" value="{{ $insumo->stock_minimo }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="precio_unitario">Precio unitario (Bs por <span class="unidad-label">{{ $insumo->unidad }}</span>) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input id="precio_unitario" type="number" class="form-control" name="precio_unitario" step="0.01" min="0" value="{{ old('precio_unitario', $insumo->precio_unitario) }}" required>
                                </div>
                                <small class="text-muted">Ingresa el precio que pagas en tu ciudad.</small>
                            </div>
                        </div>
                    </div>
 
                    <div class="alert alert-info d-flex justify-content-between align-items-center gap-2" role="status">
                        <span><i class="fas fa-calculator me-1"></i> Valor del stock físico (cantidad × precio unitario)</span>
                        <strong id="valor_total">Bs 0,00</strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unidad = document.getElementById('unidad');
    const stock = document.getElementById('stock_actual');
    const precio = document.getElementById('precio_unitario');
    const valor = document.getElementById('valor_total');

    function actualizar() {
        document.querySelectorAll('.unidad-label').forEach(function (label) {
            label.textContent = unidad.value;
        });
        valor.textContent = 'Bs ' + ((parseFloat(stock.value) || 0) * (parseFloat(precio.value) || 0)).toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    unidad.addEventListener('change', actualizar);
    stock.addEventListener('input', actualizar);
    precio.addEventListener('input', actualizar);
    actualizar();
});
</script>
@endsection
 
