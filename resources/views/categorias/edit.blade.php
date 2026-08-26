@extends('layouts.app')

@section('title', 'Editar ' . $categoria->nombre)

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar sabor: {{ $categoria->nombre }}</h1>
    <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-7 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.update', $categoria) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nombre del sabor *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                               name="nombre" value="{{ old('nombre', $categoria->nombre) }}"
                               maxlength="60" required autofocus>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion"
                                  rows="3" maxlength="200">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        <small class="text-muted"><span id="contador">0</span>/200</small>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const campo = document.getElementById('descripcion');
    const cont  = document.getElementById('contador');
    const pintar = () => cont.textContent = campo.value.length;
    campo.addEventListener('input', pintar);
    pintar();
});
</script>
@endsection