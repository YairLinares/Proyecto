@extends('layouts.app')

@section('title', 'Nuevo Insumo')

@section('content')
<style>
    .insumo-page { max-width: 900px; margin: 0 auto; }
    .insumo-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
    .insumo-header h1 { margin: 0; color: #15233d; font-size: 1.65rem; font-weight: 700; }
    .insumo-header p { margin: 4px 0 0; color: #7f8ca1; }
    .insumo-form { border: 1px solid #edf0f4; border-radius: 8px; background: #fff; box-shadow: 0 8px 24px rgba(34, 48, 76, .08); padding: 28px; }
    .insumo-section-title { display: flex; align-items: center; gap: 8px; margin: 0 0 20px; color: #15233d; font-size: 1.05rem; font-weight: 700; }
    .insumo-section-title i { color: #e91e63; }
    .insumo-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .insumo-full { grid-column: 1 / -1; }
    .insumo-form .form-label { color: #34435b; font-size: .9rem; font-weight: 600; }
    .insumo-form .form-control, .insumo-form .form-select { min-height: 44px; border-color: #dfe4ec; }
    .insumo-form .form-control:focus, .insumo-form .form-select:focus { border-color: #e91e63; box-shadow: 0 0 0 .2rem rgba(233, 30, 99, .12); }
    .insumo-summary { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 24px; padding: 15px 17px; border: 1px solid #bfe6f4; border-radius: 8px; background: #eefaff; color: #24617a; }
    .insumo-summary strong { color: #15233d; }
    .insumo-actions { display: flex; gap: 10px; margin-top: 24px; }
    .insumo-save { min-width: 150px; border: 0; background: #e91e63; color: #fff; font-weight: 700; }
    .insumo-save:hover { background: #c91853; color: #fff; }
    @media (max-width: 575px) { .insumo-header { align-items: flex-start; flex-direction: column; } .insumo-grid { grid-template-columns: 1fr; } .insumo-full { grid-column: auto; } .insumo-form { padding: 20px; } .insumo-actions { flex-direction: column; } }
</style>

<div class="insumo-page">
    <div class="insumo-header">
        <div>
            <h1>Registrar Nuevo Insumo</h1>
            <p>Agrega los ingredientes y materiales que necesitas para tus productos.</p>
        </div>
        <a href="{{ route('insumos.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ route('insumos.store') }}" class="insumo-form">
        @csrf

        <h2 class="insumo-section-title"><i class="fas fa-flask"></i> Informacion del insumo</h2>

        <div class="insumo-grid">
            <div>
                <label class="form-label" for="nombre">Nombre *</label>
                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" placeholder="Ej.: Harina de trigo" required autofocus>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label" for="unidad">Unidad de medida *</label>
                <select id="unidad" class="form-select @error('unidad') is-invalid @enderror" name="unidad" required>
                    @foreach(['Kg', 'Gramos', 'Litros', 'Mililitros', 'Unidad'] as $unidad)
                        <option value="{{ $unidad }}" @selected(old('unidad', 'Kg') === $unidad)>{{ $unidad }}</option>
                    @endforeach
                </select>
                @error('unidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="insumo-full">
                <label class="form-label" for="descripcion">Descripcion</label>
                <textarea id="descripcion" class="form-control" name="descripcion" rows="3" placeholder="Ej.: Ingrediente principal para queques y tortas.">{{ old('descripcion') }}</textarea>
            </div>

            <div>
                <label class="form-label" for="stock_actual">Stock actual *</label>
                <div class="input-group">
                    <input id="stock_actual" type="number" class="form-control @error('stock_actual') is-invalid @enderror" name="stock_actual" step="0.01" min="0" value="{{ old('stock_actual', 0) }}" required>
                    <span class="input-group-text unidad-label">Kg</span>
                    @error('stock_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="stock_minimo">Stock minimo *</label>
                <div class="input-group">
                    <input id="stock_minimo" type="number" class="form-control @error('stock_minimo') is-invalid @enderror" name="stock_minimo" step="0.01" min="0" value="{{ old('stock_minimo', 1) }}" required>
                    <span class="input-group-text unidad-label">Kg</span>
                    @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <small class="text-muted">El sistema mostrara una alerta al llegar a esta cantidad.</small>
            </div>

            <div>
                <label class="form-label" for="precio_unitario">Costo por <span class="unidad-label">Kg</span> *</label>
                <div class="input-group">
                    <span class="input-group-text">Bs</span>
                    <input id="precio_unitario" type="number" class="form-control @error('precio_unitario') is-invalid @enderror" name="precio_unitario" step="0.01" min="0" value="{{ old('precio_unitario') }}" placeholder="Ej.: 8.00" required>
                    @error('precio_unitario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <small class="text-muted">El costo de una unidad de medida del insumo.</small>
            </div>
        </div>

        <div class="insumo-summary">
            <span><i class="fas fa-calculator me-1"></i> Valor inicial del inventario</span>
            <strong id="valor_total">Bs 0,00</strong>
        </div>

        <div class="insumo-actions">
            <button type="submit" class="btn insumo-save"><i class="fas fa-save"></i> Registrar insumo</button>
            <a href="{{ route('insumos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const unidad = document.getElementById('unidad');
    const stock = document.getElementById('stock_actual');
    const precio = document.getElementById('precio_unitario');
    const labels = document.querySelectorAll('.unidad-label');
    const valor = document.getElementById('valor_total');

    function actualizarUnidad() {
        labels.forEach(function (label) {
            label.textContent = unidad.value;
        });
    }

    function actualizarValor() {
        const total = (parseFloat(stock.value) || 0) * (parseFloat(precio.value) || 0);
        valor.textContent = 'Bs ' + total.toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    unidad.addEventListener('change', actualizarUnidad);
    stock.addEventListener('input', actualizarValor);
    precio.addEventListener('input', actualizarValor);
    actualizarUnidad();
    actualizarValor();
});
</script>
@endsection
