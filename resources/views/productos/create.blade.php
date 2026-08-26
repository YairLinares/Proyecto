@extends('layouts.app')

@section('title', 'Nuevo Producto - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Nuevo Producto</h1>
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a productos
    </a>
</div>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('productos.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="nombre">Nombre *</label>
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria_id">Categoría *</label>
                            <select id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" name="categoria_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('categoria_id') == $cat->id)>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion" class="form-control" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="precio_venta">Precio de venta *</label>
                            <input id="precio_venta" type="number" min="0" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" name="precio_venta" value="{{ old('precio_venta') }}" required>
                            @error('precio_venta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="costo_produccion">Costo de producción *</label>
                            <input id="costo_produccion" type="number" min="0" step="0.01" class="form-control @error('costo_produccion') is-invalid @enderror" name="costo_produccion" value="{{ old('costo_produccion') }}" required>
                            @error('costo_produccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="stock_disponible">Stock disponible *</label>
                            <input id="stock_disponible" type="number" min="0" class="form-control @error('stock_disponible') is-invalid @enderror" name="stock_disponible" value="{{ old('stock_disponible', 0) }}" required>
                            @error('stock_disponible')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="stock_minimo">Stock mínimo *</label>
                            <input id="stock_minimo" type="number" min="0" class="form-control @error('stock_minimo') is-invalid @enderror" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" required>
                            @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="tiempo_preparacion_dias">Tiempo de preparación (días) *</label>
                            <input id="tiempo_preparacion_dias" type="number" min="1" class="form-control @error('tiempo_preparacion_dias') is-invalid @enderror" name="tiempo_preparacion_dias" value="{{ old('tiempo_preparacion_dias', 1) }}" required>
                            @error('tiempo_preparacion_dias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="unidad_medida">Unidad de medida *</label>
                            <select id="unidad_medida" class="form-select @error('unidad_medida') is-invalid @enderror" name="unidad_medida" required>
                                @foreach(['Unidad', 'Kg', 'Gramos', 'Litros', 'Mililitros'] as $unidad)
                                    <option value="{{ $unidad }}" @selected(old('unidad_medida', 'Unidad') === $unidad)>{{ $unidad }}</option>
                                @endforeach
                            </select>
                            @error('unidad_medida')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @if($insumos->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label">Insumos requeridos</label>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Insumo</th><th>Unidad</th><th>Cantidad necesaria</th></tr></thead>
                                    <tbody>
                                        @foreach($insumos as $insumo)
                                            <tr>
                                                <td>{{ $insumo->nombre }}</td>
                                                <td>{{ $insumo->unidad }}</td>
                                                <td><input type="number" min="0" step="0.01" class="form-control form-control-sm" name="insumos[{{ $insumo->id }}]" value="{{ old('insumos.' . $insumo->id) }}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar producto</button>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
