<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AutenticacionUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_iniciar_sesion_y_ser_recordado(): void
    {
        $user = User::factory()->create([
            'username' => 'yairl',
            'password' => Hash::make('contrasena-segura'),
        ]);

        $this->post(route('login'), ['username' => 'yairl', 'password' => 'contrasena-segura', 'remember' => '1'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_registro_publico_no_esta_disponible_y_el_administrador_crea_empleados(): void
    {
        $this->get('/register')->assertNotFound();

        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $this->actingAs($admin)->post(route('usuarios.store'), [
            'username' => 'nuevo_empleado',
            'nombre' => 'Nuevo',
            'apellido' => 'Empleado',
            'email' => 'nuevo@example.com',
            'password' => 'contrasena-segura',
            'password_confirmation' => 'contrasena-segura',
        ])->assertRedirect(route('register'));

        $this->assertDatabaseHas('users', ['username' => 'nuevo_empleado', 'cargo' => 'Empleado']);
    }

    public function test_el_perfil_no_permite_cambiar_el_cargo(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);

        $this->actingAs($admin)->put(route('profile.update'), [
            'nombre' => 'Lidia',
            'apellido' => '',
            'telefono' => '',
            'ciudad' => '',
            'cargo' => 'Empleado',
        ])->assertRedirect();

        $this->assertSame('Administrador', $admin->fresh()->cargo);
        $this->assertSame('Lidia', $admin->fresh()->name);
    }
}
