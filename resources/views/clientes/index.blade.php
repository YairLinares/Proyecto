@extends('layouts.app')

@section('title', 'Clientes - Delicias Dulces')

@section('content')
<style>
    .clientes-page { max-width: 1200px; margin: 0 auto; }
    .clientes-heading { display: flex; justify-content: space-between; gap: 20px; align-items: center; margin-bottom: 28px; }
    .clientes-heading h1 { margin: 0; color: #15233d; font-size: 1.45rem; font-weight: 700; }
    .clientes-heading p { margin: 4px 0 0; color: #71809a; }
    .clientes-new-btn { border-radius: 999px; background: #e91e63; border-color: #e91e63; font-weight: 700; padding: 10px 18px; white-space: nowrap; }
    .clientes-new-btn:hover { background: #c91853; border-color: #c91853; }
    .clientes-table-card { border: 1px solid #eef0f4; border-radius: 14px; box-shadow: 0 3px 14px rgba(33, 45, 70, .06); overflow: hidden; }
    .clientes-filter { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #eef0f4; }
    .clientes-search { position: relative; width: min(315px, 100%); }
    .clientes-search i { position: absolute; left: 14px; top: 50%; color: #8e9bb0; transform: translateY(-50%); }
    .clientes-search input { border-radius: 999px; padding-left: 36px; background: #fbfcfe; }
    .clientes-count { color: #8a97ab; font-size: .9rem; white-space: nowrap; }
    .clientes-filter select { max-width: 170px; margin-left: auto; }
    .clientes-table { margin: 0; vertical-align: middle; }
    .clientes-table thead th { padding: 16px; color: #63708a; background: #fbfcfe; border-bottom: 1px solid #eef0f4; font-size: .72rem; font-weight: 700; text-transform: uppercase; }
    .clientes-table tbody td { padding: 14px 16px; border-color: #f0f2f5; color: #394861; }
    .cliente-name { display: flex; align-items: center; gap: 11px; color: #15233d; font-weight: 700; }
    .cliente-initials { display: inline-grid; width: 34px; height: 34px; place-items: center; border-radius: 50%; background: #fde5ef; color: #e91e63; font-size: .78rem; font-weight: 700; }
    .cliente-type, .cliente-status { display: inline-block; border-radius: 999px; padding: 3px 11px; font-size: .78rem; font-weight: 600; }
    .cliente-type--regular { background: #edf4ff; color: #2563eb; }
    .cliente-type--vip { background: #f9e7f1; color: #d2186b; }
    .cliente-status--activo { background: #dcfce7; color: #15733a; }
    .cliente-status--inactivo { background: #eef0f3; color: #697586; }
    .cliente-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .cliente-actions .btn { border-radius: 999px; }
    @media (max-width: 767.98px) {
        .clientes-heading { align-items: flex-start; flex-direction: column; }
        .clientes-filter { flex-wrap: wrap; }
        .clientes-filter select { margin-left: 0; }
        .clientes-table-card { overflow-x: auto; }
        .clientes-table { min-width: 720px; }
    }
</style>

<div class="clientes-page">
    <div class="clientes-heading">
        <div>
            <h1><i class="fas fa-users me-2"></i>Lista de Clientes</h1>
            <p>Administra la información de todos los clientes</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary clientes-new-btn"><i class="fas fa-plus me-1"></i> Registrar Cliente</a>
    </div>

    <div class="card clientes-table-card">
        <form method="GET" class="clientes-filter">
            <div class="clientes-search">
                <i class="fas fa-search"></i>
                <input type="search" name="search" class="form-control" placeholder="Buscar cliente..." value="{{ $search }}">
            </div>
            <span class="clientes-count">{{ $clientes->total() }} {{ $clientes->total() === 1 ? 'cliente' : 'clientes' }}</span>
            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="todos">Todos los estados</option>
                <option value="activo" @selected($filter === 'activo')>Activo</option>
                <option value="inactivo" @selected($filter === 'inactivo')>Inactivo</option>
            </select>
        </form>

        <table class="table clientes-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    @php
                        $iniciales = collect(preg_split('/\s+/', trim($cliente->nombre_completo)))
                            ->take(2)
                            ->map(fn ($nombre) => mb_strtoupper(mb_substr($nombre, 0, 1)))
                            ->implode('');
                    @endphp
                    <tr>
                        <td>
                            <div class="cliente-name"><span class="cliente-initials">{{ $iniciales }}</span>{{ $cliente->nombre_completo }}</div>
                        </td>
                        <td>{{ $cliente->telefono_principal }}</td>
                        <td><span class="cliente-type {{ $cliente->tipo_cliente === 'Corporativo' ? 'cliente-type--vip' : 'cliente-type--regular' }}">{{ $cliente->tipo_cliente === 'Corporativo' ? 'Corporativo' : 'Regular' }}</span></td>
                        <td><span class="cliente-status cliente-status--{{ $cliente->estado }}">{{ ucfirst($cliente->estado) }}</span></td>
                        <td>
                            <div class="cliente-actions">
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-info" title="Ver cliente"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-light" title="Editar cliente"><i class="fas fa-pen me-1"></i>Editar</a>
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar cliente"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay clientes para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-3 pt-3">{{ $clientes->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
