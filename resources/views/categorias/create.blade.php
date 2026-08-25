
@extends('layouts.app')
 
@section('title', 'Nueva Categoría')
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Crear Nueva Categoría</h1>
</div>
 
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.store') }}">
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
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Crear</button>
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 
 
================================
VISTA: categorias/edit.blade.php
================================
 
@extends('layouts.app')
 
@section('title', 'Editar ' . $categoria->nombre)
 
@section('content')
<div class="page-header">
    <h1 class="page-title">Editar: {{ $categoria->nombre }}</h1>
</div>
 
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.update', $categoria) }}">
                    @csrf
                    @method('PUT')
 
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" name="nombre" value="{{ $categoria->nombre }}" required>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3">{{ $categoria->descripcion }}</textarea>
                    </div>
 
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                        <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
 