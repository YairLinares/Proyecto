<div class="mb-3">
    <label class="form-label" for="codigo_lote">Código del lote (entrada)</label>
    <input id="codigo_lote" name="codigo_lote" class="form-control" maxlength="100" value="{{ old('codigo_lote') }}">
    <small class="text-muted">Si lo dejas vacío, se genera un código automáticamente.</small>
    @error('codigo_lote')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label" for="fecha_vencimiento">Fecha de vencimiento (entrada)</label>
    <input id="fecha_vencimiento" type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
    <small class="text-muted">Opcional. Sin fecha se mostrará “Sin vencimiento registrado”.</small>
    @error('fecha_vencimiento')<div class="text-danger">{{ $message }}</div>@enderror
</div>
