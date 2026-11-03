<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidosRecientesTest extends TestCase
{
    use RefreshDatabase;

    private function pedido(Cliente $cliente, string $fechaEntrega, string $estado): Pedido
    {
        $numero = Pedido::count() + 1;

        return Pedido::create([
            'numero_pedido' => 'PED-'.$numero,
            'cliente_id' => $cliente->id,
            'tipo_pedido' => 'Predefinido',
            'fecha_pedido' => today(),
            'fecha_entrega' => $fechaEntrega,
            'direccion_entrega' => 'Casa',
            'telefono_contacto' => '71234567',
            'metodo_pago' => 'Efectivo',
            'estado' => $estado,
        ]);
    }

    public function test_muestra_entregas_del_mes_y_atrasados_activos_conservando_el_historial(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 3)->startOfDay());
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $cliente = Cliente::create(['nombre_completo' => 'Ana', 'telefono_principal' => '71234567', 'direccion' => 'Casa']);

        $visibles = collect([
            $this->pedido($cliente, '2026-10-31', 'Pendiente'),
            $this->pedido($cliente, '2026-09-15', 'En proceso'),
            $this->pedido($cliente, '2026-11-01', 'Completado'),
            $this->pedido($cliente, '2026-11-30', 'Cancelado'),
            $this->pedido($cliente, '2026-11-15', 'Pendiente'),
        ]);
        $completadoAnterior = $this->pedido($cliente, '2026-10-31', 'Completado');
        $this->pedido($cliente, '2026-10-31', 'Cancelado');
        $this->pedido($cliente, '2025-11-15', 'Completado');
        $this->pedido($cliente, '2026-12-01', 'Pendiente');

        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('pedidosRecientes', fn ($pedidos) =>
                $pedidos->modelKeys() === $visibles->reverse()->pluck('id')->values()->all()
            );

        $this->assertDatabaseCount('pedidos', 9);
        $this->get(route('ventas.index'))->assertOk()->assertSee($completadoAnterior->numero_pedido);
        $this->get(route('pedidos.show', $completadoAnterior))->assertOk();
    }

    public function test_al_cambiar_de_mes_oculta_completados_y_mantiene_atrasados(): void
    {
        $this->travelTo(now()->setDate(2026, 12, 31)->setTime(23, 59));
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $cliente = Cliente::create(['nombre_completo' => 'Ana', 'telefono_principal' => '71234567', 'direccion' => 'Casa']);
        $completado = $this->pedido($cliente, '2026-12-31', 'Completado');
        $pendiente = $this->pedido($cliente, '2026-12-31', 'Pendiente');
        $enero = $this->pedido($cliente, '2027-01-01', 'En proceso');

        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('pedidosRecientes', fn ($pedidos) =>
                $pedidos->modelKeys() === [$pendiente->id, $completado->id]
            );

        $this->travel(2)->minutes();

        $this->get(route('dashboard'))->assertOk()
            ->assertViewHas('pedidosRecientes', fn ($pedidos) =>
                $pedidos->modelKeys() === [$enero->id, $pendiente->id]
            );
        $this->assertDatabaseCount('pedidos', 3);
    }
}
