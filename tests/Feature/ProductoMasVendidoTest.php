<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductoMasVendidoTest extends TestCase
{
    use RefreshDatabase;

    private function venta(Cliente $cliente, Producto $producto, string $estado, int $cantidad): void
    {
        $pedido = Pedido::create([
            'numero_pedido' => 'PED-'.(Pedido::count() + 1),
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'Predefinido',
            'fecha_pedido' => today(),
            'fecha_entrega' => today(),
            'direccion_entrega' => 'Casa',
            'telefono_contacto' => '71234567',
            'metodo_pago' => 'Efectivo',
            'estado' => $estado,
        ]);
        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => 30,
            'subtotal' => 30 * $cantidad,
        ]);
    }

    public function test_muestra_foto_y_unidades_del_lider_sin_contar_pedidos_pendientes_o_cancelados(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/zanahoria.jpg', 'foto de prueba');
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $cliente = Cliente::create(['nombre_completo' => 'Ana', 'telefono_principal' => '71234567', 'direccion' => 'Casa']);
        $categoria = Categoria::create(['nombre' => 'Queques', 'slug' => 'queques']);
        $zanahoria = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Queque de zanahoria', 'precio_venta' => 30, 'costo_produccion' => 10, 'imagen' => 'productos/zanahoria.jpg']);
        $chocolate = Producto::create(['categoria_id' => $categoria->id, 'nombre' => 'Queque de chocolate', 'precio_venta' => 30, 'costo_produccion' => 10]);

        $this->venta($cliente, $zanahoria, 'Completado', 2);
        $this->venta($cliente, $zanahoria, 'Completado', 3);
        $this->venta($cliente, $chocolate, 'Completado', 4);
        $this->venta($cliente, $chocolate, 'Pendiente', 20);
        $this->venta($cliente, $chocolate, 'Cancelado', 30);

        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('productoMasVendido', fn ($producto) => $producto->id === $zanahoria->id && (int) $producto->cantidad === 5)
            ->assertSee('src="'.asset('storage/productos/zanahoria.jpg').'"', false)
            ->assertSee('5 unidades vendidas')
            ->assertDontSee('chartProductosMasVendidos');

        Storage::disk('public')->delete('productos/zanahoria.jpg');
        $this->get(route('dashboard'))->assertOk()->assertSee('Sin foto');
    }

    public function test_sin_ventas_completadas_no_inventa_un_producto_ganador(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('productoMasVendido', fn ($producto) => $producto === null)
            ->assertSee('Aún no hay productos vendidos en pedidos completados.');
    }
}
