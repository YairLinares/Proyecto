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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class IntegridadRegistrosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));
        Storage::fake('public');
    }

    private function cliente(): Cliente
    {
        return Cliente::create(['nombre_completo' => 'Ana Flores', 'telefono_principal' => '07123456', 'direccion' => 'Casa']);
    }

    private function insumo(): Insumo
    {
        $insumo = Insumo::create(['nombre' => 'Harina de prueba', 'unidad' => 'Kg', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 8]);
        MovimientoInsumo::registrar($insumo, 'Entrada', 20, 'Compra de prueba', codigoLote: 'PRUEBA', fechaVencimiento: today()->addMonth()->toDateString());
        return $insumo;
    }

    private function datosProducto(Insumo $insumo): array
    {
        $categoria = Categoria::create(['nombre' => 'Zanahoria', 'slug' => 'zanahoria']);
        return ['categoria_id' => $categoria->id, 'nombre' => 'Queque de prueba', 'precio_venta' => 30, 'tiempo_preparacion_dias' => 1, 'insumos' => [$insumo->id => 0.25]];
    }

    private function producto(Insumo $insumo): Producto
    {
        $datos = $this->datosProducto($insumo);
        $this->post(route('productos.store'), $datos)->assertSessionHasNoErrors()->assertRedirect();
        return Producto::where('nombre', $datos['nombre'])->firstOrFail();
    }

    private function datosPedido(Cliente $cliente, Producto $producto): array
    {
        return ['cliente_id' => $cliente->id, 'tipo_pedido' => 'Personalizado', 'prioridad' => 'Normal', 'fecha_entrega' => today()->addDay()->toDateString(), 'metodo_pago' => 'Efectivo', 'productos' => [$producto->id => ['cantidad' => 2]]];
    }

    public function test_cliente_sin_direccion_y_estado_vacio_no_provoca_error(): void
    {
        $datos = ['nombre_completo' => 'Cliente nuevo', 'telefono_principal' => '07123456'];
        $this->post(route('clientes.store'), $datos)->assertRedirect(route('clientes.index'))->assertSessionHasNoErrors();
        $cliente = Cliente::where('nombre_completo', 'Cliente nuevo')->firstOrFail();
        $this->assertSame('No especificada', $cliente->direccion);
        $this->put(route('clientes.update', $cliente), $datos + ['estado' => null])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('activo', $cliente->fresh()->estado);
        $this->assertSame('07123456', $cliente->fresh()->telefono_principal);
    }

    public function test_sabores_con_nombres_que_generan_el_mismo_slug_se_pueden_guardar(): void
    {
        foreach (['Queque clásico', 'Queque-clasico'] as $nombre) {
            $this->post(route('categorias.store'), ['nombre' => $nombre])->assertRedirect()->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('categorias', 2);
        $this->assertSame(2, Categoria::pluck('slug')->unique()->count());
    }

    public function test_alta_y_edicion_de_producto_conservan_receta_y_costo(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $this->assertEquals(2, $producto->costo_produccion);
        $this->assertEquals(0.25, $producto->insumos()->first()->pivot->cantidad_necesaria);
        $this->put(route('productos.update', $producto), ['categoria_id' => $producto->categoria_id, 'nombre' => $producto->nombre, 'precio_venta' => 35, 'tiempo_preparacion_dias' => 1, 'estado' => 'activo', 'insumos' => [$insumo->id => 0.5]])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(4, $producto->fresh()->costo_produccion);
        $this->assertEquals(0.5, $producto->insumos()->first()->pivot->cantidad_necesaria);
    }

    public function test_error_al_guardar_producto_revierte_el_registro(): void
    {
        $datos = $this->datosProducto($this->insumo());
        Producto::created(fn () => throw new RuntimeException('Fallo simulado de persistencia'));
        $this->withoutExceptionHandling();
        try {
            $this->post(route('productos.store'), $datos);
            $this->fail('Debió producirse el fallo simulado.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Fallo simulado de persistencia', $exception->getMessage());
        } finally {
            Producto::flushEventListeners();
        }
        $this->assertDatabaseCount('productos', 0);
        $this->assertDatabaseCount('insumo_producto', 0);
    }

    public function test_insumo_con_historial_no_permite_cambiar_unidad(): void
    {
        $insumo = $this->insumo();
        $this->put(route('insumos.update', $insumo), ['nombre' => $insumo->nombre, 'unidad' => 'Gramos', 'stock_actual' => 20, 'stock_minimo' => 1, 'precio_unitario' => 8])->assertSessionHasErrors('unidad');
        $this->assertSame('Kg', $insumo->fresh()->unidad);
        $this->assertEquals(20, $insumo->fresh()->stock_actual);
    }

    public function test_numeros_fuera_de_precision_o_capacidad_se_rechazan_sin_modificar_stock(): void
    {
        $insumo = $this->insumo();
        foreach ([0.001, 100000000, '1e100'] as $cantidad) {
            $this->post(route('insumos.movimientos.store', $insumo), ['tipo' => 'Entrada', 'cantidad' => $cantidad, 'motivo' => 'Prueba de límites'])->assertSessionHasErrors('cantidad');
            $this->assertEquals(20, $insumo->fresh()->stock_actual);
        }
        $this->post(route('insumos.movimientos.store', $insumo), ['tipo' => 'Entrada', 'cantidad' => 0, 'motivo' => 'Entrada vacía'])->assertSessionHasErrors('cantidad');
        $this->assertSame(1, $insumo->movimientos()->count());
    }

    public function test_no_se_admiten_recetas_que_se_redondean_a_cero(): void
    {
        $insumo = $this->insumo();
        $datos = $this->datosProducto($insumo);
        $datos['insumos'] = [$insumo->id => 0.001];
        $this->post(route('productos.store'), $datos)->assertSessionHasErrors('insumos.'.$insumo->id);
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_pedido_con_importes_excesivos_no_deja_registros_ni_reservas(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $datos = $this->datosPedido($this->cliente(), $producto);
        foreach (['descuento' => 61, 'anticipo_recibido' => 61] as $campo => $importe) {
            $this->post(route('pedidos.store'), $datos + [$campo => $importe])->assertSessionHasErrors($campo);
            $this->assertDatabaseCount('pedidos', 0);
            $this->assertDatabaseCount('reservas_insumo', 0);
        }
        $datos['productos'][$producto->id]['cantidad'] = 100000000;
        $this->post(route('pedidos.store'), $datos)->assertSessionHasErrors('productos');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_busqueda_de_pedidos_respeta_el_filtro_de_estado(): void
    {
        $cliente = $this->cliente();
        foreach (['Pendiente', 'Completado'] as $indice => $estado) {
            Pedido::create(['numero_pedido' => 'PED-'.($indice + 1), 'cliente_id' => $cliente->id, 'tipo_pedido' => 'Predefinido', 'fecha_pedido' => today(), 'fecha_entrega' => today(), 'direccion_entrega' => 'Casa', 'telefono_contacto' => '07123456', 'metodo_pago' => 'Efectivo', 'estado' => $estado]);
        }
        $this->get(route('pedidos.index', ['search' => 'PED-', 'estado' => 'Completado']))->assertOk()
            ->assertViewHas('pedidos', fn ($pedidos) => $pedidos->total() === 1 && $pedidos->first()->estado === 'Completado');
    }

    public function test_ventas_rechaza_fechas_invalidas_y_rangos_invertidos(): void
    {
        $this->get(route('ventas.index', ['desde' => 'fecha-invalida']))->assertSessionHasErrors('desde');
        $this->get(route('ventas.index', ['desde' => '2026-11-05', 'hasta' => '2026-11-01']))->assertSessionHasErrors('hasta');
    }

    public function test_compras_del_cliente_se_calculan_con_pedidos_completados(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $cliente = $this->cliente();
        $this->post(route('pedidos.store'), $this->datosPedido($cliente, $producto))->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'En proceso'])->assertSessionHasNoErrors();
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'Completado'])->assertSessionHasNoErrors();
        $this->get(route('clientes.show', $cliente))->assertOk()->assertViewHas('cliente', fn ($c) => (float) $c->total_compras === 60.0);
        $this->patch(route('pedidos.cambiarEstado', $pedido), ['estado' => 'Cancelado'])->assertSessionHasNoErrors();
        $this->get(route('clientes.show', $cliente))->assertOk()->assertViewHas('cliente', fn ($c) => (float) $c->total_compras === 0.0);
        $this->assertEquals(19.5, $insumo->fresh()->stock_actual);
    }

    public function test_todas_las_pantallas_de_registro_y_consulta_cargan_con_datos(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $cliente = $this->cliente();
        $this->post(route('pedidos.store'), $this->datosPedido($cliente, $producto))->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        foreach (['clientes', 'categorias', 'productos', 'insumos', 'pedidos'] as $modulo) {
            $this->get(route($modulo.'.index'))->assertOk();
            $this->get(route($modulo.'.create'))->assertOk();
        }
        foreach (['clientes' => $cliente, 'categorias' => $producto->categoria_id, 'productos' => $producto, 'insumos' => $insumo, 'pedidos' => $pedido] as $modulo => $registro) {
            $this->get(route($modulo.'.show', $registro))->assertOk();
            $this->get(route($modulo.'.edit', $registro))->assertOk();
        }
        foreach (['dashboard', 'ventas.index', 'reportes.index', 'empleados.index', 'profile.show'] as $ruta) $this->get(route($ruta))->assertOk();
    }

    public function test_fallo_al_editar_producto_conserva_receta_precio_y_foto_anterior(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        Storage::disk('public')->put('productos/anterior.png', 'imagen anterior');
        $producto->update(['imagen' => 'productos/anterior.png']);
        $imagen = UploadedFile::fake()->createWithContent('nueva.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jVBAAAAAASUVORK5CYII='));
        Producto::updated(fn () => throw new RuntimeException('Fallo simulado al editar'));
        $this->withoutExceptionHandling();
        try {
            $this->put(route('productos.update', $producto), ['categoria_id' => $producto->categoria_id, 'nombre' => $producto->nombre, 'precio_venta' => 99, 'tiempo_preparacion_dias' => 1, 'estado' => 'activo', 'insumos' => [$insumo->id => 0.5], 'imagen' => $imagen]);
            $this->fail('Debió producirse el fallo simulado.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Fallo simulado al editar', $exception->getMessage());
        } finally {
            Producto::flushEventListeners();
        }
        $this->assertEquals(30, $producto->fresh()->precio_venta);
        $this->assertEquals(0.25, $producto->insumos()->first()->pivot->cantidad_necesaria);
        $this->assertSame(['productos/anterior.png'], Storage::disk('public')->allFiles('productos'));
    }

    public function test_importe_invalido_al_editar_no_inicia_produccion_ni_consume_stock(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $cliente = $this->cliente();
        $this->post(route('pedidos.store'), $this->datosPedido($cliente, $producto))->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        $this->put(route('pedidos.update', $pedido), ['cliente_id' => $cliente->id, 'tipo_pedido' => 'Personalizado', 'prioridad' => 'Normal', 'fecha_entrega' => today()->addDay()->toDateString(), 'direccion_entrega' => 'Casa', 'telefono_contacto' => '07123456', 'metodo_pago' => 'Efectivo', 'estado' => 'En proceso', 'anticipo_recibido' => 61])->assertSessionHasErrors('anticipo_recibido');
        $this->assertSame('Pendiente', $pedido->fresh()->estado);
        $this->assertNull($pedido->fresh()->produccion_iniciada_at);
        $this->assertEquals(20, $insumo->fresh()->stock_actual);
        $this->assertEquals(0.5, $pedido->reservas()->sum('cantidad'));
    }

    public function test_busquedas_con_formato_incorrecto_muestran_validacion(): void
    {
        foreach (['clientes.index', 'productos.index', 'insumos.index', 'pedidos.index'] as $ruta) {
            $this->get(route($ruta, ['search' => ['dato-invalido']]))->assertRedirect()->assertSessionHasErrors('search');
        }
        $this->get(route('ventas.index', ['buscar' => ['dato-invalido']]))->assertRedirect()->assertSessionHasErrors('buscar');
    }

    public function test_nombre_completo_demasiado_largo_no_rompe_el_registro_de_empleado(): void
    {
        $this->post(route('usuarios.store'), ['username' => 'empleado_prueba', 'nombre' => str_repeat('A', 150), 'apellido' => str_repeat('B', 150), 'email' => 'prueba@example.com', 'telefono' => '71234567', 'ciudad' => 'Santa Cruz', 'password' => 'contrasena-segura', 'password_confirmation' => 'contrasena-segura'])->assertSessionHasErrors('nombre');
        $this->assertDatabaseMissing('users', ['username' => 'empleado_prueba']);
    }

    public function test_entrada_que_desborda_el_stock_se_revierte(): void
    {
        $insumo = $this->insumo();
        $this->post(route('insumos.movimientos.store', $insumo), ['tipo' => 'Entrada', 'cantidad' => 99999999.99, 'motivo' => 'Prueba de desbordamiento'])->assertSessionHasErrors('cantidad');
        $this->assertEquals(20, $insumo->fresh()->stock_actual);
        $this->assertSame(1, $insumo->movimientos()->count());
    }

    public function test_editar_importes_recalcula_el_total_y_conserva_importes_omitidos(): void
    {
        $insumo = $this->insumo();
        $producto = $this->producto($insumo);
        $cliente = $this->cliente();
        $this->post(route('pedidos.store'), $this->datosPedido($cliente, $producto))->assertSessionHasNoErrors();
        $pedido = Pedido::firstOrFail();
        $datos = ['cliente_id' => $cliente->id, 'tipo_pedido' => 'Personalizado', 'prioridad' => 'Normal', 'fecha_entrega' => today()->addDay()->toDateString(), 'direccion_entrega' => 'Casa', 'telefono_contacto' => '07123456', 'metodo_pago' => 'Efectivo', 'estado' => 'Pendiente'];
        $this->put(route('pedidos.update', $pedido), $datos + ['descuento' => 5, 'costo_envio' => 10, 'anticipo_recibido' => 20])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(65, $pedido->fresh()->total);
        $this->put(route('pedidos.update', $pedido), $datos)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertEquals(65, $pedido->fresh()->total);
        $this->assertEquals(20, $pedido->fresh()->anticipo_recibido);
    }
}
