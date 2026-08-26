@extends('layouts.app')

@section('title', 'Sabores')

@section('content')
<div class="page-header">
    <h1 class="page-title">Sabores de Queque</h1>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Sabor
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Sabor</th>
                    <th>Descripción</th>
                    <th>Queques</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                <tr>
                    <td><strong>{{ $categoria->nombre }}</strong></td>
                    <td>{{ Str::limit($categoria->descripcion, 50) ?: 'Sin descripción' }}</td>
                    <td><span class="badge badge-info">{{ $categoria->productos_count }}</span></td>
                    <td>
                        <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar el sabor {{ $categoria->nombre }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No hay sabores registrados</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $categorias->links() }}
    </div>
</div>
@endsection