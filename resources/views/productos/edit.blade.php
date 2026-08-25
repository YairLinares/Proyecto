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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Categoría *</label>
                                <select class="form-select" name="categoria_id" required>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Precio de Venta *</label>
                                <input type="number" class="form-control" name="precio_venta" step="0.01" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Costo de Producción *</label>
                                <input type="number" class="form-control" name="costo_produccion" step="0.01" value="{{ old('costo_produccion', $producto->costo_produccion) }}" required>
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Disponible *</label>
                                <input type="number" class="form-control" name="stock_disponible" value="{{ old('stock_disponible', $producto->stock_disponible) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tiempo de Preparación (días) *</label>
                                <input type="number" class="form-control" name="tiempo_preparacion_dias" value="{{ old('tiempo_preparacion_dias', $producto->tiempo_preparacion_dias) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unidad de Medida *</label>
                                <select class="form-select" name="unidad_medida" required>
                                    <option value="Unidad" {{ $producto->unidad_medida == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                                    <option value="Kg" {{ $producto->unidad_medida == 'Kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="Gramos" {{ $producto->unidad_medida == 'Gramos' ? 'selected' : '' }}>Gramos</option>
                                    <option value="Litros" {{ $producto->unidad_medida == 'Litros' ? 'selected' : '' }}>Litros</option>
                                    <option value="Mililitros" {{ $producto->unidad_medida == 'Mililitros' ? 'selected' : '' }}>Mililitros</option>
                                </select>
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <select class="form-select" name="estado" required>
                            <option value="activo" {{ $producto->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ $producto->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 