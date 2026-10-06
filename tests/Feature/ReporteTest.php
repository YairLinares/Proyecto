<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    private function pedido(Cliente $cliente, Producto $producto, string $fecha, string $estado, float $total): Pedido
    {
        $pedido = Pedido::create([
            'numero_pedido' => 'PED-'.str_pad((string) (Pedido::count() + 1), 3, '0', STR_PAD_LEFT),
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'Predefinido',
            'prioridad' => 'Normal',
            'fecha_pedido' => $fecha,
            'fecha_entrega' => $fecha,
            'direccion_entrega' => 'Casa',
            'telefono_contacto' => '123456',
            'metodo_pago' => 'Efectivo',
            'subtotal' => $total,
            'total' => $total,
            'estado' => $estado,
        ]);
        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'precio_unitario' => $total / 2,
            'subtotal' => $total,
        ]);

        return $pedido;
    }

    public function test_administrador_ve_solo_ventas_del_periodo_y_descarga_el_mismo_filtro(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        $cliente = Cliente::create(['nombre_completo' => '=SUM(1,2)', 'telefono_principal' => '123456', 'direccion' => 'Casa']);
        $categoria = Categoria::create(['nombre' => 'Tortas', 'slug' => 'tortas']);
        $producto = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Queque', 'precio_venta' => 50, 'costo_produccion' => 20]);
        $this->pedido($cliente, $producto, '2026-10-03', 'Completado', 100);
        $this->pedido($cliente, $producto, '2026-09-25', 'Completado', 80);
        $this->pedido($cliente, $producto, '2026-10-04', 'Pendiente', 60);
        $insumo = Insumo::create(['nombre' => 'Harina', 'unidad' => 'Kg', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 10]);
        $this->travelTo(Carbon::parse('2026-10-04 10:00:00'));
        MovimientoInsumo::registrar($insumo, 'Entrada', 5, 'Compra');
        MovimientoInsumo::registrar($insumo, 'Salida', 2, 'Merma por caducidad');
        $this->travelBack();

        $filtro = ['desde' => '2026-10-01', 'hasta' => '2026-10-06'];
        $this->get(route('reportes.index', $filtro))
            ->assertOk()
            ->assertSee('Bs 100,00')
            ->assertSee('Queque')
            ->assertSee('Merma por caducidad')
            ->assertSee('Pedidos pendientes');

        $csv = $this->get(route('reportes.ventas.csv', $filtro));
        $csv->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $contenido = $csv->streamedContent();
        $this->assertStringContainsString('PED-001', $contenido);
        $this->assertStringNotContainsString('PED-002', $contenido);
        $this->assertStringNotContainsString('PED-003', $contenido);
        $this->assertStringContainsString("'=SUM(1,2)", $contenido);
    }

    public function test_empleado_no_puede_ver_ni_descargar_reportes(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $this->get(route('reportes.index'))->assertRedirect(route('dashboard'));
        $this->get(route('reportes.ventas.csv'))->assertRedirect(route('dashboard'));
    }

    public function test_rechaza_rango_de_fechas_invertido(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        $this->get(route('reportes.index', ['desde' => '2026-10-06', 'hasta' => '2026-10-01']))->assertSessionHasErrors('hasta');
        $this->get(route('reportes.ventas.csv', ['desde' => '2026-10-06', 'hasta' => '2026-10-01']))->assertSessionHasErrors('hasta');
    }
}
