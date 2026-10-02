<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduccionPedidoTest extends TestCase
{
    use RefreshDatabase;

    private function datos(): array
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        $cliente = Cliente::create(['nombre_completo' => 'Ana', 'telefono_principal' => '123456', 'direccion' => 'Casa']);
        $categoria = Categoria::create(['nombre' => 'Tortas', 'slug' => 'tortas']);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Torta', 'precio_venta' => 30, 'costo_produccion' => 10]);
        $insumo = Insumo::create(['nombre' => 'Harina', 'unidad' => 'Kg', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 10]);
        MovimientoInsumo::registrar($insumo, 'Entrada', 10, 'Compra', codigoLote: 'H1', fechaVencimiento: today()->addDays(3)->toDateString());
        $producto->insumos()->attach($insumo->id, ['cantidad_necesaria' => 3]);

        return [$cliente, $producto, $insumo];
    }

    private function crearPedido(Cliente $cliente, Producto $producto): Pedido
    {
        $this->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'Predefinido',
            'prioridad' => 'Normal',
            'fecha_entrega' => today()->addDay()->toDateString(),
            'metodo_pago' => 'Efectivo',
            'productos' => [$producto->id => ['cantidad' => 1]],
        ])->assertRedirect();

        return Pedido::latest('id')->firstOrFail();
    }

    private function estado(Pedido $pedido, string $estado): void
    {
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => $estado])->assertRedirect();
    }

    public function test_reserva_sin_consumir_y_consume_al_iniciar(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $pedido = $this->crearPedido($cliente, $producto);
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
        $this->assertEquals(7, $insumo->stockLibre());
        $this->assertEquals(3, $pedido->reservas()->sum('cantidad'));
        $this->assertEquals(0, MovimientoInsumo::where('pedido_id', $pedido->id)->count());
        $this->get(route('pedidos.show', $pedido))->assertOk()->assertSee('Iniciar preparación');
        $this->get(route('pedidos.index'))->assertOk();

        $this->estado($pedido, 'En proceso');
        $this->assertEquals(7, $insumo->fresh()->stock_actual);
        $this->assertEquals(7, $insumo->stockLibre());
        $this->assertEquals(0, $pedido->reservas()->count());
        $this->assertEquals(1, MovimientoInsumo::where('pedido_id', $pedido->id)->where('tipo', 'Salida')->count());
        $this->assertNotNull($pedido->fresh()->produccion_iniciada_at);
        $this->get(route('pedidos.show', $pedido))->assertOk()->assertSee('Marcar terminado');
        $this->estado($pedido, 'Completado');
        $this->assertEquals(7, $insumo->fresh()->stock_actual);
    }

    public function test_cancelar_pendiente_libera_reserva_y_cancelar_producido_no_reintegra(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $primero = $this->crearPedido($cliente, $producto);
        $this->estado($primero, 'Cancelado');
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
        $this->assertEquals(10, $insumo->stockLibre());

        $segundo = $this->crearPedido($cliente, $producto);
        $this->estado($segundo, 'En proceso');
        $this->estado($segundo, 'Cancelado');
        $this->assertEquals(7, $insumo->fresh()->stock_actual);
        $this->assertEquals(0, $segundo->reservas()->count());
        $this->assertEquals(1, MovimientoInsumo::where('pedido_id', $segundo->id)->where('tipo', 'Salida')->count());
    }

    public function test_movimiento_manual_no_puede_gastar_reserva(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $this->crearPedido($cliente, $producto);
        $response = $this->post(route('insumos.movimientos.store', $insumo), ['tipo' => 'Salida', 'cantidad' => 8, 'motivo' => 'Uso manual']);
        $response->assertSessionHasErrors('cantidad');
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
        $this->assertEquals(10, $insumo->lotes()->sum('cantidad'));
    }

    public function test_vencimiento_posterior_bloquea_produccion_sin_perder_reserva(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $pedido = $this->crearPedido($cliente, $producto);
        $insumo->lotes()->first()->update(['fecha_vencimiento' => today()->subDay()]);
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'En proceso'])->assertSessionHasErrors('estado');
        $this->assertEquals('Pendiente', $pedido->fresh()->estado);
        $this->assertEquals(3, $pedido->reservas()->sum('cantidad'));
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
    }

    public function test_pedido_anterior_no_se_descontara_dos_veces(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $pedido = $this->crearPedido($cliente, $producto);
        $pedido->reservas()->delete();
        $pedido->update(['insumos_reservados_at' => null]);
        MovimientoInsumo::registrar($insumo, 'Salida', 3, 'Consumo anterior', pedidoId: $pedido->id);
        $this->estado($pedido, 'En proceso');
        $this->estado($pedido, 'Cancelado');
        $this->assertEquals(7, $insumo->fresh()->stock_actual);
        $this->assertEquals(1, MovimientoInsumo::where('pedido_id', $pedido->id)->where('tipo', 'Salida')->count());
    }

    public function test_no_acepta_dos_reservas_que_superen_el_stock(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $this->crearPedido($cliente, $producto);
        $this->crearPedido($cliente, $producto);
        $this->crearPedido($cliente, $producto);
        $this->post(route('pedidos.store'), [
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'Predefinido',
            'prioridad' => 'Normal',
            'fecha_entrega' => today()->addDay()->toDateString(),
            'metodo_pago' => 'Efectivo',
            'productos' => [$producto->id => ['cantidad' => 1]],
        ])->assertSessionHasErrors('productos');
        $this->assertEquals(3, Pedido::count());
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
        $this->assertEquals(1, $insumo->stockLibre());
    }

    public function test_pedido_con_consumo_no_se_puede_eliminar_y_pendiente_si(): void
    {
        [$cliente, $producto, $insumo] = $this->datos();
        $primero = $this->crearPedido($cliente, $producto);
        $this->delete(route('pedidos.destroy', $primero))->assertRedirect();
        $this->assertEquals(0, Pedido::count());
        $this->assertEquals(10, $insumo->stockLibre());

        $segundo = $this->crearPedido($cliente, $producto);
        $this->estado($segundo, 'En proceso');
        $this->estado($segundo, 'Cancelado');
        $this->delete(route('pedidos.destroy', $segundo))->assertSessionHas('error');
        $this->assertNotNull(Pedido::find($segundo->id));
    }

    public function test_no_se_puede_completar_antes_de_iniciar_preparacion(): void
    {
        [$cliente, $producto] = $this->datos();
        $pedido = $this->crearPedido($cliente, $producto);
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'Completado'])->assertSessionHasErrors('estado');
        $this->assertEquals('Pendiente', $pedido->fresh()->estado);
    }
}
