@extends('layouts.app')

@section('title', 'Nuevo pedido - Delicias Dulces')

@section('styles')
<style>
    .pedido-create { --wine: #8f0929; --gold: #ac8123; max-width: 1280px; margin: 0 auto; color: #332c29; }
    .pedido-create .pedido-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 18px; color: #5c5049; font-size: .92rem; }
    .pedido-create .pedido-breadcrumb a { color: var(--wine); text-decoration: none; }
    .pedido-create .pedido-breadcrumb i { color: var(--gold); }
    .pedido-create .pedido-header { display: flex; align-items: center; justify-content: space-between; gap: 25px; margin-bottom: 25px; }
    .pedido-create h1 { margin: 0 0 6px; color: var(--wine); font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 800; }
    .pedido-create .pedido-intro { margin: 0; color: #6a5e57; font-size: 1.02rem; }
    .pedido-create .pedido-button { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 43px; padding: 9px 20px; border: 1px solid var(--wine); border-radius: 9px; background: transparent; color: var(--wine); font-weight: 600; text-decoration: none; }
    .pedido-create .pedido-button:hover { background: #fff0e8; }
    .pedido-create .pedido-back { flex-shrink: 0; }
    .pedido-create .pedido-grid { display: grid; grid-template-columns: minmax(0, 1.9fr) minmax(280px, .95fr); gap: 25px; align-items: start; }
    .pedido-create .pedido-panel { min-width: 0; border: 1px solid #e9dfd6; border-radius: 13px; background: #fff; box-shadow: 0 5px 11px rgba(95, 47, 22, .08); }
    .pedido-create .pedido-panel__body { padding: 24px; }
    .pedido-create .pedido-title { display: flex; align-items: center; gap: 12px; margin: 0 0 20px; color: var(--wine); font-size: 1.3rem; font-weight: 750; }
    .pedido-create .pedido-title i { color: var(--gold); font-size: 1.4rem; }
    .pedido-create .form-label { margin-bottom: 7px; color: #332c29; font-size: .94rem; font-weight: 650; }
    .pedido-create .form-control, .pedido-create .form-select { min-height: 43px; border: 1px solid #cdc5bf; border-radius: 9px; color: #3f3631; }
    .pedido-create .form-control:focus, .pedido-create .form-select:focus { border-color: var(--wine); box-shadow: 0 0 0 3px rgba(143, 9, 41, .1); }
    .pedido-create .form-control[readonly] { background: #f2eee8; }
    .pedido-create .pedido-date-wrap { position: relative; }
    .pedido-create .pedido-date-wrap input { padding-right: 35px; }
    .pedido-create .pedido-date-wrap i { position: absolute; right: 14px; top: 14px; color: #766e67; }
    .pedido-create .pedido-section { margin-top: 22px; padding-top: 20px; border-top: 1px solid #e1d6cb; }
    .pedido-create .pedido-product-head { display: grid; grid-template-columns: minmax(0, 1fr) 114px 104px 27px; gap: 12px; margin-bottom: 9px; padding: 10px 12px; border-radius: 8px; background: #f4f0e9; color: var(--wine); font-size: .87rem; font-weight: 700; }
    .pedido-create .pedido-product { display: grid; grid-template-columns: minmax(0, 1fr) 114px 104px 27px; align-items: center; gap: 12px; margin-bottom: 10px; padding: 10px 12px; border-radius: 8px; background: #fcfaf8; }
    .pedido-create .producto-selector { min-width: 0; font-size: .9rem; }
    .pedido-create .pedido-quantity-controls { display: flex; overflow: hidden; border: 1px solid #d0c7be; border-radius: 8px; background: #fff; }
    .pedido-create .pedido-step { flex: 0 0 33px; height: 36px; padding: 0; border: 0; color: var(--wine); background: #fffcf8; font-size: .85rem; }
    .pedido-create .pedido-step:hover { background: #f6e9db; }
    .pedido-create .pedido-step:disabled { color: #bdb6ad; cursor: not-allowed; }
    .pedido-create .pedido-product__quantity { display: grid; place-items: center; flex: 1; min-width: 34px; border-left: 1px solid #eee5da; border-right: 1px solid #eee5da; font-weight: 650; }
    .pedido-create .pedido-price { display: block; color: #3d3030; text-align: right; font-size: .88rem; font-weight: 750; white-space: nowrap; }
    .pedido-create .pedido-price-label { display: block; margin-top: 3px; color: #817367; text-align: right; font-size: .67rem; }
    .pedido-create .pedido-product__remove { min-height: 36px; padding: 0; border: 0; background: transparent; color: #8c827a; font-size: 1rem; }
    .pedido-create .pedido-product__remove:hover { color: #c3273b; }
    .pedido-create .pedido-add { width: 100%; margin-top: 6px; padding: 10px; border: 2px dashed #a41238; border-radius: 9px; background: #fff; color: var(--wine); font-size: 1rem; font-weight: 700; }
    .pedido-create .pedido-add:hover { background: #fff6ef; }
    .pedido-create .pedido-help { margin: 11px 0 0; color: #71655b; font-size: .83rem; }
    .pedido-create .pedido-help i { margin-right: 7px; color: var(--gold); }
    .pedido-create .pedido-observaciones { font-size: 1.1rem; }
    .pedido-create .pedido-observaciones span { font-weight: 400; }
    .pedido-create .pedido-required-note { margin: 15px 0 0; font-size: .82rem; color: #66594f; }
    .pedido-create .pedido-summary-empty { padding: 13px 5px 20px; text-align: center; color: #756b64; }
    .pedido-create .pedido-summary-empty i { display: block; margin-bottom: 12px; color: #b1a596; font-size: 3rem; }
    .pedido-create .pedido-summary-empty p { margin: 0; font-size: .95rem; }
    .pedido-create .summary-row { display: flex; justify-content: space-between; gap: 12px; margin: 12px 0; font-size: .9rem; }
    .pedido-create .summary-row span { overflow-wrap: anywhere; }
    .pedido-create .summary-row strong { flex-shrink: 0; }
    .pedido-create .summary-total { padding-top: 16px; margin: 16px 0 0; border-top: 1px solid #e7ddd2; color: var(--wine); font-size: 1.35rem; font-weight: 750; }
    .pedido-create .summary-total span:last-child { font-weight: 800; }
    .pedido-create .payment-choice { display: flex; align-items: center; gap: 14px; padding: 15px 18px; border-radius: 9px; background: #f8f0e1; color: var(--wine); font-size: 1.05rem; }
    .pedido-create .payment-choice i { color: var(--gold); font-size: 1.4rem; }
    .pedido-create .payment-note { margin: 9px 0 0; color: #71655b; font-size: .83rem; }
    .pedido-create .pedido-side { display: grid; gap: 20px; }
    .pedido-create .pedido-form-actions { display: grid; gap: 12px; margin-top: 4px; }
    .pedido-create .save-order { min-height: 47px; color: #fff; background: var(--wine); font-size: 1.05rem; font-weight: 700; }
    .pedido-create .save-order:hover { color: #fff; background: #720720; }
    .pedido-create a:focus-visible, .pedido-create button:focus-visible { outline: 3px solid #d4a441; outline-offset: 3px; }
    @media (max-width: 1100px) { .pedido-create .pedido-grid { grid-template-columns: minmax(0, 1fr); } }
    @media (max-width: 575px) { .pedido-create .pedido-header { align-items: flex-start; flex-direction: column; gap: 17px; } .pedido-create .pedido-panel__body { padding: 18px 15px; } .pedido-create .pedido-title { font-size: 1.15rem; } .pedido-create .pedido-product-head { display: none; } .pedido-create .pedido-product { grid-template-columns: 114px minmax(0, 1fr) 30px; gap: 12px; } .pedido-create .producto-selector { grid-column: 1 / -1; } .pedido-create .pedido-product { padding: 12px 9px; } }
</style>
@endsection

@section('content')
<div class="pedido-create">
    <nav class="pedido-breadcrumb" aria-label="Ruta de navegación"><a href="{{ route('dashboard') }}" aria-label="Inicio"><i class="fas fa-house" aria-hidden="true"></i></a><span aria-hidden="true">/</span><a href="{{ route('pedidos.index') }}">Pedidos</a><span aria-hidden="true">/</span><span aria-current="page">Nuevo pedido</span></nav>
    <header class="pedido-header">
        <div><h1>Registrar nuevo pedido</h1><p class="pedido-intro">Completa los datos del cliente, los productos y la entrega.</p></div>
        <a href="{{ route('pedidos.index') }}" class="pedido-button pedido-back"><i class="fas fa-chevron-left" aria-hidden="true"></i> Volver al listado</a>
    </header>
    <form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm">
        @csrf
        <input type="hidden" name="tipo_pedido" value="Personalizado">
        <input type="hidden" name="prioridad" value="Normal">
        <div class="pedido-grid">
            <section class="pedido-panel" aria-labelledby="datos-pedido-title">
                <div class="pedido-panel__body">
                    <h2 class="pedido-title" id="datos-pedido-title"><i class="far fa-clipboard" aria-hidden="true"></i> Datos del pedido</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="fecha_registro">Fecha de registro</label>
                            <div class="pedido-date-wrap"><input id="fecha_registro" type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly><i class="fas fa-lock" aria-hidden="true"></i></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="fecha_entrega">Fecha de entrega <span class="text-danger" aria-hidden="true">*</span></label>
                            <input type="date" id="fecha_entrega" class="form-control @error('fecha_entrega') is-invalid @enderror" name="fecha_entrega" min="{{ now()->format('Y-m-d') }}" value="{{ old('fecha_entrega', now()->format('Y-m-d')) }}" required @error('fecha_entrega') aria-invalid="true" aria-describedby="fecha-error" @enderror>
                            @error('fecha_entrega')<div id="fecha-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="cliente_id">Cliente <span class="text-danger" aria-hidden="true">*</span></label>
                            <select id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" name="cliente_id" required @error('cliente_id') aria-invalid="true" aria-describedby="cliente-error" @enderror>
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clientes as $cliente)<option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombre_completo }}</option>@endforeach
                            </select>
                            @error('cliente_id')<div id="cliente-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <section class="pedido-section" aria-labelledby="productos-pedido-title">
                        <h2 class="pedido-title" id="productos-pedido-title"><i class="fas fa-bread-slice" aria-hidden="true"></i> Productos del pedido</h2>
                        <div class="pedido-product-head" aria-hidden="true"><span>Producto</span><span class="text-center">Cantidad</span><span class="text-end">Subtotal</span><span></span></div>
                        <div id="productosPedido"></div>
                        @error('productos')<div class="text-danger small mb-2" role="alert">{{ $message }}</div>@enderror
                        <button type="button" class="pedido-add" id="agregarProducto"><i class="fas fa-plus" aria-hidden="true"></i> Agregar producto</button>
                        <p class="pedido-help"><i class="fas fa-circle-info" aria-hidden="true"></i>Selecciona un producto para consultar su precio.</p>
                        @if($productos->isEmpty())<p class="text-danger small mt-2">No hay productos activos disponibles. Solicita al administrador que registre un producto.</p>@endif
                        <noscript><p class="text-danger mt-2">Activa JavaScript para seleccionar productos y calcular el pedido.</p></noscript>
                    </section>
                    <div class="pedido-section">
                        <label class="pedido-title pedido-observaciones" for="descripcion_especificaciones"><i class="far fa-pen-to-square" aria-hidden="true"></i> Observaciones <span>(opcional)</span></label>
                        <textarea id="descripcion_especificaciones" class="form-control" name="descripcion_especificaciones" rows="3" placeholder="Ej. Indicaciones para la entrega o notas del pedido...">{{ old('descripcion_especificaciones') }}</textarea>
                    </div>
                    <p class="pedido-required-note"><span class="text-danger" aria-hidden="true">*</span> Campos obligatorios</p>
                </div>
            </section>
            <aside class="pedido-side" aria-label="Resumen y pago">
                <section class="pedido-panel" aria-labelledby="resumen-title"><div class="pedido-panel__body">
                    <h2 class="pedido-title" id="resumen-title"><i class="fas fa-receipt" aria-hidden="true"></i> Resumen del pedido</h2>
                    <div id="resumenProductos" aria-live="polite"></div>
                    <div class="summary-row summary-total"><span>Total</span><span id="totalPedido" aria-live="polite">Bs 0,00</span></div>
                </div></section>
                <section class="pedido-panel" aria-labelledby="pago-title"><div class="pedido-panel__body">
                    <h2 class="pedido-title" id="pago-title"><i class="fas fa-wallet" aria-hidden="true"></i> Pago</h2>
                    <p class="form-label">Método de pago</p>
                    <div class="payment-choice"><i class="fas fa-money-bill-wave" aria-hidden="true"></i><strong>Efectivo</strong></div>
                    <input type="hidden" name="metodo_pago" value="Efectivo">
                    <p class="payment-note">Único método disponible por ahora.</p>
                </div></section>
                <div class="pedido-form-actions">
                    <button type="submit" class="pedido-button save-order"><i class="fas fa-save" aria-hidden="true"></i> Guardar pedido</button>
                    <a href="{{ route('pedidos.index') }}" class="pedido-button"><i class="fas fa-times" aria-hidden="true"></i> Cancelar</a>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const productosDisponibles = @json($productos->map(fn ($producto) => ['id' => $producto->id, 'nombre' => $producto->nombre, 'precio' => (float) $producto->precio_venta])->values());
    const productosAnteriores = @json(old('productos', []));
    const contenedorProductos = document.getElementById('productosPedido');
    const formatoNumero = new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const moneda = importe => `Bs ${formatoNumero.format(importe)}`;
    const escaparHtml = valor => String(valor).replace(/[&<>"']/g, caracter => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[caracter]);
    const resumenVacio = '<div class="pedido-summary-empty"><i class="fas fa-basket-shopping" aria-hidden="true"></i><p>Agrega un producto para ver el resumen.</p></div>';

    function productoPorId(id) {
        return productosDisponibles.find(producto => Number(producto.id) === Number(id));
    }

    function crearFila(productoId = '', cantidad = 1) {
        const fila = document.createElement('div');
        fila.className = 'pedido-product';
        const selector = document.createElement('select');
        selector.className = 'form-select producto-selector';
        selector.required = true;
        selector.setAttribute('aria-label', 'Producto del pedido');
        selector.append(new Option('Seleccionar producto...', ''));
        productosDisponibles.forEach(producto => selector.append(new Option(producto.nombre, producto.id, false, Number(productoId) === Number(producto.id))));

        const controlesCantidad = document.createElement('div');
        controlesCantidad.className = 'pedido-quantity-controls';
        const menos = document.createElement('button');
        menos.type = 'button'; menos.className = 'pedido-step'; menos.setAttribute('aria-label', 'Disminuir cantidad'); menos.innerHTML = '<i class="fas fa-minus" aria-hidden="true"></i>';
        const cantidadTexto = document.createElement('span');
        cantidadTexto.className = 'pedido-product__quantity';
        cantidadTexto.setAttribute('aria-label', 'Cantidad');
        const mas = document.createElement('button');
        mas.type = 'button'; mas.className = 'pedido-step'; mas.setAttribute('aria-label', 'Aumentar cantidad'); mas.innerHTML = '<i class="fas fa-plus" aria-hidden="true"></i>';
        controlesCantidad.append(menos, cantidadTexto, mas);
        const precio = document.createElement('div'); precio.className = 'pedido-price-wrap';
        const eliminar = document.createElement('button');
        eliminar.type = 'button'; eliminar.className = 'pedido-product__remove'; eliminar.setAttribute('aria-label', 'Quitar producto'); eliminar.innerHTML = '<i class="far fa-trash-can" aria-hidden="true"></i>';
        const cantidadInput = document.createElement('input');
        cantidadInput.type = 'hidden'; cantidadInput.value = Math.max(1, Math.floor(Number(cantidad) || 1));

        function actualizarFila() {
            const producto = productoPorId(selector.value);
            const valorCantidad = Math.max(1, Math.floor(Number(cantidadInput.value) || 1));
            cantidadInput.value = valorCantidad;
            cantidadTexto.textContent = valorCantidad;
            menos.disabled = valorCantidad <= 1;
            cantidadInput.name = producto ? `productos[${producto.id}][cantidad]` : '';
            precio.innerHTML = `<span class="pedido-price">${moneda(producto ? producto.precio * valorCantidad : 0)}</span>` + (producto ? `<span class="pedido-price-label">${moneda(producto.precio)} / unidad</span>` : '');
            actualizarResumen();
        }

        selector.addEventListener('change', () => {
            const repetido = [...contenedorProductos.querySelectorAll('.producto-selector')].some(otro => otro !== selector && otro.value === selector.value && selector.value);
            if (repetido) { selector.value = ''; alert('Este producto ya está agregado al pedido. Ajusta su cantidad en la fila existente.'); }
            actualizarFila();
        });
        menos.addEventListener('click', () => { cantidadInput.value = Math.max(1, Number(cantidadInput.value) - 1); actualizarFila(); });
        mas.addEventListener('click', () => { cantidadInput.value = Number(cantidadInput.value) + 1; actualizarFila(); });
        eliminar.addEventListener('click', () => { fila.remove(); if (!contenedorProductos.children.length) crearFila(); else actualizarResumen(); });
        fila.append(selector, controlesCantidad, precio, eliminar, cantidadInput);
        contenedorProductos.append(fila);
        actualizarFila();
        return selector;
    }

    function actualizarResumen() {
        let subtotal = 0;
        const lineas = [];
        contenedorProductos.querySelectorAll('.pedido-product').forEach(fila => {
            const producto = productoPorId(fila.querySelector('.producto-selector').value);
            const cantidad = Number(fila.querySelector('input[type="hidden"]').value);
            if (producto) {
                const importe = producto.precio * cantidad;
                subtotal += importe;
                lineas.push(`<div class="summary-row"><span>${cantidad} × ${escaparHtml(producto.nombre)}</span><strong>${moneda(importe)}</strong></div>`);
            }
        });
        document.getElementById('resumenProductos').innerHTML = lineas.length ? lineas.join('') : resumenVacio;
        document.getElementById('totalPedido').textContent = moneda(subtotal);
    }

    document.getElementById('agregarProducto').addEventListener('click', () => crearFila().focus());
    const filasAnteriores = productosAnteriores && typeof productosAnteriores === 'object' ? Object.entries(productosAnteriores) : [];
    if (filasAnteriores.length) filasAnteriores.forEach(([id, datos]) => crearFila(id, datos.cantidad));
    else crearFila();
</script>
@endsection
