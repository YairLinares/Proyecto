<div class="card mt-3 mb-3">
    <div class="card-header"><h5>Lotes y caducidad</h5></div>
    <div class="card-body">
        <p>Stock utilizable: <strong>{{ number_format($insumo->stockUtilizable(), 2, ',', '.') }} {{ $insumo->unidad }}</strong>. Excluye lotes vencidos.</p>
        <p class="text-muted">Aviso de vencimiento con 7 días de anticipación. Los lotes sin fecha siguen disponibles; completa la fecha cuando la conozcas.</p>
        <div class="table-responsive"><table class="table">
            <thead><tr><th>Lote</th><th>Cantidad</th><th>Estado</th><th>Vencimiento</th></tr></thead>
            <tbody>
            @forelse($insumo->lotes()->orderByRaw('fecha_vencimiento IS NULL')->orderBy('fecha_vencimiento')->get() as $lote)
                <tr>
                    <td>{{ $lote->codigo }}</td><td>{{ $lote->cantidad }} {{ $insumo->unidad }}</td>
                    <td>
                        @if($lote->cantidad <= 0)<span class="badge bg-secondary">Agotado</span>
                        @elseif(!$lote->fecha_vencimiento)<span class="badge bg-secondary">Sin vencimiento registrado</span>
                        @elseif($lote->fecha_vencimiento->lt(today()))<span class="badge bg-danger">Vencido</span>
                        @elseif($lote->fecha_vencimiento->lte(today()->addDays(7)))<span class="badge bg-warning text-dark">Próximo a vencer</span>
                        @else<span class="badge bg-success">Vigente</span>@endif
                    </td>
                    <td><form method="POST" action="{{ route('insumos.lotes.update', [$insumo, $lote->id]) }}" class="d-flex gap-2">
                        @csrf @method('PATCH')
                        <input type="date" name="fecha_vencimiento" class="form-control" aria-label="Vencimiento de {{ $lote->codigo }}" value="{{ $lote->fecha_vencimiento?->format('Y-m-d') }}">
                        <button class="btn btn-outline-primary" type="submit">Guardar</button>
                    </form></td>
                </tr>
            @empty<tr><td colspan="4">Sin lotes registrados.</td></tr>@endforelse
            </tbody>
        </table></div>
        @error('fecha_vencimiento')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
</div>
