
@extends('layouts.app')
 
@section('title', 'Nuevo Insumo')
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Registrar Nuevo Insumo</h1>
</div>
 
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('insumos.store') }}">
                    @csrf
 
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" required>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unidad *</label>
                                <select class="form-select @error('unidad') is-invalid @enderror" name="unidad" required>
                                    <option value="Kg">Kg</option>
                                    <option value="Gramos">Gramos</option>
                                    <option value="Litros">Litros</option>
                                    <option value="Mililitros">Mililitros</option>
                                    <option value="Unidad">Unidad</option>
                                </select>
                                @error('unidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Actual *</label>
                                <input type="number" class="form-control @error('stock_actual') is-invalid @enderror" name="stock_actual" step="0.01" required>
                                @error('stock_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" name="stock_minimo" step="0.01" required>
                                @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Precio Unitario *</label>
                                <input type="number" class="form-control @error('precio_unitario') is-invalid @enderror" name="precio_unitario" step="0.01" required>
                                @error('precio_unitario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Proveedor *</label>
                        <input type="text" class="form-control @error('proveedor') is-invalid @enderror" name="proveedor" required>
                        @error('proveedor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
                        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 