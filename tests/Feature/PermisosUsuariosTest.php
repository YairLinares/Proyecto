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

        foreach (['empleados', 'reportes', 'reportes/ventas.csv', 'productos', 'insumos', 'categorias'] as $ruta) {
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

        foreach (['dashboard', 'empleados', 'reportes', 'productos', 'insumos', 'categorias'] as $ruta) {
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
        foreach (['dashboard', 'clientes', 'pedidos', 'ventas', 'reportes', 'perfil', 'empleados'] as $ruta) {
            $this->get('/'.$ruta)->assertRedirect(route('login'));
        }
    }

    public function test_administrador_ve_empleados_en_administracion_y_no_en_dashboard(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $otroAdmin = User::factory()->create(['cargo' => 'Administrador', 'nombre' => 'Otra', 'apellido' => 'Administradora']);
        $empleado = User::factory()->create([
            'cargo' => 'Empleado',
            'nombre' => 'Ana',
            'apellido' => 'Flores',
            'username' => 'ana_flores',
            'email' => 'ana@example.com',
            'telefono' => '12345678',
            'ciudad' => 'La Paz',
        ]);

        $this->actingAs($admin)->get('/dashboard')->assertOk()
            ->assertDontSee('Total de cuentas con el cargo Empleado');

        $this->actingAs($admin)->get(route('empleados.index'))->assertOk()
            ->assertViewHas('empleadosTotal', 1)
            ->assertSee('Ana Flores')
            ->assertSee('ana_flores')
            ->assertSee('ana@example.com')
            ->assertSee('12345678')
            ->assertSee('La Paz')
            ->assertDontSee('Otra Administradora');

        $this->actingAs($admin)->get(route('empleados.index', ['buscar' => 'ana_flores']))->assertOk()
            ->assertSee('Ana Flores');

        $this->actingAs($admin)->get(route('empleados.index', ['buscar' => 'sin_coincidencia']))->assertOk()
            ->assertDontSee('Ana Flores');

        $this->actingAs($empleado)->get(route('empleados.index'))
            ->assertRedirect(route('dashboard'));
    }
}
