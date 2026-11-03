@extends('layouts.app')

@section('title', 'Pedido ' . $pedido->codigo_pedido)

@section('styles')
<style>
    .pedido-detail { --wine: #8f0929; --gold: #a47c2b; max-width: 1000px; margin: 6px auto 20px; color: #3d2c28; }
    .pedido-detail__card { overflow: hidden; border: 1px solid #e8d8c9; border-radius: 23px; background: #fff; box-shadow: 0 13px 30px rgba(93, 45, 24, .14); }
    .pedido-detail__header { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin: 0 27px; padding: 25px 10px 22px; border-bottom: 1px solid #ddc6ad; }
    .pedido-detail__title { display: flex; align-items: center; gap: 16px; margin: 0; color: #790d23; font-size: clamp(1.4rem, 2.5vw, 2rem); font-weight: 800; }
    .pedido-detail__title i { color: var(--wine); font-size: 1.9rem; }
    .pedido-detail__close { display: grid; place-items: center; width: 39px; height: 39px; flex-shrink: 0; border-radius: 8px; color: var(--wine); font-size: 1.6rem; text-decoration: none; }
    .pedido-detail__close:hover { color: #70071e; background: #fff0e8; }
    .pedido-detail__body { padding: 23px 36px 27px; }
    .pedido-detail__identity { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin: 0 7px 20px; }
    .pedido-detail__number { margin: 0; color: #790d23; font-size: clamp(1.8rem, 3vw, 2.65rem); font-weight: 800; }
    .pedido-detail__status { display: inline-block; padding: 8px 20px; border-radius: 999px; font-size: 1rem; font-weight: 750; white-space: nowrap; }
    .pedido-detail__status--pendiente { background: #fff1c6; color: #8b620d; }
    .pedido-detail__status--proceso { background: #e3edff; color: #3458a1; }
    .pedido-detail__status--completado { background: #e0f3d3; color: #176125; }
    .pedido-detail__status--cancelado { background: #fee2e2; color: #b52c37; }
    .pedido-detail__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 17px; }
    .pedido-detail__field { display: flex; align-items: center; gap: 23px; min-height: 94px; padding: 17px 23px; border-radius: 17px; background: linear-gradient(120deg, #faf5ef, #f4efe9); }
    .pedido-detail__field > i { flex: 0 0 38px; color: var(--gold); font-size: 2rem; text-align: center; }
    .pedido-detail__field > div { min-width: 0; }
    .pedido-detail__label { display: block; color: var(--wine); font-size: .96rem; font-weight: 650; }
    .pedido-detail__value { display: block; margin-top: 4px; color: #3d1018; font-size: clamp(1.03rem, 1.8vw, 1.35rem); font-weight: 750; line-height: 1.3; overflow-wrap: anywhere; }
    .pedido-detail__value--total { color: #8f0929; font-size: clamp(1.4rem, 2.4vw, 1.95rem); }
    .pedido-detail__notice { display: flex; align-items: flex-start; gap: 20px; margin-top: 25px; padding: 21px 25px; border: 1px solid #efd4a7; border-radius: 14px; background: #fff8ed; }
    .pedido-detail__notice > i { flex-shrink: 0; padding-right: 21px; border-right: 1px solid #dfc49d; color: var(--gold); font-size: 1.8rem; }
    .pedido-detail__notice h3 { margin: 0 0 6px; color: #790d23; font-size: 1.2rem; font-weight: 750; }
    .pedido-detail__notice p { margin: 3px 0 0; font-size: .97rem; line-height: 1.5; }
    .pedido-detail__notice ul { margin: 9px 0 0; padding-left: 20px; font-size: .88rem; }
    .pedido-detail__observations { margin-top: 20px; padding: 18px 21px; border: 1px solid #eadfce; border-radius: 12px; background: #fcfaf6; }
    .pedido-detail__observations h3 { margin: 0 0 7px; color: var(--wine); font-size: 1rem; font-weight: 700; }
    .pedido-detail__observations p { margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; }
    .pedido-detail__flow { display: flex; justify-content: flex-end; flex-wrap: wrap; gap: 12px; margin-top: 23px; }
    .pedido-detail__actions { display: flex; justify-content: space-between; align-items: center; gap: 18px; margin: 0 36px; padding: 23px 0 28px; border-top: 1px solid #dfc9b2; }
    .pedido-detail__actions-left, .pedido-detail__actions-right { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; }
    .pedido-detail__actions-right { margin-left: auto; }
    .pedido-detail__button { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 47px; padding: 10px 22px; border: 1px solid #8b7a6e; border-radius: 12px; color: #5a504a; background: #fff; font-size: .98rem; font-weight: 650; text-decoration: none; }
    .pedido-detail__button:hover { color: #4e3528; background: #fff5e9; }
    .pedido-detail__button--primary { border-color: var(--wine); color: #fff; background: var(--wine); }
    .pedido-detail__button--primary:hover { color: #fff; background: #720720; }
    .pedido-detail__button--danger { border-color: #d3263a; color: #c31d30; }
    .pedido-detail__button--danger:hover { color: #ad1526; background: #fff0ef; }
    .pedido-detail a:focus-visible, .pedido-detail button:focus-visible { outline: 3px solid #d4a441; outline-offset: 3px; }
    @media (max-width: 767px) { .pedido-detail__body { padding: 20px; } .pedido-detail__header { margin: 0 20px; padding-left: 0; padding-right: 0; } .pedido-detail__field { gap: 14px; padding: 16px; } .pedido-detail__field > i { flex-basis: 28px; font-size: 1.5rem; } .pedido-detail__actions { flex-wrap: wrap; margin: 0 20px; } }
    @media (max-width: 575px) { .pedido-detail__grid { grid-template-columns: minmax(0, 1fr); } .pedido-detail__identity { flex-wrap: wrap; margin-left: 0; } .pedido-detail__status { padding: 6px 15px; font-size: .9rem; } .pedido-detail__notice { gap: 13px; padding: 17px 15px; } .pedido-detail__notice > i { padding-right: 13px; font-size: 1.35rem; } .pedido-detail__actions-left, .pedido-detail__actions-right, .pedido-detail__actions form { width: 100%; } .pedido-detail__button { width: 100%; } .pedido-detail__flow form { width: 100%; } }
</style>
@endsection

@section('content')
@php
    $productosPedido = $pedido->detalles->map(fn ($detalle) => $detalle->producto?->nombre ?? 'Producto no disponible')->implode(', ');
    $cantidadTotal = $pedido->detalles->sum('cantidad');
    $claseEstado = match ($pedido->estado) { 'Pendiente' => 'pendiente', 'En proceso' => 'proceso', 'Completado' => 'completado', default => 'cancelado' };
    $puedeEliminar = in_array($pedido->estado, ['Pendiente', 'Cancelado'], true) && !$pedido->produccion_iniciada_at && !$pedido->movimientos_insumo_exists;
@endphp
<div class="pedido-detail">
    <article class="pedido-detail__card">
        <header class="pedido-detail__header">
            <h1 class="pedido-detail__title"><i class="fas fa-clipboard-list" aria-hidden="true"></i> Detalle del pedido</h1>
            <a href="{{ route('pedidos.index') }}" class="pedido-detail__close" aria-label="Cerrar detalle y volver al listado"><i class="fas fa-times" aria-hidden="true"></i></a>
        </header>
        <div class="pedido-detail__body">
            <div class="pedido-detail__identity"><h2 class="pedido-detail__number">{{ $pedido->codigo_pedido }}</h2><span class="pedido-detail__status pedido-detail__status--{{ $claseEstado }}">{{ $pedido->estado }}</span></div>
            <div class="pedido-detail__grid">
                <div class="pedido-detail__field"><i class="far fa-user" aria-hidden="true"></i><div><span class="pedido-detail__label">Cliente</span><strong class="pedido-detail__value">{{ $pedido->cliente->nombre_completo }}</strong></div></div>
                <div class="pedido-detail__field"><i class="far fa-calendar" aria-hidden="true"></i><div><span class="pedido-detail__label">Fecha de registro</span><strong class="pedido-detail__value">{{ $pedido->fecha_pedido->format('d/m/Y') }}</strong></div></div>
                <div class="pedido-detail__field"><i class="fas fa-cake-candles" aria-hidden="true"></i><div><span class="pedido-detail__label">{{ $pedido->detalles->count() === 1 ? 'Producto' : 'Productos' }}</span><strong class="pedido-detail__value">{{ $productosPedido ?: 'Sin productos' }}</strong></div></div>
                <div class="pedido-detail__field"><i class="fas fa-layer-group" aria-hidden="true"></i><div><span class="pedido-detail__label">Cantidad total</span><strong class="pedido-detail__value">{{ $cantidadTotal }} {{ $cantidadTotal == 1 ? 'unidad' : 'unidades' }}</strong></div></div>
                <div class="pedido-detail__field"><i class="far fa-calendar-check" aria-hidden="true"></i><div><span class="pedido-detail__label">Fecha de entrega</span><strong class="pedido-detail__value">{{ $pedido->fecha_entrega->format('d/m/Y') }}</strong></div></div>
                <div class="pedido-detail__field"><i class="fas fa-coins" aria-hidden="true"></i><div><span class="pedido-detail__label">Total del pedido</span><strong class="pedido-detail__value pedido-detail__value--total">Bs {{ number_format($pedido->total, 2, ',', '.') }}</strong></div></div>
            </div>

            @if($pedido->insumos_reservados_at && $pedido->estado === 'Pendiente')
                <section class="pedido-detail__notice" aria-label="Reserva de insumos">
                    <i class="fas fa-circle-info" aria-hidden="true"></i><div><h3>Insumos apartados</h3><p>El stock se descontará cuando inicie la preparación.</p>
                        @if($pedido->reservas->isNotEmpty())<ul>@foreach($pedido->reservas as $reserva)<li>{{ $reserva->insumo->nombre }}: {{ number_format($reserva->cantidad, 2, ',', '.') }} {{ $reserva->insumo->unidad }}</li>@endforeach</ul>@endif
                    </div>
                </section>
            @elseif($pedido->produccion_iniciada_at)
                <section class="pedido-detail__notice" aria-label="Estado de preparación"><i class="fas fa-circle-info" aria-hidden="true"></i><div><h3>Preparación registrada</h3><p>Iniciada el {{ $pedido->produccion_iniciada_at->format('d/m/Y') }} a las {{ $pedido->produccion_iniciada_at->format('H:i') }}.</p><p>Los insumos de este pedido ya se consumieron.</p></div></section>
            @elseif(!$pedido->insumos_reservados_at && $pedido->movimientos_insumo_exists)
                <section class="pedido-detail__notice" aria-label="Pedido anterior"><i class="fas fa-circle-info" aria-hidden="true"></i><div><h3>Pedido anterior</h3><p>Los insumos se descontaron cuando se registró.</p></div></section>
            @elseif($pedido->estado === 'Cancelado' && $pedido->insumos_reservados_at)
                <section class="pedido-detail__notice" aria-label="Pedido cancelado"><i class="fas fa-circle-info" aria-hidden="true"></i><div><h3>Pedido cancelado</h3><p>La reserva de insumos se liberó sin iniciar la preparación.</p></div></section>
            @endif

            @if($pedido->descripcion_especificaciones)
                <section class="pedido-detail__observations"><h3><i class="far fa-pen-to-square" aria-hidden="true"></i> Observaciones</h3><p>{{ $pedido->descripcion_especificaciones }}</p></section>
            @endif
            @if(in_array($pedido->estado, ['Pendiente', 'En proceso'], true))
                <div class="pedido-detail__flow">
                    <form method="POST" action="{{ route('pedidos.cambiarEstado', $pedido) }}" @if($pedido->estado === 'Pendiente') onsubmit="return confirm('¿Iniciar la preparación y consumir los insumos reservados?')" @endif>
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="{{ $pedido->estado === 'Pendiente' ? 'En proceso' : 'Completado' }}">
                        <button type="submit" class="pedido-detail__button pedido-detail__button--primary">{{ $pedido->estado === 'Pendiente' ? 'Iniciar preparación' : 'Marcar terminado' }}</button>
                    </form>
                </div>
            @endif
        </div>
        <footer class="pedido-detail__actions">
            <div class="pedido-detail__actions-left">
                @if($pedido->estado !== 'Cancelado')
                    <form method="POST" action="{{ route('pedidos.cambiarEstado', $pedido) }}" onsubmit="return confirm('¿Cancelar este pedido? Los insumos ya consumidos no se repondrán automáticamente.')">
                        @csrf @method('PATCH')<input type="hidden" name="estado" value="Cancelado">
                        <button type="submit" class="pedido-detail__button pedido-detail__button--danger">Cancelar pedido</button>
                    </form>
                @endif
                @if($puedeEliminar)
                    <form method="POST" action="{{ route('pedidos.destroy', $pedido) }}" onsubmit="return confirm('¿Eliminar este pedido? Esta acción no se puede deshacer.')">
                        @csrf @method('DELETE')<button type="submit" class="pedido-detail__button pedido-detail__button--danger"><i class="far fa-trash-can" aria-hidden="true"></i> Eliminar</button>
                    </form>
                @endif
            </div>
            <div class="pedido-detail__actions-right">
                <a href="{{ route('pedidos.index') }}" class="pedido-detail__button">Cerrar</a>
                <a href="{{ route('pedidos.edit', $pedido) }}" class="pedido-detail__button pedido-detail__button--primary"><i class="fas fa-pen" aria-hidden="true"></i> Editar pedido</a>
            </div>
        </footer>
    </article>
</div>
@endsection
