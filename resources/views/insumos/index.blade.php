@extends('layouts.app')

@section('title', 'Insumos - Delicias Dulces')

@section('content')
<style>
    .insumos-panel {
        border: 1px solid #edf0f4;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(26, 35, 57, .08);
        overflow: hidden;
    }

    .insumos-table-wrap {
        overflow-x: auto;
    }

    .insumos-table {
        margin-bottom: 0;
        min-width: 900px;
        vertical-align: middle;
    }

    .insumos-table thead th {
        padding: 16px 18px;
        border-bottom: 1px solid #dde4ee;
        color: #52617a;
        font-size: .78rem;
        letter-spacing: .02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .insumos-table tbody td {
        padding: 14px 18px;
        color: #2d3748;
        border-bottom: 1px solid #edf0f4;
    }

    .insumos-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .insumo-name {
        color: #17233d;
        font-weight: 700;
    }

    .insumo-muted {
        color: #7a879b;
        font-size: .84rem;
    }

    .insumo-actions {
        display: flex;
        gap: 7px;
        align-items: center;
        justify-content: flex-start;
        min-width: 112px;
    }

    .insumo-action {
        display: inline-grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 0;
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        transition: transform .15s ease, opacity .15s ease;
    }

    .insumo-action:hover {
        color: #fff;
        opacity: .9;
        transform: translateY(-1px);
    }

    .insumo-action--view { background: #0dcaf0; }
    .insumo-action--edit { background: #ffc107; color: #17233d; }
    .insumo-action--edit:hover { color: #17233d; }
    .insumo-action--delete { background: #dc3545; }

    .insumos-table td:last-child {
        white-space: nowrap;
    }

    .insumos-table td:last-child .btn-sm {
        display: inline-grid;
        width: 34px;
        height: 34px;
        place-items: center;
        padding: 0;
        border: 0;
        border-radius: 8px;
        vertical-align: middle;
    }

    .insumos-table td:last-child form {
        display: inline-block !important;
        margin: 0;
        vertical-align: middle;
    }

    .insumos-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        border-top: 1px solid #edf0f4;
        background: #fbfcfe;
    }

    .insumos-footer .pagination {
        margin-bottom: 0;
    }

    .insumos-footer .page-link {
        color: #c7436f;
        border-color: #e2e8f0;
    }

    .insumos-footer .page-item.active .page-link {
        background: #c7436f;
        border-color: #c7436f;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .insumos-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
<div class="page-header">
    <h1 class="page-title">Gestión de Insumos</h1>
    <a href="{{ route('insumos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Insumo</a>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Total Insumos</p>
                <h3 class="mb-0">{{ $totalInsumos }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Stock Bajo</p>
                <h3 class="mb-0" style="color: #ffc107;">{{ $stockBajo }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Agotados</p>
                <h3 class="mb-0" style="color: #dc3545;">{{ $agotados }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1">Valor en Inventario</p>
                <h3 class="mb-0" style="color: #198754;">Bs {{ number_format($valorInventario, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar insumo..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit" title="Buscar" aria-label="Buscar"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="todos">Todos los estados</option>
                    <option value="Normal" @if($filter == 'Normal') selected @endif>Normal</option>
                    <option value="Stock bajo" @if($filter == 'Stock bajo') selected @endif>Stock bajo</option>
                    <option value="Agotado" @if($filter == 'Agotado') selected @endif>Agotado</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card insumos-panel">
    <div class="card-body p-0">
        <div class="insumos-table-wrap">
        <table class="table table-hover insumos-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Stock Actual</th>
                    <th>Unidad</th>
                    <th>Precio Unitario</th>
                    <th>Usado en</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($insumos as $insumo)
                <tr>
                    <td>
                        <div class="insumo-name">{{ $insumo->nombre }}</div>
                        @if($insumo->descripcion)
                            <div class="insumo-muted">{{ Str::limit($insumo->descripcion, 55) }}</div>
                        @endif
                    </td>
                    <td>{{ number_format($insumo->stock_actual, 2, ',', '.') }}</td>
                    <td>{{ $insumo->unidad }}</td>
                    <td>Bs {{ number_format($insumo->precio_unitario, 2, ',', '.') }}</td>
                    <td>{{ $insumo->productos_count }} producto(s)</td>
                    <td>
                        <span class="badge {{ $insumo->estado == 'Normal' ? 'badge-completed' : ($insumo->estado == 'Stock bajo' ? 'badge-pending' : 'badge-cancelled') }}">
                            {{ $insumo->estado }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-sm btn-info" title="Ver detalle"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('insumos.destroy', $insumo) }}" style="display:inline;" onsubmit="return confirm('¿Seguro?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No hay insumos registrados</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="insumos-footer">
            <div class="insumo-muted">
                Mostrando {{ $insumos->firstItem() ?? 0 }} a {{ $insumos->lastItem() ?? 0 }} de {{ $insumos->total() }} insumos
            </div>
            {{ $insumos->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
