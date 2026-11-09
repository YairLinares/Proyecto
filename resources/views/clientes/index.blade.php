@extends('layouts.app')

@section('title', 'Clientes - Delicias Dulces')

@section('styles')
<style>
    .clientes-page { --clientes-wine: #8f0929; max-width: 1200px; margin: 0 auto; }
    .clientes-breadcrumb { display: flex; gap: 10px; margin: 0 0 23px; padding: 0; list-style: none; color: #705c4f; font-size: .9rem; }
    .clientes-breadcrumb strong { color: var(--clientes-wine); }
    .clientes-breadcrumb li + li::before { content: '/'; margin-right: 10px; }
    .clientes-heading { display: flex; justify-content: space-between; gap: 20px; align-items: center; margin-bottom: 12px; }
    .clientes-heading h1 { margin: 0; color: var(--clientes-wine); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 750; }
    .clientes-heading p { margin: 6px 0 0 43px; color: #665d59; font-size: .95rem; }
    .clientes-new-btn { border-radius: 10px; background: var(--clientes-wine); border-color: var(--clientes-wine); font-weight: 650; padding: 10px 18px; white-space: nowrap; }
    .clientes-new-btn:hover { background: #730620; border-color: #730620; }
    .clientes-count { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; padding: 5px 12px; border: 1px solid #ebbf78; border-radius: 999px; background: #fff3df; color: #754416; font-size: .82rem; }
    .clientes-table-card { border: 1px solid #e8ddd3; border-radius: 12px; box-shadow: 0 6px 16px rgba(95, 47, 22, .08), 0 2px 3px rgba(95, 47, 22, .05); overflow: hidden; background: #fff; }
    .clientes-filter { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 16px 18px; border-bottom: 1px solid #e9dfd4; }
    .clientes-search { position: relative; width: min(470px, 100%); }
    .clientes-search button { position: absolute; left: 4px; top: 50%; transform: translateY(-50%); width: 36px; height: 34px; border: 0; border-radius: 7px; color: #68615c; background: transparent; }
    .clientes-search button:hover { background: #f7eee5; }
    .clientes-search input { min-height: 40px; border-color: #d9d0ca; border-radius: 12px; padding-left: 44px; background: #fcfbfa; font-size: .88rem; }
    .clientes-state-filter { display: flex; align-items: center; gap: 11px; flex-shrink: 0; }
    .clientes-state-filter label { font-size: .9rem; font-weight: 650; color: #544843; }
    .clientes-filter select { width: 185px; min-height: 38px; border-color: #d9d0ca; border-radius: 8px; background-color: #fcfbfa; }
    .clientes-filter .form-control:focus, .clientes-filter .form-select:focus { border-color: var(--clientes-wine); box-shadow: 0 0 0 3px rgba(143, 9, 41, .1); }
    .clientes-table { margin: 0; vertical-align: middle; font-size: .93rem; }
    .clientes-table thead th { padding: 12px 20px; color: var(--clientes-wine); background: #fcf3e7; border-bottom: 1px solid #e7dbd0; font-size: .9rem; font-weight: 700; white-space: nowrap; }
    .clientes-table tbody td { padding: 10px 20px; border-color: #e9e4e0; color: #514640; }
    .clientes-table tbody tr:hover td { background: #fffaf4; }
    .cliente-name { display: flex; align-items: center; gap: 13px; color: #322b28; font-weight: 650; }
    .cliente-initials { display: inline-grid; flex-shrink: 0; width: 34px; height: 34px; place-items: center; border-radius: 13px; background: #f8d9d6; color: var(--clientes-wine); font-size: .8rem; font-weight: 700; }
    .cliente-type, .cliente-status { display: inline-block; border-radius: 999px; padding: 3px 11px; font-size: .78rem; font-weight: 600; }
    .cliente-type--regular { background: #fff0d9; color: #754416; }
    .cliente-type--vip { background: #f6e4eb; color: #8f2346; }
    .cliente-status::before { content: ''; display: inline-block; width: 7px; height: 7px; margin-right: 7px; border-radius: 50%; background: currentColor; vertical-align: middle; }
    .cliente-status--activo { background: #e1f8d9; color: #287518; }
    .cliente-status--inactivo { background: #eeecea; color: #6a625c; }
    .cliente-actions { display: flex; gap: 8px; justify-content: flex-end; white-space: nowrap; }
    .cliente-actions .btn { display: inline-flex; align-items: center; justify-content: center; gap: 5px; min-height: 32px; min-width: 37px; padding: 5px 10px; border: 1px solid #d7cbc3; border-radius: 8px; background: #fff; color: #625044; font-size: .8rem; }
    .cliente-actions .btn:hover { background: #fcf2e7; border-color: #bca58d; }
    .cliente-actions .cliente-delete { border-color: #f0b9b9; background: #fff3f1; color: #d72c32; }
    .cliente-actions .cliente-delete:hover { background: #ffe4e1; border-color: #d72c32; }
    .clientes-footer { padding: 15px 20px; }
    .clientes-footer .app-pagination { justify-content: center; margin: 0; padding: 0; }
    .clientes-footer .app-pagination__summary { color: #74665c; font-size: .85rem; }
    .clientes-footer .app-pagination__summary strong { color: inherit; font-weight: 400; }
    .clientes-footer .app-pagination__link { width: 32px; height: 32px; min-width: 32px; border-radius: 8px; background: #fff; color: #705348; border-color: #e8ddd3; }
    .clientes-footer .app-pagination__link--active, .clientes-footer a.app-pagination__link:hover { background: var(--clientes-wine); border-color: var(--clientes-wine); color: #fff; }
    .clientes-footer .app-pagination__link--disabled { color: #b7aea8; background: #f6f3f1; border-color: #f6f3f1; }
    .clientes-page a:focus-visible, .clientes-page button:focus-visible { outline: 3px solid #dea52b; outline-offset: 3px; }
    @media (max-width: 767.98px) {
        .clientes-heading { align-items: flex-start; flex-direction: column; }
        .clientes-heading p { margin-left: 0; }
        .clientes-filter { align-items: stretch; flex-direction: column; gap: 12px; }
        .clientes-search { width: 100%; }
        .clientes-state-filter { justify-content: space-between; }
        .clientes-filter select { flex: 1; max-width: 240px; }
        .clientes-table { min-width: 690px; }
        .clientes-footer .app-pagination { align-items: center; gap: 14px; }
    }
</style>
@endsection

@section('content')
<div class="clientes-page">
    <nav aria-label="Ruta de navegación">
        <ol class="clientes-breadcrumb"><li><strong>Clientes</strong></li><li aria-current="page">Listado</li></ol>
    </nav>
    <div class="clientes-heading">
        <div>
            <h1><i class="fas fa-users me-2" aria-hidden="true"></i>Lista de clientes</h1>
            <p>Administra la información y el estado de tus clientes.</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary clientes-new-btn"><i class="fas fa-plus me-1" aria-hidden="true"></i> Registrar cliente</a>
    </div>
    <div class="clientes-count"><i class="fas fa-users" aria-hidden="true"></i> {{ $totalClientes }} {{ $totalClientes === 1 ? 'cliente registrado' : 'clientes registrados' }}</div>

    <div class="card clientes-table-card">
        <form method="GET" action="{{ route('clientes.index') }}" class="clientes-filter" role="search">
            <div class="clientes-search">
                <label class="visually-hidden" for="clientes-search">Buscar por nombre o teléfono</label>
                <button type="submit" aria-label="Buscar clientes"><i class="fas fa-search" aria-hidden="true"></i></button>
                <input id="clientes-search" type="search" name="search" class="form-control" placeholder="Buscar por nombre o teléfono..." value="{{ $search }}">
            </div>
            <div class="clientes-state-filter">
                <label for="clientes-estado">Estado</label>
                <select id="clientes-estado" name="filter" class="form-select form-select-sm" onchange="this.form.requestSubmit()">
                    <option value="todos" @selected($filter === 'todos')>Todos los estados</option>
                    <option value="activo" @selected($filter === 'activo')>Activo</option>
                    <option value="inactivo" @selected($filter === 'inactivo')>Inactivo</option>
                </select>
            </div>
        </form>

        <div class="table-responsive" role="region" aria-label="Listado de clientes" tabindex="0">
        <table class="table clientes-table">
            <thead>
                <tr>
                    <th scope="col">Cliente</th>
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
                            <div class="cliente-name"><span class="cliente-initials" aria-hidden="true">{{ $iniciales }}</span>{{ $cliente->nombre_completo }}</div>
                        </td>
                        <td>{{ $cliente->telefono_principal }}</td>
                        <td><span class="cliente-type {{ $cliente->tipo_cliente === 'Corporativo' ? 'cliente-type--vip' : 'cliente-type--regular' }}">{{ $cliente->tipo_cliente === 'Corporativo' ? 'Corporativo' : 'Regular' }}</span></td>
                        <td><span class="cliente-status cliente-status--{{ $cliente->estado }}">{{ ucfirst($cliente->estado) }}</span></td>
                        <td>
                            <div class="cliente-actions">
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn" title="Ver cliente" aria-label="Ver a {{ $cliente->nombre_completo }}"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn" aria-label="Editar a {{ $cliente->nombre_completo }}"><i class="fas fa-pen" aria-hidden="true"></i>Editar</a>
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn cliente-delete" title="Eliminar cliente" aria-label="Eliminar a {{ $cliente->nombre_completo }}"><i class="far fa-trash-can" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay clientes para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="clientes-footer">
            @if($clientes->hasPages())
                @include('components.pagination', ['paginator' => $clientes->withQueryString()])
            @else
                <nav class="app-pagination" aria-label="Paginación de clientes">
                    <ul class="app-pagination__links">
                        <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-left" aria-hidden="true"></i><span class="visually-hidden">Anterior</span></span></li>
                        <li><span class="app-pagination__link app-pagination__link--active" aria-current="page">1</span></li>
                        <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-right" aria-hidden="true"></i><span class="visually-hidden">Siguiente</span></span></li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection
