@extends('layouts.app')

@section('title', 'Nuevo Sabor')

@section('content')
<div class="page-header">
    <h1 class="page-title">Registrar Nuevo Sabor</h1>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('categorias.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre del sabor *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                               name="nombre" value="{{ old('nombre') }}"
                               placeholder="Ej: Tres Leches" maxlength="60" required autofocus>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Solo el sabor, sin la palabra "queque"</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion"
                                  rows="3" maxlength="200"
                                  placeholder="Ej: Queque húmedo bañado en tres tipos de leche, con merengue">{{ old('descripcion') }}</textarea>
                        <small class="text-muted">Se muestra en el catálogo. <span id="contador">0</span>/200</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="accion" value="guardar" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear
                        </button>
                        <button type="submit" name="accion" value="guardar_y_nuevo" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Guardar y crear otro
                        </button>
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Sabores ya registrados</h6></div>
            <div class="card-body">
                @forelse($sabores ?? [] as $sabor)
                    <span class="badge bg-secondary mb-1">{{ $sabor }}</span>
                @empty
                    <p class="text-muted mb-0 small">Todavía no hay sabores registrados.</p>
                @endforelse
            </div>
        </div>

        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h6>Ideas de sabores</h6>
                <p class="small text-muted mb-0">
                    Tres Leches · Maracuyá · Dulce de Leche · Café ·
                    Plátano con nuez · Red Velvet · Marmoleado
                </p>
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