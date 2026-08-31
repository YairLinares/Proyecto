@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title mb-1">Movimiento de Inventario</h1>
            <p class="text-muted mb-0">{{ $insumo->nombre }}: {{ number_format($insumo->stock_actual, 2, ',', '.') }} {{ $insumo->unidad }} disponibles</p>
        </div>
        <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('insumos.movimientos.store', $insumo) }}">
                @csrf

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de movimiento *</label>
                    <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="Entrada" @selected(old('tipo') === 'Entrada')>Entrada: compra o reposicion</option>
                        <option value="Salida" @selected(old('tipo') === 'Salida')>Salida: merma o uso manual</option>
                        <option value="Ajuste" @selected(old('tipo') === 'Ajuste')>Ajuste: corregir el stock contado</option>
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="cantidad" id="cantidad-label" class="form-label">Cantidad a registrar *</label>
                    <div class="input-group">
                        <input id="cantidad" type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" min="0" step="0.01" value="{{ old('cantidad') }}" required>
                        <span class="input-group-text">{{ $insumo->unidad }}</span>
                        @error('cantidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <small id="cantidad-help" class="text-muted">Indica la cantidad que ingresara o saldra del inventario.</small>
                </div>

                <div class="mb-4">
                    <label for="motivo" class="form-label">Motivo *</label>
                    <input id="motivo" type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror" value="{{ old('motivo') }}" placeholder="Ej.: Compra en mercado, merma por vencimiento" maxlength="255" required>
                    @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar movimiento</button>
                <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipo = document.getElementById('tipo');
    const label = document.getElementById('cantidad-label');
    const help = document.getElementById('cantidad-help');

    function actualizarTexto() {
        if (tipo.value === 'Ajuste') {
            label.textContent = 'Nuevo stock contado *';
            help.textContent = 'Escribe el stock final que contaste fisicamente.';
            return;
        }

        label.textContent = 'Cantidad a registrar *';
        help.textContent = tipo.value === 'Entrada'
            ? 'Indica la cantidad que ingresara al inventario.'
            : 'Indica la cantidad que saldra del inventario.';
    }

    tipo.addEventListener('change', actualizarTexto);
    actualizarTexto();
});
</script>
@endsection
