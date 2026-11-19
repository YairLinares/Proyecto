<?php

namespace Tests\Feature;

use App\Models\{Categoria, Cliente, Insumo, MovimientoInsumo, Pedido, Producto, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaPreparacionTest extends TestCase
{
    use RefreshDatabase;

    private function pedido(string $codigo, string $fecha, string $estado = 'Pendiente', bool $sinFecha = false): Pedido
    {
        $cliente = Cliente::firstOrCreate(['nombre_completo' => 'Ana Flores'], ['telefono_principal' => '70000000', 'direccion' => 'Casa']);

        return Pedido::create([
            'numero_pedido' => $codigo, 'cliente_id' => $cliente->id, 'tipo_pedido' => 'Predefinido',
            'fecha_pedido' => today(), 'fecha_preparacion' => $sinFecha ? null : $fecha,
            'fecha_entrega' => $sinFecha ? $fecha : \Illuminate\Support\Carbon::parse($fecha)->addDay(),
            'direccion_entrega' => 'Casa', 'telefono_contacto' => '70000000',
            'metodo_pago' => 'Efectivo', 'estado' => $estado,
        ]);
    }

    public function test_agenda_agrupa_por_preparacion_y_excluye_pedidos_cerrados_sin_modificar_datos(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 16)->startOfDay());
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $this->pedido('PED-ATRASADO', '2026-11-15');
        $this->pedido('PED-HOY', '2026-11-16');
        $this->pedido('PED-FUTURO', '2026-11-23');
        $this->pedido('PED-ANTERIOR', '2026-11-16', sinFecha: true);
        $this->pedido('PED-PROCESO', '2026-11-15', 'En proceso');
        $this->pedido('PED-CERRADO', '2026-11-16', 'Completado');
        $this->pedido('PED-CANCELADO', '2026-11-16', 'Cancelado');

        $this->get('/pedidos/agenda')->assertOk()->assertSee('Agenda de preparación')
            ->assertViewHas('resumen', ['atrasados' => 1, 'hoy' => 2, 'proximos' => 1, 'en_proceso' => 1])
            ->assertSee('PED-ATRASADO')->assertSee('PED-HOY')->assertSee('PED-FUTURO')
            ->assertSee('PED-ANTERIOR')->assertSee('PED-PROCESO')->assertSee('Ana Flores')
            ->assertViewHas('pedidos', fn ($pedidos) => $pedidos->total() === 5);
        $this->assertDatabaseCount('pedidos', 7);
        $this->assertDatabaseCount('movimientos_insumo', 0);
    }

    public function test_filtros_y_paginacion_conservan_el_periodo(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        for ($i = 1; $i <= 13; $i++) {
            $this->pedido('PED-FUTURO-'.$i, today()->addDays($i)->toDateString());
        }
        $this->pedido('PED-HOY', today()->toDateString());
        $this->get('/pedidos/agenda?periodo=proximos')->assertOk()
            ->assertViewHas('pedidos', fn ($pedidos) => $pedidos->total() === 13 && $pedidos->count() === 12)
            ->assertSee('periodo=proximos', false);
        $this->get('/pedidos/agenda?periodo=hoy')->assertOk()
            ->assertViewHas('pedidos', fn ($pedidos) => $pedidos->total() === 1 && $pedidos->first()->numero_pedido === 'PED-HOY');
        $this->get('/pedidos/agenda?periodo=invalido')->assertSessionHasErrors('periodo');
    }

    public function test_agenda_requiere_autenticacion_y_muestra_estado_vacio(): void
    {
        $this->get('/pedidos/agenda')->assertRedirect('/login');
        foreach (['Administrador', 'Empleado'] as $cargo) {
            $this->actingAs(User::factory()->create(['cargo' => $cargo]));
            $this->get('/pedidos/agenda')->assertOk()->assertSee('No hay pedidos para esta vista.');
        }
    }

    public function test_advierte_faltantes_y_el_inicio_desde_la_agenda_reutiliza_el_consumo_existente(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $pedido = $this->pedido('PED-AGENDA', today()->toDateString());
        $categoria = Categoria::create(['nombre' => 'Zanahoria', 'slug' => 'zanahoria']);
        $producto = Producto::create(['nombre' => 'Queque de zanahoria', 'categoria_id' => $categoria->id, 'precio_venta' => 50, 'costo_produccion' => 20]);
        $pedido->detalles()->create(['producto_id' => $producto->id, 'cantidad' => 2, 'precio_unitario' => 50, 'subtotal' => 100]);
        $insumo = Insumo::create(['nombre' => 'Harina', 'unidad' => 'Kg', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 8]);
        $pedido->update(['insumos_reservados_at' => now()]);
        $pedido->reservas()->create(['insumo_id' => $insumo->id, 'cantidad' => 3]);
        $this->get('/pedidos/agenda')->assertOk()->assertSee('Reponer insumos')->assertSee('Harina')->assertSee('Queque de zanahoria');

        MovimientoInsumo::registrar($insumo, 'Entrada', 10, 'Compra', codigoLote: 'L1', fechaVencimiento: today()->addDays(20)->toDateString());
        $this->get('/pedidos/agenda')->assertOk()->assertSee('Iniciar preparación')->assertSee('Reserva registrada');
        $this->assertEquals(10, $insumo->fresh()->stock_actual);
        $this->from('/pedidos/agenda')->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'En proceso'])
            ->assertRedirect('/pedidos/agenda')->assertSessionHasNoErrors();
        $this->assertSame('En proceso', $pedido->fresh()->estado);
        $this->assertEquals(7, $insumo->fresh()->stock_actual);
        $this->assertEquals(0, $pedido->reservas()->count());
    }
}
