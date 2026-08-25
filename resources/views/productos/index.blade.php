@extends('layouts.app')

@section('title', 'Productos - Delicias Dulces')

@section('content')
<div class="page-header">
    <h1 class="page-title">Catálogo de Productos</h1>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Buscar producto..." value="{{ $search }}">
            </div>
            <div class="col-md-6">
                <select name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" @if($categoria == $cat->id) selected @endif>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                <tr>
                    <td><strong>{{ $producto->nombre }}</strong></td>
                    <td>{{ $producto->categoria->nombre }}</td>
                    <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                    <td>
<span class="badge {{ $producto->stock_disponible <= $producto->stock_minimo ? 'badge-pending' : 'badge-completed' }}">                            {{ $producto->stock_disponible }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $producto->estado == 'activo' ? 'badge-completed' : 'badge-cancelled' }}">
                            {{ $producto->estado }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('productos.destroy', $producto) }}" style="display:inline;" onsubmit="return confirm('¿Estás seguro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay productos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $productos->links() }}
    </div>
</div>
@endsection