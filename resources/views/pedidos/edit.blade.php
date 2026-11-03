@extends('layouts.app')

@section('title', 'Editar ' . $pedido->codigo_pedido)

@section('styles')
<style>
    .pedido-edit { --wine: #8f0929; --gold: #ad8127; max-width: 1200px; margin: 0 auto; color: #3f3632; }
    .pedido-edit .edit-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 22px; font-size: .9rem; }
    .pedido-edit .edit-breadcrumb a { color: var(--wine); text-decoration: none; }
    .pedido-edit .edit-breadcrumb i { color: var(--gold); }
    .pedido-edit .edit-breadcrumb strong { color: var(--wine); }
    .pedido-edit .edit-heading { display: flex; justify-content: space-between; align-items: center; gap: 23px; margin-bottom: 28px; }
    .pedido-edit h1 { margin: 0 0 6px; color: #790d23; font-size: clamp(1.7rem, 3vw, 2.5rem); font-weight: 800; }
    .pedido-edit .edit-intro { margin: 0; color: #6c625c; font-size: 1rem; }
    .pedido-edit .edit-button { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 44px; padding: 9px 20px; border: 1px solid var(--wine); border-radius: 9px; color: var(--wine); background: transparent; font-size: .96rem; text-decoration: none; }
    .pedido-edit .edit-button:hover { background: #fff0e8; color: #720720; }
    .pedido-edit .edit-back { flex-shrink: 0; font-weight: 650; }
    .pedido-edit .edit-card { padding: 25px 29px 23px; border: 1px solid #e8dfd6; border-radius: 17px; background: #fff; box-shadow: 0 8px 20px rgba(95, 47, 22, .1); }
    .pedido-edit .edit-section-title { display: flex; align-items: center; gap: 17px; margin: 0 0 21px; color: var(--wine); font-size: 1.3rem; font-weight: 750; }
    .pedido-edit .edit-section-title i { width: 28px; color: var(--gold); font-size: 1.7rem; text-align: center; }
    .pedido-edit .edit-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 27px; }
    .pedido-edit .edit-delivery-grid { grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); }
    .pedido-edit .form-label { display: block; margin-bottom: 8px; color: #790d23; font-size: .94rem; font-weight: 650; }
    .pedido-edit .form-control, .pedido-edit .form-select { min-height: 44px; border: 1px solid #cdc3bb; border-radius: 9px; color: #3c332f; }
    .pedido-edit .form-control:focus, .pedido-edit .form-select:focus { border-color: var(--wine); box-shadow: 0 0 0 3px rgba(143, 9, 41, .1); }
    .pedido-edit .form-control.is-invalid, .pedido-edit .form-select.is-invalid { border-color: #b42338; }
    .pedido-edit .edit-section { margin-top: 25px; padding-top: 21px; border-top: 1px solid #d9bc97; }
    .pedido-edit .edit-payment { max-width: calc(50% - 14px); }
    .pedido-edit .edit-payment-field { position: relative; }
    .pedido-edit .edit-payment-field i { position: absolute; left: 18px; top: 13px; color: var(--gold); font-size: 1.2rem; }
    .pedido-edit .edit-payment-field input { padding-left: 53px; background: #f2eee8; }
    .pedido-edit .edit-help { margin: 8px 0 0; color: #776b62; font-size: .83rem; }
    .pedido-edit .edit-footer { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #d9bc97; }
    .pedido-edit .edit-required { margin: 0; color: #61564e; font-size: .88rem; }
    .pedido-edit .edit-actions { display: flex; gap: 16px; }
    .pedido-edit .edit-cancel { border-color: #81766e; color: #625750; background: #fff; }
    .pedido-edit .edit-save { border-color: var(--wine); color: #fff; background: var(--wine); }
    .pedido-edit .edit-save:hover { color: #fff; background: #720720; }
    .pedido-edit a:focus-visible, .pedido-edit button:focus-visible { outline: 3px solid #d4a441; outline-offset: 3px; }
    @media (max-width: 767px) { .pedido-edit .edit-heading { flex-direction: column; align-items: flex-start; gap: 17px; } .pedido-edit .edit-grid, .pedido-edit .edit-delivery-grid { grid-template-columns: minmax(0, 1fr); } .pedido-edit .edit-payment { max-width: none; } .pedido-edit .edit-card { padding: 22px 19px; } .pedido-edit .edit-footer { align-items: flex-start; flex-direction: column; } .pedido-edit .edit-actions { align-self: flex-end; } }
    @media (max-width: 450px) { .pedido-edit .edit-actions { width: 100%; flex-direction: column-reverse; gap: 10px; } .pedido-edit .edit-button { width: 100%; } .pedido-edit .edit-section-title { font-size: 1.12rem; gap: 12px; } }
</style>
@endsection

@section('content')
@php
    $estadosPermitidos = match ($pedido->estado) {
        'Pendiente' => ['Pendiente', 'En proceso', 'Cancelado'],
        'En proceso' => ['En proceso', 'Completado', 'Cancelado'],
        'Completado' => ['Completado', 'Cancelado'],
        default => ['Cancelado'],
    };
    $estadoSeleccionado = old('estado', $pedido->estado);
    if (!in_array($estadoSeleccionado, $estadosPermitidos, true)) $estadoSeleccionado = $pedido->estado;
@endphp
<div class="pedido-edit">
    <nav class="edit-breadcrumb" aria-label="Ruta de navegación"><a href="{{ route('dashboard') }}" aria-label="Inicio"><i class="fas fa-house" aria-hidden="true"></i></a><span aria-hidden="true">/</span><a href="{{ route('pedidos.index') }}">Pedidos</a><span aria-hidden="true">/</span><a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->codigo_pedido }}</a><span aria-hidden="true">/</span><strong aria-current="page">Editar</strong></nav>
    <header class="edit-heading">
        <div><h1>Editar pedido: {{ $pedido->codigo_pedido }}</h1><p class="edit-intro">Actualiza la información del pedido y los datos de entrega.</p></div>
        <a href="{{ route('pedidos.index') }}" class="edit-button edit-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al listado</a>
    </header>
    <form method="POST" action="{{ route('pedidos.update', $pedido) }}" class="edit-card">
        @csrf @method('PUT')
        <input type="hidden" name="tipo_pedido" value="{{ $pedido->tipo_pedido }}">
        <section aria-labelledby="edit-order-title">
            <h2 class="edit-section-title" id="edit-order-title"><i class="fas fa-clipboard-list" aria-hidden="true"></i> Información del pedido</h2>
            <div class="edit-grid">
                <div>
                    <label class="form-label" for="edit-cliente">Cliente <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="edit-cliente" class="form-select @error('cliente_id') is-invalid @enderror" name="cliente_id" required @error('cliente_id') aria-invalid="true" aria-describedby="edit-cliente-error" @enderror>
                        @foreach($clientes as $cliente)<option value="{{ $cliente->id }}" @selected(old('cliente_id', $pedido->cliente_id) == $cliente->id)>{{ $cliente->nombre_completo }}</option>@endforeach
                    </select>
                    @error('cliente_id')<div id="edit-cliente-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label" for="edit-estado">Estado <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="edit-estado" class="form-select @error('estado') is-invalid @enderror" name="estado" required aria-describedby="edit-estado-help{{ $errors->has('estado') ? ' edit-estado-error' : '' }}" @error('estado') aria-invalid="true" @enderror>
                        @foreach($estadosPermitidos as $estadoDisponible)<option value="{{ $estadoDisponible }}" @selected($estadoSeleccionado === $estadoDisponible)>{{ $estadoDisponible }}</option>@endforeach
                    </select>
                    @error('estado')<div id="edit-estado-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    <p id="edit-estado-help" class="edit-help">@if($pedido->estado === 'Pendiente')Pasar a “En proceso” inicia la preparación y consume los insumos reservados.@elseif($pedido->estado === 'Cancelado')Este pedido está cancelado y no puede reactivarse.@else Cancelar el pedido no repone los ingredientes ya consumidos.@endif</p>
                </div>
                <div>
                    <label class="form-label" for="edit-entrega">Fecha de entrega <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="date" id="edit-entrega" class="form-control @error('fecha_entrega') is-invalid @enderror" name="fecha_entrega" value="{{ old('fecha_entrega', $pedido->fecha_entrega?->format('Y-m-d')) }}" required @error('fecha_entrega') aria-invalid="true" aria-describedby="edit-entrega-error" @enderror>
                    @error('fecha_entrega')<div id="edit-entrega-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label" for="edit-prioridad">Prioridad <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="edit-prioridad" class="form-select @error('prioridad') is-invalid @enderror" name="prioridad" required @error('prioridad') aria-invalid="true" aria-describedby="edit-prioridad-error" @enderror>
                        @foreach(['Bajo', 'Normal', 'Alto'] as $prioridad)<option value="{{ $prioridad }}" @selected(old('prioridad', $pedido->prioridad) === $prioridad)>{{ $prioridad }}</option>@endforeach
                    </select>
                    @error('prioridad')<div id="edit-prioridad-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>
        <section class="edit-section" aria-labelledby="edit-delivery-title">
            <h2 class="edit-section-title" id="edit-delivery-title"><i class="fas fa-location-dot" aria-hidden="true"></i> Datos de entrega</h2>
            <div class="edit-grid edit-delivery-grid">
                <div>
                    <label class="form-label" for="edit-direccion">Dirección <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="text" id="edit-direccion" class="form-control @error('direccion_entrega') is-invalid @enderror" name="direccion_entrega" value="{{ old('direccion_entrega', $pedido->direccion_entrega) }}" autocomplete="street-address" required @error('direccion_entrega') aria-invalid="true" aria-describedby="edit-direccion-error" @enderror>
                    @error('direccion_entrega')<div id="edit-direccion-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label" for="edit-telefono">Teléfono <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="tel" id="edit-telefono" class="form-control @error('telefono_contacto') is-invalid @enderror" name="telefono_contacto" value="{{ old('telefono_contacto', $pedido->telefono_contacto) }}" inputmode="numeric" pattern="[0-9]+" autocomplete="tel-national" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required @error('telefono_contacto') aria-invalid="true" aria-describedby="edit-telefono-error" @enderror>
                    @error('telefono_contacto')<div id="edit-telefono-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>
        <section class="edit-section" aria-labelledby="edit-payment-title">
            <h2 class="edit-section-title" id="edit-payment-title"><i class="fas fa-wallet" aria-hidden="true"></i> Pago</h2>
            <div class="edit-payment">
                <label class="form-label" for="edit-metodo-pago">Método de pago</label>
                <div class="edit-payment-field"><i class="fas fa-money-bill-wave" aria-hidden="true"></i><input id="edit-metodo-pago" type="text" class="form-control" value="Efectivo" readonly aria-describedby="edit-payment-help"></div>
                <input type="hidden" name="metodo_pago" value="Efectivo">
                <p class="edit-help" id="edit-payment-help">Método de pago registrado para este pedido.</p>
            </div>
        </section>
        <footer class="edit-footer">
            <p class="edit-required"><span class="text-danger" aria-hidden="true">*</span> Campos obligatorios</p>
            <div class="edit-actions"><a href="{{ route('pedidos.index') }}" class="edit-button edit-cancel">Cancelar</a><button type="submit" class="edit-button edit-save"><i class="fas fa-save" aria-hidden="true"></i> Guardar cambios</button></div>
        </footer>
    </form>
</div>
@endsection
