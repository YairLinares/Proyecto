@extends('layouts.app')

@section('title', 'Registrar cliente - Delicias Dulces')

@section('styles')
<style>
    .client-create { --client-wine: #8f0929; --client-gold: #dea52b; max-width: 1240px; margin: 0 auto; color: #242424; }
    .client-create .client-breadcrumb { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin: 0 0 18px; padding: 0; list-style: none; font-size: .95rem; }
    .client-create .client-breadcrumb a { position: relative; color: var(--client-wine); font-weight: 700; text-decoration: none; }
    .client-create .client-breadcrumb a::after { content: ''; position: absolute; left: 0; right: 0; bottom: -9px; height: 3px; background: var(--client-gold); }
    .client-create .client-breadcrumb li + li::before { content: '/'; margin-right: 10px; color: #9a6b3a; }
    .client-create .client-heading { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 34px; }
    .client-create h1 { margin: 0 0 8px; color: var(--client-wine); font: 700 clamp(1.8rem, 3vw, 2.7rem)/1.15 Georgia, 'Times New Roman', serif; }
    .client-create .client-intro { margin: 0; color: #53565a; font-size: 1.04rem; line-height: 1.6; }
    .client-create .client-back { display: inline-flex; align-items: center; justify-content: center; gap: 10px; flex-shrink: 0; min-height: 44px; padding: 10px 18px; border: 1px solid var(--client-wine); border-radius: 9px; color: var(--client-wine); background: transparent; font-weight: 600; text-decoration: none; }
    .client-create .client-back:hover { background: #fff0ec; }
    .client-create .client-card { max-width: 1080px; margin: 0 auto; overflow: hidden; border: 1px solid #eadbcb; border-radius: 16px; background: #fff; box-shadow: 0 10px 28px rgba(95, 47, 22, .09), 0 2px 4px rgba(95, 47, 22, .04); }
    .client-create .client-card-heading { display: flex; align-items: center; gap: 24px; padding: 25px 30px; background: linear-gradient(110deg, #fff2df, #fff5e9); }
    .client-create .client-card-icon { display: grid; place-items: center; flex-shrink: 0; width: 68px; min-height: 64px; padding-right: 22px; border-right: 2px solid var(--client-gold); color: var(--client-wine); font-size: 2.3rem; }
    .client-create .client-card-heading h2 { margin: 0 0 4px; color: var(--client-wine); font: 700 clamp(1.45rem, 2.5vw, 2rem)/1.2 Georgia, 'Times New Roman', serif; }
    .client-create .client-card-heading p { margin: 0; color: #53565a; font-size: 1rem; }
    .client-create .client-fields { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 25px 30px; padding: 28px 30px 32px; }
    .client-create .client-field-wide { grid-column: 1 / -1; }
    .client-create .client-label { display: block; margin-bottom: 8px; font-size: 1rem; font-weight: 700; }
    .client-create .client-required { color: var(--client-wine); }
    .client-create .client-optional { color: #626467; font-weight: 400; }
    .client-create .client-input-wrap { position: relative; }
    .client-create .client-input-icon { position: absolute; top: 17px; left: 17px; color: #686b70; font-size: 1.15rem; pointer-events: none; }
    .client-create .form-control, .client-create .form-select { min-height: 52px; border: 1px solid #c9c9cc; border-radius: 10px; background-color: #fff; color: #292929; font-size: 1rem; }
    .client-create .form-control { padding: 13px 16px 13px 47px; }
    .client-create .form-select { padding-left: 18px; }
    .client-create .form-control::placeholder { color: #727479; opacity: 1; }
    .client-create .form-control:focus, .client-create .form-select:focus { border-color: var(--client-wine); box-shadow: 0 0 0 3px rgba(143, 9, 41, .1); }
    .client-create .form-control.is-invalid, .client-create .form-select.is-invalid { border-color: #b42336; }
    .client-create textarea.form-control { min-height: 114px; resize: vertical; }
    .client-create .client-help { margin: 5px 0 0; color: #626467; font-size: .88rem; }
    .client-create .client-footer { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 22px 30px; border-top: 1px solid #edcd9e; }
    .client-create .client-required-note { margin: 0; color: #57595e; font-size: .92rem; }
    .client-create .client-actions { display: flex; gap: 16px; }
    .client-create .client-button { display: inline-flex; align-items: center; justify-content: center; gap: 12px; min-height: 51px; padding: 12px 25px; border: 1px solid transparent; border-radius: 10px; font: 600 1rem/1.4 'Segoe UI', Tahoma, sans-serif; text-decoration: none; cursor: pointer; }
    .client-create .client-cancel { border-color: #77797c; color: #53565a; background: #fff; }
    .client-create .client-cancel:hover { background: #f5f4f2; }
    .client-create .client-save { border-color: var(--client-wine); color: #fff; background: var(--client-wine); }
    .client-create .client-save:hover { background: #730620; }
    .client-create a:focus-visible, .client-create button:focus-visible { outline: 3px solid var(--client-gold); outline-offset: 3px; }
    @media (max-width: 900px) {
        .client-create .client-heading { align-items: flex-start; flex-direction: column; gap: 17px; }
        .client-create .client-footer { align-items: flex-start; flex-direction: column; }
        .client-create .client-actions { align-self: flex-end; }
    }
    @media (max-width: 575px) {
        .client-create .client-fields { grid-template-columns: minmax(0, 1fr); gap: 21px; padding: 23px 19px; }
        .client-create .client-card-heading { gap: 16px; padding: 22px 19px; }
        .client-create .client-card-icon { width: 49px; padding-right: 15px; font-size: 1.7rem; }
        .client-create .client-card-heading p { font-size: .9rem; }
        .client-create .client-footer { padding: 20px 19px; }
        .client-create .client-actions { width: 100%; flex-direction: column-reverse; gap: 10px; }
        .client-create .client-button { width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="client-create">
    <nav aria-label="Ruta de navegación">
        <ol class="client-breadcrumb">
            <li><a href="{{ route('clientes.index') }}">Clientes</a></li>
            <li aria-current="page">Nuevo cliente</li>
        </ol>
    </nav>
    <header class="client-heading">
        <div>
            <h1>Registrar nuevo cliente</h1>
            <p class="client-intro">Completa los datos para registrar al cliente y gestionar sus pedidos.</p>
        </div>
        <a class="client-back" href="{{ route('clientes.index') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al listado</a>
    </header>
    <section class="client-card" aria-labelledby="client-data-title">
        <header class="client-card-heading">
            <div class="client-card-icon" aria-hidden="true"><i class="fas fa-user-plus"></i></div>
            <div>
                <h2 id="client-data-title">Datos del cliente</h2>
                <p>Información personal y de contacto</p>
            </div>
        </header>
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="client-fields">
                <div class="client-field-wide">
                    <label class="client-label" for="nombre_completo">Nombre completo <span class="client-required" aria-hidden="true">*</span></label>
                    <div class="client-input-wrap">
                        <i class="far fa-user client-input-icon" aria-hidden="true"></i>
                        <input id="nombre_completo" type="text" class="form-control @error('nombre_completo') is-invalid @enderror" name="nombre_completo" placeholder="Ej. María González" autocomplete="name" maxlength="255" required value="{{ old('nombre_completo') }}" @error('nombre_completo') aria-invalid="true" aria-describedby="nombre-error" @enderror>
                    </div>
                    @error('nombre_completo')<div id="nombre-error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="client-label" for="telefono_principal">Teléfono <span class="client-required" aria-hidden="true">*</span></label>
                    <div class="client-input-wrap">
                        <i class="fas fa-phone client-input-icon" aria-hidden="true"></i>
                        <input id="telefono_principal" type="tel" inputmode="numeric" pattern="[0-9]{8}" minlength="8" maxlength="8" class="form-control @error('telefono_principal') is-invalid @enderror" name="telefono_principal" placeholder="Ej. 70000000" autocomplete="tel-national" required value="{{ old('telefono_principal') }}" aria-describedby="telefono-help{{ $errors->has('telefono_principal') ? ' telefono-error' : '' }}" @error('telefono_principal') aria-invalid="true" @enderror oninput="this.value = this.value.replace(/\D/g, '')">
                    </div>
                    <p id="telefono-help" class="client-help">Ingresa un número de 8 dígitos.</p>
                    @error('telefono_principal')<div id="telefono-error" class="invalid-feedback d-block">El teléfono debe tener exactamente 8 dígitos numéricos.</div>@enderror
                </div>
                <div>
                    <label class="client-label" for="tipo_cliente">Tipo de cliente</label>
                    <select id="tipo_cliente" class="form-select @error('tipo_cliente') is-invalid @enderror" name="tipo_cliente" @error('tipo_cliente') aria-invalid="true" aria-describedby="tipo-error" @enderror>
                        <option value="Regular" @selected(old('tipo_cliente', 'Regular') === 'Regular')>Regular</option>
                        <option value="Corporativo" @selected(old('tipo_cliente') === 'Corporativo')>Corporativo</option>
                    </select>
                    @error('tipo_cliente')<div id="tipo-error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="client-field-wide">
                    <label class="client-label" for="direccion">Dirección <span class="client-optional">(opcional)</span></label>
                    <div class="client-input-wrap">
                        <i class="fas fa-location-dot client-input-icon" aria-hidden="true"></i>
                        <textarea id="direccion" class="form-control @error('direccion') is-invalid @enderror" name="direccion" rows="3" autocomplete="street-address" placeholder="Barrio, calle y número de casa..." @error('direccion') aria-invalid="true" aria-describedby="direccion-error" @enderror>{{ old('direccion') }}</textarea>
                    </div>
                    @error('direccion')<div id="direccion-error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <footer class="client-footer">
                <p class="client-required-note"><span class="client-required" aria-hidden="true">*</span> Campos obligatorios</p>
                <div class="client-actions">
                    <a class="client-button client-cancel" href="{{ route('clientes.index') }}">Cancelar</a>
                    <button class="client-button client-save" type="submit"><i class="fas fa-save" aria-hidden="true"></i> Registrar cliente</button>
                </div>
            </footer>
        </form>
    </section>
</div>
@endsection
