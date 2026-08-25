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
                                <select class="form-select" name="unidad" required>
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
                                <label class="form-label">Stock Actual *</label>
                                <input type="number" class="form-control" name="stock_actual" step="0.01" value="{{ $insumo->stock_actual }}" required>
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
                                <label class="form-label">Precio Unitario *</label>
                                <input type="number" class="form-control" name="precio_unitario" step="0.01" value="{{ $insumo->precio_unitario }}" required>
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Proveedor *</label>
                        <input type="text" class="form-control" name="proveedor" value="{{ $insumo->proveedor }}" required>
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
@endsection
 