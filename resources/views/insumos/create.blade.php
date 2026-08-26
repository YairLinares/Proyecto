@extends('layouts.app')

@section('title', 'Nuevo Insumo')

@section('content')
<div class="page-header">
    <h1 class="page-title">Registrar Nuevo Insumo</h1>
    <a href="{{ route('insumos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('insumos.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                               name="nombre" value="{{ old('nombre') }}" required autofocus>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Unidad *</label>
                            <select class="form-select @error('unidad') is-invalid @enderror"
                                    name="unidad" id="unidad" required>
                                @foreach(['Kg','Gramos','Litros','Mililitros','Unidad'] as $u)
                                    <option value="{{ $u }}" @selected(old('unidad') == $u)>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Actual *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('stock_actual') is-invalid @enderror"
                                       name="stock_actual" id="stock_actual" step="0.01" min="0"
                                       value="{{ old('stock_actual', 0) }}" required>
                                <span class="input-group-text unidad-label">Kg</span>
                                @error('stock_actual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Mínimo *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror"
                                       name="stock_minimo" step="0.01" min="0"
                                       value="{{ old('stock_minimo', 1) }}" required>
                                <span class="input-group-text unidad-label">Kg</span>
                                @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <small class="text-muted">Debajo de esto salta la alerta</small>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body py-3">
                            <p class="mb-2"><strong>¿No sabes el precio unitario?</strong> Calcúlalo:</p>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small">Pagué en total (Bs)</label>
                                    <input type="number" step="0.01" min="0" id="calc_total" class="form-control">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small">Y recibí (<span class="unidad-label">Kg</span>)</label>
                                    <input type="number" step="0.01" min="0" id="calc_cantidad" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="btn_calcular" class="btn btn-outline-primary w-100">
                                        Calcular
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Precio por <span class="unidad-label">Kg</span> *</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs</span>
                                <input type="number" class="form-control @error('precio_unitario') is-invalid @enderror"
                                       name="precio_unitario" id="precio_unitario" step="0.01" min="0"
                                       value="{{ old('precio_unitario') }}" required>
                                @error('precio_unitario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <small class="text-muted">Lo que te cuesta comprarlo</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Proveedor *</label>
                            <input type="text" class="form-control @error('proveedor') is-invalid @enderror"
                                   name="proveedor" list="lista_proveedores"
                                   value="{{ old('proveedor') }}" required>
                            <datalist id="lista_proveedores">
                                @foreach($proveedores ?? [] as $p)
                                    <option value="{{ $p }}">
                                @endforeach
                            </datalist>
                            @error('proveedor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Escribe o elige uno ya registrado</small>
                        </div>
                    </div>

                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle"></i>
                        Valor en inventario: <strong id="valor_total">Bs 0.00</strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="accion" value="guardar" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                        <button type="submit" name="accion" value="guardar_y_nuevo" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Guardar y registrar otro
                        </button>
                        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const unidad = document.getElementById('unidad');
    const precio = document.getElementById('precio_unitario');
    const stock  = document.getElementById('stock_actual');
    const labels = document.querySelectorAll('.unidad-label');

    function pintarUnidad() {
        labels.forEach(el => el.textContent = unidad.value);
    }

    function calcularValor() {
        const total = (parseFloat(precio.value) || 0) * (parseFloat(stock.value) || 0);
        document.getElementById('valor_total').textContent =
            'Bs ' + total.toLocaleString('es-BO', { minimumFractionDigits: 2 });
    }

    document.getElementById('btn_calcular').addEventListener('click', function () {
        const t = parseFloat(document.getElementById('calc_total').value);
        const c = parseFloat(document.getElementById('calc_cantidad').value);
        if (t > 0 && c > 0) {
            precio.value = (t / c).toFixed(2);
            calcularValor();
        } else {
            alert('Llena los dos campos con números mayores a cero.');
        }
    });

    unidad.addEventListener('change', pintarUnidad);
    precio.addEventListener('input', calcularValor);
    stock.addEventListener('input', calcularValor);

    pintarUnidad();
    calcularValor();
});
</script>
@endsection