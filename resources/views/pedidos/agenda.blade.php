@extends('layouts.app')

@section('title', 'Agenda de preparación - Delicias Dulces')

@section('styles')
<style>
    .agenda { --wine: #8f0929; --gold: #ad813b; max-width: 1280px; margin: 0 auto; color: #383031; }
    .agenda-header { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 26px; }
    .agenda-eyebrow { color: #79644f; font-size: .85rem; margin-bottom: 8px; }
    .agenda h1 { color: var(--wine); font-size: clamp(1.7rem, 3vw, 2.4rem); font-weight: 800; margin-bottom: 8px; }
    .agenda-header p { margin: 0; color: #70635c; }
    .agenda-button { display: inline-flex; justify-content: center; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #d7b98d; border-radius: 10px; color: var(--wine); background: #fffdf9; text-decoration: none; font-weight: 650; }
    .agenda-button:hover { background: #f9ecd9; color: var(--wine); }
    .agenda-button--primary { background: var(--wine); border-color: var(--wine); color: white; }
    .agenda-button--primary:hover { background: #720720; color: white; }
    .agenda-button:disabled { opacity: .6; cursor: not-allowed; }
    .agenda-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
    .agenda-summary a { padding: 17px 20px; border: 1px solid #e5d7c7; border-radius: 14px; text-decoration: none; color: #594a42; background: #fffdf9; }
    .agenda-summary a[aria-current="page"] { border-color: var(--wine); box-shadow: 0 0 0 1px var(--wine); }
    .agenda-summary strong { display: block; font-size: 1.9rem; color: var(--wine); line-height: 1.2; margin-bottom: 5px; }
    .agenda-summary span { display: flex; align-items: center; gap: 7px; font-size: .9rem; }
    .agenda-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid #e5d7c7; }
    .agenda-toolbar p { margin: 0; color: #70635c; font-size: .9rem; }
    .agenda-group { margin-top: 26px; }
    .agenda-group h2 { font-size: 1.2rem; font-weight: 750; margin: 0 0 14px; color: var(--wine); display: flex; align-items: center; gap: 9px; }
    .agenda-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .agenda-card { background: white; border: 1px solid #e6dacd; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 16px rgba(95, 47, 22, .05); }
    .agenda-card header { display: flex; justify-content: space-between; gap: 12px; align-items: center; padding: 16px 20px; background: #fbf5eb; border-bottom: 1px solid #ece1d5; }
    .agenda-card h3 { font-size: 1rem; margin: 0; font-weight: 750; }
    .agenda-card h3 a { color: var(--wine); text-decoration: none; }
    .agenda-badge { padding: 4px 10px; border-radius: 20px; background: #fff0c5; color: #805b0c; font-size: .75rem; font-weight: 700; }
    .agenda-badge--atrasados { background: #fee2e2; color: #a72637; }
    .agenda-badge--en_proceso { background: #e3edff; color: #3458a1; }
    .agenda-card-body { padding: 20px; }
    .agenda-client { font-size: 1.08rem; font-weight: 700; margin-bottom: 16px; }
    .agenda-dates { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px; }
    .agenda-dates dt { color: #796b63; font-size: .8rem; font-weight: 500; margin-bottom: 3px; }
    .agenda-dates dd { margin: 0; font-size: .95rem; font-weight: 650; }
    .agenda-products { padding: 12px 0; margin-bottom: 14px; border-block: 1px solid #f0e7dd; list-style: none; }
    .agenda-products li { display: flex; justify-content: space-between; gap: 12px; padding: 4px 0; font-size: .9rem; }
    .agenda-stock { margin: 0; font-size: .85rem; color: #327044; }
    .agenda-warning { padding: 12px; border-radius: 10px; background: #fff4d8; color: #74500b; font-size: .85rem; }
    .agenda-warning ul { padding-left: 18px; margin: 7px 0; }
    .agenda-card footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 14px 20px; border-top: 1px solid #f0e7dd; }
    .agenda-card footer form { margin: 0; }
    .agenda-empty { text-align: center; padding: 55px 20px; margin-top: 20px; background: #fffdf9; border: 1px dashed #d8c4a9; border-radius: 16px; }
    .agenda-empty > i { font-size: 2.4rem; color: var(--gold); margin-bottom: 14px; }
    .agenda-pagination { margin-top: 25px; }
    .agenda a:focus-visible, .agenda button:focus-visible { outline: 3px solid #d5a751; outline-offset: 3px; }
    @media (max-width: 900px) { .agenda-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); } .agenda-cards { grid-template-columns: 1fr; } }
    @media (max-width: 575px) { .agenda-header { align-items: flex-start; flex-direction: column; } .agenda-summary a { padding: 14px; } .agenda-card footer > *, .agenda-card footer .agenda-button { width: 100%; } }
</style>
@endsection

@section('content')
@php
    $secciones = [
        'atrasados' => ['Atrasados', 'fa-clock'],
        'hoy' => ['Para hoy', 'fa-calendar-day'],
        'proximos' => ['Próximos días', 'fa-calendar-days'],
        'en_proceso' => ['En preparación', 'fa-kitchen-set'],
    ];
@endphp
<div class="agenda">
    <header class="agenda-header">
        <div>
            <div class="agenda-eyebrow">Pedidos / Organización de la producción</div>
            <h1>Agenda de preparación</h1>
            <p>Hoy, {{ $hoy->format('d/m/Y') }}. Consulta qué preparar y revisa sus insumos.</p>
        </div>
        <a href="{{ route('pedidos.create') }}" class="agenda-button agenda-button--primary"><i class="fas fa-plus" aria-hidden="true"></i> Nuevo pedido</a>
    </header>

    <nav class="agenda-summary" aria-label="Filtrar agenda por preparación">
        @foreach($secciones as $clave => [$titulo, $icono])
            <a href="{{ route('pedidos.agenda', ['periodo' => $clave]) }}" @if($periodo === $clave) aria-current="page" @endif>
                <strong>{{ $resumen[$clave] }}</strong><span><i class="fas {{ $icono }}" aria-hidden="true"></i> {{ $titulo }}</span>
            </a>
        @endforeach
    </nav>
    <div class="agenda-toolbar">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pedidos.agenda') }}" class="agenda-button {{ $periodo === 'todos' ? 'agenda-button--primary' : '' }}" @if($periodo === 'todos') aria-current="page" @endif>Ver todos</a>
            <a href="{{ route('pedidos.index') }}" class="agenda-button">Listado de pedidos</a>
        </div>
        <p>{{ $pedidos->total() }} pedidos en esta vista · El consumo se registra al iniciar la preparación.</p>
    </div>

    @foreach($secciones as $clave => [$titulo, $icono])
        @if($grupos->has($clave))
            <section class="agenda-group" aria-labelledby="grupo-{{ $clave }}">
                <h2 id="grupo-{{ $clave }}"><i class="fas {{ $icono }}" aria-hidden="true"></i> {{ $titulo }}</h2>
                <div class="agenda-cards">
                    @foreach($grupos[$clave] as $pedido)
                        @php($faltantes = $faltantesPorPedido[$pedido->id])
                        <article class="agenda-card">
                            <header>
                                <h3><a href="{{ route('pedidos.show', $pedido) }}">{{ $pedido->codigo_pedido }}</a></h3>
                                <span class="agenda-badge agenda-badge--{{ $clave }}">{{ $clave === 'atrasados' ? 'Preparación atrasada' : $pedido->estado }}</span>
                            </header>
                            <div class="agenda-card-body">
                                <p class="agenda-client">{{ $pedido->cliente?->nombre_completo ?? 'Cliente no disponible' }}</p>
                                <dl class="agenda-dates">
                                    <div><dt>Preparar</dt><dd>{{ ($pedido->fecha_preparacion ?? $pedido->fecha_entrega)->format('d/m/Y') }}</dd></div>
                                    <div><dt>Entregar</dt><dd>{{ $pedido->fecha_entrega->format('d/m/Y') }}</dd></div>
                                </dl>
                                @if(!$pedido->fecha_preparacion)<p class="small text-muted">Pedido anterior: se usa la fecha de entrega para organizar la preparación.</p>@endif
                                <ul class="agenda-products" aria-label="Productos y cantidades">
                                    @forelse($pedido->detalles as $detalle)
                                        <li><span>{{ $detalle->producto?->nombre ?? 'Producto no disponible' }}</span><strong>× {{ $detalle->cantidad }}</strong></li>
                                    @empty
                                        <li>Sin productos registrados</li>
                                    @endforelse
                                </ul>
                                @if($faltantes)
                                    <div class="agenda-warning" role="status">
                                        <strong><i class="fas fa-triangle-exclamation" aria-hidden="true"></i> Reponer insumos</strong>
                                        <ul>@foreach($faltantes as $faltante)<li>{{ $faltante }}</li>@endforeach</ul>
                                        Repón los insumos con ayuda del administrador o revisa la fecha de preparación.
                                    </div>
                                @elseif($pedido->estado === 'En proceso')
                                    <p class="agenda-stock"><i class="fas fa-kitchen-set" aria-hidden="true"></i> Preparación iniciada. Consulta el detalle para continuar.</p>
                                @elseif($pedido->insumos_reservados_at)
                                    <p class="agenda-stock"><i class="fas fa-check-circle" aria-hidden="true"></i> Reserva registrada para la preparación.</p>
                                @else
                                    <p class="small text-muted mb-0">Pedido anterior: revisa el detalle y el registro de insumos antes de continuar.</p>
                                @endif
                            </div>
                            <footer>
                                <a href="{{ route('pedidos.show', $pedido) }}" class="agenda-button">Ver detalle <span class="visually-hidden">de {{ $pedido->codigo_pedido }}</span></a>
                                @if($pedido->estado === 'Pendiente')
                                    <form method="POST" action="{{ route('pedidos.cambiarEstado', $pedido) }}" onsubmit="return confirm('¿Iniciar la preparación del pedido {{ $pedido->codigo_pedido }} y consumir los insumos reservados?')">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="estado" value="En proceso">
                                        <button type="submit" class="agenda-button agenda-button--primary" @disabled(count($faltantes) > 0) aria-label="Iniciar preparación de {{ $pedido->codigo_pedido }}">Iniciar preparación</button>
                                    </form>
                                @endif
                            </footer>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if($pedidos->isEmpty())
        <div class="agenda-empty"><i class="far fa-calendar-check" aria-hidden="true"></i><h2 class="h5">No hay pedidos para esta vista.</h2><p class="text-muted">Los pedidos aparecerán aquí según su fecha de preparación. Los completados y cancelados quedan en el listado.</p><a href="{{ route('pedidos.index') }}" class="agenda-button">Ir a pedidos</a></div>
    @endif
    <div class="agenda-pagination">@include('components.pagination', ['paginator' => $pedidos])</div>
</div>
@endsection
