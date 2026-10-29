<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermisosUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_empleado_no_puede_abrir_secciones_administrativas(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));

        foreach (['empleados', 'reportes', 'reportes/ventas.csv', 'productos', 'insumos', 'categorias', 'compartir'] as $ruta) {
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

        foreach (['dashboard', 'empleados', 'reportes', 'productos', 'insumos', 'categorias', 'compartir'] as $ruta) {
            $this->get('/'.$ruta)->assertOk();
        }

        $this->get('/dashboard')->assertSee('href="'.route('compartir').'"', false);
    }

    public function test_empleado_puede_abrir_secciones_operativas_sin_menu_administrativo(): void
    {
        $this->actingAs(User::factory()->create(['cargo' => 'Empleado']));

        foreach (['dashboard', 'clientes', 'pedidos', 'ventas', 'perfil'] as $ruta) {
            $this->get('/'.$ruta)->assertOk()
                ->assertDontSee('href="'.route('reportes.index').'"', false)
                ->assertDontSee('href="'.route('compartir').'"', false)
                ->assertDontSee('href="'.route('insumos.index').'"', false);
        }
    }

    public function test_empleado_consulta_su_perfil_pero_no_puede_editar_sus_datos(): void
    {
        $empleado = User::factory()->create(['cargo' => 'Empleado']);
        $datosOriginales = $empleado->fresh()->getAttributes();

        $this->actingAs($empleado)->get(route('profile.show'))->assertOk()
            ->assertSee($empleado->username)
            ->assertSee('Para corregir tus datos, contacta al administrador.')
            ->assertDontSee('id="botonEditarPerfil"', false)
            ->assertDontSee('action="'.route('profile.update').'"', false)
            ->assertSee('action="'.route('profile.password').'"', false);

        $this->actingAs($empleado)->put('/perfil', [
            'nombre' => 'Yair',
            'apellido' => 'Prueba',
            'email' => $empleado->email,
            'cargo' => 'Administrador',
        ])->assertRedirect(route('dashboard'))->assertSessionHas('error');

        $this->assertSame($datosOriginales, $empleado->fresh()->getAttributes());
    }

    public function test_visitante_necesita_iniciar_sesion(): void
    {
        foreach (['dashboard', 'clientes', 'pedidos', 'ventas', 'reportes', 'perfil', 'empleados', 'compartir'] as $ruta) {
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

    public function test_solo_administrador_puede_eliminar_cuentas_de_empleados(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $empleado = User::factory()->create(['cargo' => 'Empleado']);
        $otroEmpleado = User::factory()->create(['cargo' => 'Empleado']);

        $this->actingAs($empleado)->delete(route('empleados.destroy', $otroEmpleado))
            ->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['id' => $otroEmpleado->id]);

        $this->actingAs($admin)->delete(route('empleados.destroy', $admin))->assertNotFound();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        DB::table('sessions')->insert([
            'id' => 'sesion-empleado',
            'user_id' => $otroEmpleado->id,
            'payload' => '',
            'last_activity' => time(),
        ]);
        DB::table('admin_session_lock')->where('id', 2)->update([
            'user_id' => $otroEmpleado->id,
            'token' => 'sesion-empleado',
            'last_activity' => time(),
            'session_id' => 'sesion-empleado',
        ]);

        $this->actingAs($admin)->delete(route('empleados.destroy', $otroEmpleado))
            ->assertRedirect(route('empleados.index'));

        $this->assertDatabaseMissing('users', ['id' => $otroEmpleado->id]);
        $this->assertDatabaseMissing('sessions', ['user_id' => $otroEmpleado->id]);
        $this->assertDatabaseHas('admin_session_lock', ['id' => 2, 'user_id' => null]);
    }
}
