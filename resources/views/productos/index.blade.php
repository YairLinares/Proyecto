@extends('layouts.app')

@section('title', 'Productos - Delicias Dulces')

@section('content')
<style>
    .productos-page { max-width: 1280px; margin: 0 auto; }
    .productos-heading { display: flex; justify-content: space-between; gap: 20px; align-items: center; margin-bottom: 20px; }
    .productos-heading h1 { margin: 0; color: #15233d; font-size: 1.45rem; font-weight: 700; }
    .productos-heading p { margin: 4px 0 0; color: #71809a; }
    .productos-new-btn { border-radius: 999px; background: #e91e63; border-color: #e91e63; font-weight: 700; padding: 10px 18px; white-space: nowrap; }
    .productos-new-btn:hover { background: #c91853; border-color: #c91853; }
    .productos-filtros { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px; }
    .producto-filter { border: 0; border-radius: 999px; padding: 8px 16px; background: #f3f5f8; color: #52617a; font-size: .85rem; font-weight: 600; white-space: nowrap; }
    .producto-filter:hover, .producto-filter--active { background: #e91e63; color: #fff; }
    .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px; }
    .producto-card { display: block; border-radius: 14px; background: #fff; overflow: hidden; text-decoration: none; color: inherit; box-shadow: 0 3px 14px rgba(33, 45, 70, .06); border: 1px solid #eef0f4; transition: transform .15s ease, box-shadow .15s ease; }
    .producto-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(33, 45, 70, .12); color: inherit; }
    .producto-imagen { height: 150px; display: flex; align-items: center; justify-content: center; font-size: 64px; background: linear-gradient(135deg, #fbd6c7, #fdeec2); }
    .producto-body { padding: 16px; }
    .producto-body h3 { font-size: 1.02rem; font-weight: 700; color: #15233d; margin: 0 0 8px; }
    .producto-precio-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .producto-precio { color: #e91e63; font-weight: 800; font-size: 1.1rem; }
    .producto-stock-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 3px 10px; font-size: .74rem; font-weight: 700; }
    .producto-stock-badge i { font-size: 7px; }
    .producto-stock-badge--optimo { background: #dcfce7; color: #15733a; }
    .producto-stock-badge--bajo { background: #fff5c2; color: #955b00; }
    .producto-stock-badge--critico { background: #fee2e2; color: #b91c1c; }
    .producto-stock-row { display: flex; justify-content: space-between; align-items: center; font-size: .82rem; color: #8e9bb0; border-top: 1px solid #f0f2f5; padding-top: 10px; }
    .producto-stock-row strong { color: #394861; }
    @media (max-width: 575.98px) { .productos-heading { align-items: flex-start; flex-direction: column; } }
</style>

<div class="productos-page">
    <div class="productos-heading">
        <div>
            <h1><i class="fas fa-birthday-cake me-2"></i>Productos</h1>
            <p>Catálogo de productos elaborados por Delicias Dulces</p>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-primary productos-new-btn"><i class="fas fa-plus me-1"></i> Registrar Producto</a>
    </div>

    <form method="GET" class="productos-filtros">
        <button type="submit" name="categoria" value="" class="producto-filter {{ !$categoria ? 'producto-filter--active' : '' }}">Todos</button>
        @foreach($categorias as $cat)
            <button type="submit" name="categoria" value="{{ $cat->id }}" class="producto-filter {{ (string) $categoria === (string) $cat->id ? 'producto-filter--active' : '' }}">{{ $cat->nombre }}</button>
        @endforeach
    </form>

    <div class="productos-grid">
        @forelse($productos as $producto)
            @php
                $nombreCategoria = strtolower($producto->categoria->nombre ?? '');
                $emoji = match(true) {
                    str_contains($nombreCategoria, 'vainilla') => '🎂',
                    str_contains($nombreCategoria, 'chocolate') => '🍫',
                    str_contains($nombreCategoria, 'naranja') => '🍊',
                    str_contains($nombreCategoria, 'zanahoria') => '🥕',
                    default => '🧁',
                };

                if ($producto->stock_disponible <= $producto->stock_minimo) {
                    [$estadoTexto, $estadoClase] = ['Crítico', 'critico'];
                } elseif ($producto->stock_minimo > 0 && $producto->stock_disponible <= $producto->stock_minimo * 1.2) {
                    [$estadoTexto, $estadoClase] = ['Bajo', 'bajo'];
                } else {
                    [$estadoTexto, $estadoClase] = ['Óptimo', 'optimo'];
                }
            @endphp
            <a href="{{ route('productos.show', $producto) }}" class="producto-card">
                <div class="producto-imagen">{{ $emoji }}</div>
                <div class="producto-body">
                    <h3>{{ $producto->nombre }}</h3>
                    <div class="producto-precio-row">
                        <span class="producto-precio">Bs {{ number_format($producto->precio_venta, 0, ',', '.') }}</span>
                        <span class="producto-stock-badge producto-stock-badge--{{ $estadoClase }}"><i class="fas fa-circle"></i> {{ $estadoTexto }}</span>
                    </div>
                    <div class="producto-stock-row">
                        <span>Stock disponible</span>
                        <strong>{{ $producto->stock_disponible }} ud</strong>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-muted">No hay productos registrados</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $productos->withQueryString()->links() }}</div>
</div>
@endsection
