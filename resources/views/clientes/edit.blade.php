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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control @error('nombre_completo') is-invalid @enderror" 
                                       name="nombre_completo" required value="{{ old('nombre_completo', $cliente->nombre_completo) }}">
                                @error('nombre_completo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Documento *</label>
                                <select class="form-select @error('tipo_documento') is-invalid @enderror" 
                                        name="tipo_documento" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Cédula de Ciudadanía" {{ old('tipo_documento', $cliente->tipo_documento) == 'Cédula de Ciudadanía' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="Pasaporte" {{ old('tipo_documento', $cliente->tipo_documento) == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="NIT" {{ old('tipo_documento', $cliente->tipo_documento) == 'NIT' ? 'selected' : '' }}>NIT</option>
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número de Documento *</label>
                                <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" 
                                       name="numero_documento" required value="{{ old('numero_documento', $cliente->numero_documento) }}">
                                @error('numero_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" required value="{{ old('email', $cliente->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono Principal *</label>
                                <input type="text" class="form-control @error('telefono_principal') is-invalid @enderror" 
                                       name="telefono_principal" required value="{{ old('telefono_principal', $cliente->telefono_principal) }}">
                                @error('telefono_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono Alternativo</label>
                                <input type="text" class="form-control" name="telefono_alternativo" value="{{ old('telefono_alternativo', $cliente->telefono_alternativo) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ciudad *</label>
                                <input type="text" class="form-control @error('ciudad') is-invalid @enderror" 
                                       name="ciudad" required value="{{ old('ciudad', $cliente->ciudad) }}">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Cliente *</label>
                                <select class="form-select @error('tipo_cliente') is-invalid @enderror" 
                                        name="tipo_cliente" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Regular" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Corporativo" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'Corporativo' ? 'selected' : '' }}>Corporativo</option>
                                </select>
                                @error('tipo_cliente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección *</label>
                        <input type="text" class="form-control @error('direccion') is-invalid @enderror" 
                               name="direccion" required value="{{ old('direccion', $cliente->direccion) }}">
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas y Preferencias</label>
                        <textarea class="form-control" name="notas_preferencias" rows="4">{{ old('notas_preferencias', $cliente->notas_preferencias) }}</textarea>
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