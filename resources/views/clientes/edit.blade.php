@extends('layouts.app')

@section('title', 'Editar Cliente - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Cliente</h1>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Datos del Cliente: {{ $cliente->nombre_completo }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('clientes.update', $cliente) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control @error('nombre_completo') is-invalid @enderror" 
                               name="nombre_completo" required value="{{ old('nombre_completo', $cliente->nombre_completo) }}">
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono *</label>
                        <input type="tel" inputmode="numeric" pattern="[0-9]{8}" minlength="8" maxlength="8" class="form-control @error('telefono_principal') is-invalid @enderror" 
                               name="telefono_principal" required value="{{ old('telefono_principal', $cliente->telefono_principal) }}" oninput="this.value = this.value.replace(/\D/g, '')">
                        @error('telefono_principal')
                            <div class="invalid-feedback">El teléfono debe tener exactamente 8 dígitos numéricos.</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Cliente</label>
                        <select class="form-select" name="tipo_cliente">
                            <option value="Regular" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Corporativo" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'Corporativo' ? 'selected' : '' }}>Corporativo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="direccion" rows="2">{{ old('direccion', $cliente->direccion) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <select class="form-select @error('estado') is-invalid @enderror" name="estado" required>
                            <option value="activo" {{ old('estado', $cliente->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado', $cliente->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
