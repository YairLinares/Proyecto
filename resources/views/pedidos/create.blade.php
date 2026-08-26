@extends('layouts.app')

@section('title', 'Nuevo Pedido - Delicias Dulces')

@section('content')
<style>
    .pedido-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(280px, .9fr); gap: 24px; align-items: start; }
    .pedido-panel { border: 1px solid #edf0f3; border-radius: 8px; background: #fff; box-shadow: 0 2px 5px rgba(24, 39, 75, .08); }
    .pedido-panel__body { padding: 24px; }
    .pedido-title { color: #16233d; font-size: 1.1rem; font-weight: 700; margin: 0 0 20px; }
    .pedido-title i { color: #e91e63; margin-right: 8px; }
    .pedido-product { display: grid; grid-template-columns: minmax(150px, 1fr) auto auto minmax(70px, auto) minmax(76px, auto) auto; align-items: center; gap: 10px; padding: 12px; margin-bottom: 10px; background: #f8f9fb; border-radius: 8px; }
    .pedido-product__quantity { min-width: 22px; text-align: center; font-weight: 700; }
    .pedido-step { width: 32px; height: 32px; padding: 0; border-radius: 50%; }
    .pedido-price-label { color: #8490a3; font-size: .73rem; display: block; }
    .pedido-price { color: #16233d; font-weight: 700; font-size: .9rem; white-space: nowrap; }
    .pedido-product__remove { color: #9aa4b2; border: 0; background: transparent; }
    .pedido-add { width: 100%; border: 2px dashed #dce2ea; color: #8b98aa; font-weight: 600; background: #fff; border-radius: 8px; padding: 9px; }
    .pedido-add:hover { border-color: #e91e63; color: #e91e63; }
    .summary-row { display: flex; justify-content: space-between; gap: 12px; margin: 10px 0; color: #536176; }
    .summary-total { border-top: 1px solid #edf0f3; padding-top: 14px; margin-top: 14px; color: #16233d; font-size: 1.05rem; font-weight: 700; }
    .summary-total span:last-child { color: #e91e63; }
    .payment-choice { background: #effcf4; border: 1px solid #cef2dc; border-radius: 8px; padding: 14px; color: #15733a; }
    .payment-choice small { display: block; color: #329458; }
    .save-order { width: 100%; border-radius: 8px; background: #e91e63; border-color: #e91e63; font-weight: 700; }
    .save-order:hover { background: #c91853; border-color: #c91853; }
    @media (max-width: 991.98px) { .pedido-grid { grid-template-columns: 1fr; } }
    @media (max-width: 575.98px) { .pedido-panel__body { padding: 16px; } .pedido-product { grid-template-columns: 1fr auto auto auto; } .pedido-price-wrap { grid-column: span 2; } }
</style>

<div class="page-header">
    <h1 class="page-title">Registrar Nuevo Pedido</h1>
</div>

<form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm">
    @csrf
    <input type="hidden" name="tipo_pedido" value="Personalizado">
    <input type="hidden" name="prioridad" value="Normal">

    <div class="pedido-grid">
        <section class="pedido-panel">
            <div class="pedido-panel__body">
                <h2 class="pedido-title"><i class="fas fa-clipboard-list"></i>Datos del Pedido</h2>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha (automática)</label>
                        <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" name="cliente_id" required>
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h2 class="pedido-title mt-2"><i class="fas fa-birthday-cake"></i>Productos del Pedido</h2>
                <div id="productosPedido"></div>
                @error('productos')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <button type="button" class="pedido-add" id="agregarProducto"><i class="fas fa-plus"></i> Agregar producto</button>

                <div class="mt-4">
                    <label class="form-label" for="descripcion_especificaciones"><i class="fas fa-edit me-1"></i>Observaciones</label>
                    <textarea id="descripcion_especificaciones" class="form-control" name="descripcion_especificaciones" rows="4" placeholder="Notas adicionales, personalizaciones, color de decoración...">{{ old('descripcion_especificaciones') }}</textarea>
                </div>

            </div>
        </section>

        <aside>
            <section class="pedido-panel mb-3">
                <div class="pedido-panel__body">
                    <h2 class="pedido-title"><i class="fas fa-sack-dollar"></i>Resumen</h2>
                    <div id="resumenProductos" class="small text-muted">Agrega un producto al pedido.</div>
                    <div class="summary-row summary-total"><span>Total</span><span id="totalPedido">Bs 0.00</span></div>
                </div>
            </section>

            <section class="pedido-panel mb-3">
                <div class="pedido-panel__body">
                    <h2 class="pedido-title"><i class="fas fa-money-bill-wave"></i>Pago</h2>
                    <div class="payment-choice">
                        <label class="form-label mb-1" for="metodo_pago">Método de pago</label>
                        <select id="metodo_pago" name="metodo_pago" class="form-select border-0 bg-transparent px-0" required>
                            <option value="Efectivo" @selected(old('metodo_pago', 'Efectivo') === 'Efectivo')>Efectivo</option>
                            <option value="Tarjeta" @selected(old('metodo_pago') === 'Tarjeta')>Tarjeta</option>
                            <option value="Transferencia" @selected(old('metodo_pago') === 'Transferencia')>Transferencia</option>
                        </select>
                        <small>Selecciona cómo realizará el pago.</small>
                    </div>
                </div>
            </section>

            <button type="submit" class="btn btn-primary save-order mb-2"><i class="fas fa-save"></i> Guardar Pedido</button>
            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times"></i> Cancelar</a>
        </aside>
    </div>
</form>

<script>
    const productosDisponibles = @json($productos->map(fn ($producto) => ['id' => $producto->id, 'nombre' => $producto->nombre, 'precio' => (float) $producto->precio_venta])->values());
    const contenedorProductos = document.getElementById('productosPedido');
    const moneda = new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB', minimumFractionDigits: 2 });
    const escaparHtml = valor => String(valor).replace(/[&<>"']/g, caracter => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[caracter]);

    function productoPorId(id) {
        return productosDisponibles.find(producto => producto.id === Number(id));
    }

    function crearFila(productoId = '', cantidad = 1) {
        const fila = document.createElement('div');
        fila.className = 'pedido-product';
        const selector = document.createElement('select');
        selector.className = 'form-select producto-selector';
        selector.required = true;
        selector.append(new Option('Seleccionar producto...', ''));
        productosDisponibles.forEach(producto => selector.append(new Option(producto.nombre, producto.id, false, Number(productoId) === producto.id)));

        const menos = document.createElement('button');
        menos.type = 'button'; menos.className = 'btn btn-light pedido-step'; menos.innerHTML = '<i class="fas fa-minus"></i>';
        const cantidadTexto = document.createElement('span');
        cantidadTexto.className = 'pedido-product__quantity';
        const mas = document.createElement('button');
        mas.type = 'button'; mas.className = 'btn btn-danger pedido-step'; mas.innerHTML = '<i class="fas fa-plus"></i>';
        const precio = document.createElement('div'); precio.className = 'pedido-price-wrap text-end';
        const eliminar = document.createElement('button');
        eliminar.type = 'button'; eliminar.className = 'pedido-product__remove'; eliminar.title = 'Quitar producto'; eliminar.innerHTML = '<i class="fas fa-trash"></i>';
        const cantidadInput = document.createElement('input');
        cantidadInput.type = 'hidden'; cantidadInput.value = cantidad;

        function actualizarFila() {
            const producto = productoPorId(selector.value);
            const valorCantidad = Math.max(1, Number(cantidadInput.value) || 1);
            cantidadInput.value = valorCantidad;
            cantidadTexto.textContent = valorCantidad;
            cantidadInput.name = producto ? `productos[${producto.id}][cantidad]` : '';
            precio.innerHTML = producto ? `<span class="pedido-price-label">P.U.</span><span class="pedido-price">${moneda.format(producto.precio)}</span>` : '<span class="pedido-price-label">Selecciona un producto</span>';
            actualizarResumen();
        }

        selector.addEventListener('change', () => {
            const repetido = [...document.querySelectorAll('.producto-selector')].some(otro => otro !== selector && otro.value === selector.value && selector.value);
            if (repetido) { selector.value = ''; alert('Este producto ya está agregado al pedido.'); }
            actualizarFila();
        });
        menos.addEventListener('click', () => { cantidadInput.value = Math.max(1, Number(cantidadInput.value) - 1); actualizarFila(); });
        mas.addEventListener('click', () => { cantidadInput.value = Number(cantidadInput.value) + 1; actualizarFila(); });
        eliminar.addEventListener('click', () => { fila.remove(); actualizarResumen(); });
        fila.append(selector, menos, cantidadTexto, mas, precio, cantidadInput, eliminar);
        contenedorProductos.append(fila);
        actualizarFila();
    }

    function actualizarResumen() {
        let subtotal = 0;
        const lineas = [];
        document.querySelectorAll('.pedido-product').forEach(fila => {
            const producto = productoPorId(fila.querySelector('.producto-selector').value);
            const cantidad = Number(fila.querySelector('input[type="hidden"]').value);
            if (producto) { const importe = producto.precio * cantidad; subtotal += importe; lineas.push(`<div class="summary-row"><span>${cantidad}x ${escaparHtml(producto.nombre)}</span><strong>${moneda.format(importe)}</strong></div>`); }
        });
        document.getElementById('resumenProductos').innerHTML = lineas.length ? lineas.join('') : 'Agrega un producto al pedido.';
        document.getElementById('totalPedido').textContent = moneda.format(subtotal);
    }

    document.getElementById('agregarProducto').addEventListener('click', () => crearFila());
    crearFila();
</script>
@endsection
