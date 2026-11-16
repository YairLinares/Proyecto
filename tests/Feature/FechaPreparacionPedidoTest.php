<?php

namespace Tests\Feature;

use App\Models\{Categoria, Cliente, Insumo, MovimientoInsumo, Pedido, Producto, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FechaPreparacionPedidoTest extends TestCase
{
    use RefreshDatabase;

    private function escenario(): array
    {
        $this->travelTo(now()->setDate(2026, 11, 9)->startOfDay());
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $cliente = Cliente::create(['nombre_completo' => 'Ana', 'telefono_principal' => '70000000', 'direccion' => 'Casa']);
        $categoria = Categoria::create(['nombre' => 'Zanahoria', 'slug' => 'zanahoria']);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Queque de zanahoria', 'precio_venta' => 50, 'costo_produccion' => 20]);
        $insumo = Insumo::create(['nombre' => 'Leche', 'unidad' => 'Litros', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 8]);
        MovimientoInsumo::registrar($insumo, 'Entrada', 10, 'Compra', codigoLote: 'L1', fechaVencimiento: '2026-11-12');
        $producto->insumos()->attach($insumo->id, ['cantidad_necesaria' => 3]);

        return [$insumo, [
            'cliente_id' => $cliente->id, 'tipo_pedido' => 'Predefinido', 'prioridad' => 'Normal',
            'fecha_preparacion' => '2026-11-14', 'fecha_entrega' => '2026-11-15',
            'metodo_pago' => 'Efectivo', 'productos' => [$producto->id => ['cantidad' => 1]],
        ]];
    }

    public function test_no_reserva_insumos_que_vencen_antes_de_preparar(): void
    {
        [$insumo, $datos] = $this->escenario();
        $this->post(route('pedidos.store'), $datos)->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('pedidos', 0);
        $this->assertDatabaseCount('reservas_insumo', 0);
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
    }

    public function test_reserva_solo_si_alcanza_el_stock_vigente_en_la_fecha_y_no_lo_duplica(): void
    {
        [$insumo, $datos] = $this->escenario();
        MovimientoInsumo::registrar($insumo, 'Entrada', 4, 'Compra', codigoLote: 'L2', fechaVencimiento: '2026-11-14');
        $this->post(route('pedidos.store'), $datos)->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        $this->assertSame('2026-11-14', $pedido->fecha_preparacion->toDateString());
        $this->assertEquals(14, $insumo->fresh()->stock_actual);
        $this->assertEquals(3, $pedido->reservas()->sum('cantidad'));
        $this->post(route('pedidos.store'), $datos)->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('pedidos', 1);
    }

    public function test_reprogramar_a_una_fecha_sin_insumos_vigentes_conserva_reserva_y_fecha(): void
    {
        [$insumo, $datos] = $this->escenario();
        $datos['fecha_preparacion'] = '2026-11-11';
        $this->post(route('pedidos.store'), $datos)->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        unset($datos['productos']);
        $datos += ['direccion_entrega' => 'Casa', 'telefono_contacto' => '70000000', 'estado' => 'Pendiente'];
        $datos['fecha_preparacion'] = '2026-11-14';
        $this->put(route('pedidos.update', $pedido), $datos)->assertSessionHasErrors('fecha_preparacion');
        $this->assertSame('2026-11-11', $pedido->fresh()->fecha_preparacion->toDateString());
        $this->assertEquals(3, $pedido->reservas()->sum('cantidad'));
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
    }

    public function test_fecha_de_preparacion_debe_estar_entre_hoy_y_la_entrega(): void
    {
        [, $datos] = $this->escenario();
        foreach (['2026-11-08', '2026-11-16', 'no-es-fecha'] as $fecha) {
            $this->post(route('pedidos.store'), array_replace($datos, ['fecha_preparacion' => $fecha]))
                ->assertSessionHasErrors('fecha_preparacion');
        }
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_empleado_ve_advertencia_si_un_lote_reservado_deja_de_servir(): void
    {
        [$insumo, $datos] = $this->escenario();
        $datos['fecha_preparacion'] = '2026-11-11';
        $this->post(route('pedidos.store'), $datos)->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        $insumo->lotes()->first()->update(['fecha_vencimiento' => '2026-11-10']);
        $this->get(route('pedidos.show', $pedido))->assertOk()
            ->assertSee('Insumos insuficientes para la preparación')->assertSee('Leche');
        $this->get(route('profile.show'))->assertOk()
            ->assertSee('Revisar insumos del pedido '.$pedido->codigo_pedido)->assertSee('Leche');
    }

    public function test_reprogramar_conserva_la_reserva_propia_y_permite_un_lote_sin_vencimiento(): void
    {
        [$insumo, $datos] = $this->escenario();
        MovimientoInsumo::registrar($insumo, 'Entrada', 3, 'Compra', codigoLote: 'L2');
        $this->post(route('pedidos.store'), $datos)->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        unset($datos['productos']);
        $datos += ['direccion_entrega' => 'Casa', 'telefono_contacto' => '70000000', 'estado' => 'Pendiente'];
        $datos['fecha_preparacion'] = '2026-11-15';
        $this->put(route('pedidos.update', $pedido), $datos)->assertSessionHasNoErrors();
        $this->assertSame('2026-11-15', $pedido->fresh()->fecha_preparacion->toDateString());
        $this->assertEquals(3, $pedido->reservas()->sum('cantidad'));
        $this->get(route('pedidos.show', $pedido))->assertOk()->assertDontSee('Insumos insuficientes para la preparación');
    }

    public function test_formulario_anterior_sin_fecha_usa_la_entrega_y_comprueba_vencimientos(): void
    {
        [$insumo, $datos] = $this->escenario();
        unset($datos['fecha_preparacion']);
        $this->post(route('pedidos.store'), $datos)->assertSessionHasErrors('productos');
        MovimientoInsumo::registrar($insumo, 'Entrada', 3, 'Compra', codigoLote: 'L2', fechaVencimiento: '2026-11-15');
        $this->post(route('pedidos.store'), $datos)->assertSessionHasNoErrors();
        $this->assertSame('2026-11-15', Pedido::firstOrFail()->fecha_preparacion->toDateString());
    }
}
