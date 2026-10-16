<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisosUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_empleado_no_puede_abrir_secciones_administrativas(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));

        foreach (['reportes', 'reportes/ventas.csv', 'productos', 'insumos', 'categorias'] as $ruta) {
            $this->get('/'.$ruta)->assertRedirect(route('dashboard'))
                ->assertSessionHas('error');
        }

        foreach (['productos', 'insumos', 'categorias'] as $ruta) {
            $this->post('/'.$ruta, [])->assertRedirect(route('dashboard'));
        }
    }

    public function test_administrador_puede_abrir_secciones_administrativas(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Administrador']));

        foreach (['dashboard', 'reportes', 'productos', 'insumos', 'categorias'] as $ruta) {
            $this->get('/'.$ruta)->assertOk();
        }
    }

    public function test_empleado_puede_abrir_secciones_operativas_sin_menu_administrativo(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));

        foreach (['dashboard', 'clientes', 'pedidos', 'ventas', 'perfil'] as $ruta) {
            $this->get('/'.$ruta)->assertOk()
                ->assertDontSee('href="'.route('reportes.index').'"', false)
                ->assertDontSee('href="'.route('insumos.index').'"', false);
        }
    }

    public function test_empleado_no_puede_ascender_desde_su_perfil(): void
    {
        $empleado = User::factory()->create(['cargo' => 'Empleado']);
        $this->actingAs($empleado)->put('/perfil', [
            'nombre' => 'Yair',
            'apellido' => 'Prueba',
            'email' => $empleado->email,
            'cargo' => 'Administrador',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Empleado', $empleado->fresh()->cargo);
    }

    public function test_visitante_necesita_iniciar_sesion(): void
    {
        foreach (['dashboard', 'clientes', 'pedidos', 'ventas', 'reportes', 'perfil'] as $ruta) {
            $this->get('/'.$ruta)->assertRedirect(route('login'));
        }
    }
}
