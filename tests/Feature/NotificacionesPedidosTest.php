<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificacionesPedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_al_cambiar_de_dia_oculta_avisos_cerrados_y_conserva_pedidos_pendientes(): void
    {
        $this->travelTo(now()->startOfDay()->setTime(23, 59));
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));
        $cliente = Cliente::create([
            'nombre_completo' => 'Ana Flores',
            'telefono_principal' => '71234567',
            'direccion' => 'Casa',
        ]);

        foreach (['Pendiente', 'En proceso', 'Completado', 'Cancelado'] as $indice => $estado) {
            Pedido::create([
                'numero_pedido' => 'PED-'.($indice + 1),
                'cliente_id' => $cliente->id,
                'tipo_pedido' => 'Predefinido',
                'fecha_pedido' => today(),
                'fecha_entrega' => today()->addDay(),
                'direccion_entrega' => 'Casa',
                'telefono_contacto' => '71234567',
                'metodo_pago' => 'Efectivo',
                'estado' => $estado,
            ]);
        }

        $this->get(route('profile.show'))->assertOk()
            ->assertSee('Pedido PED-3 de Ana Flores esta completado')
            ->assertSee('Pedido PED-4 de Ana Flores esta cancelado');

        $this->travel(2)->minutes();

        $this->get(route('profile.show'))->assertOk()
            ->assertSee('Pedido PED-1 de Ana Flores esta pendiente')
            ->assertSee('Pedido PED-2 de Ana Flores esta en proceso')
            ->assertDontSee('Pedido PED-3 de Ana Flores esta completado')
            ->assertDontSee('Pedido PED-4 de Ana Flores esta cancelado');

        $this->assertDatabaseCount('pedidos', 4);
    }
}
