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
        $this->actingAs($admin)->get(route('register'))
            ->assertRedirect(route('empleados.index', ['nuevo' => 1]));
        $this->get(route('empleados.index', ['nuevo' => 1]))->assertOk()
            ->assertSee('data-bs-target="#nuevoEmpleado"', false)
            ->assertSee('Celular del empleado')
            ->assertSee('Ciudad');

        $this->actingAs($admin)->post(route('usuarios.store'), [
            'username' => 'nuevo_empleado',
            'nombre' => 'Nuevo',
            'apellido' => 'Empleado',
            'email' => 'nuevo@example.com',
            'telefono' => '71234567',
            'ciudad' => 'La Paz',
            'password' => 'contrasena-segura',
            'password_confirmation' => 'contrasena-segura',
        ])->assertRedirect(route('empleados.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'nuevo_empleado',
            'cargo' => 'Empleado',
            'telefono' => '71234567',
            'ciudad' => 'La Paz',
        ]);
    }

    public function test_registro_de_empleado_exige_celular_de_ocho_digitos_y_ciudad(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);

        $this->actingAs($admin)->post(route('usuarios.store'), [
            'username' => 'sin_datos',
            'nombre' => 'Ana',
            'apellido' => 'Flores',
            'email' => 'ana@example.com',
            'telefono' => '123',
            'ciudad' => '',
            'password' => 'contrasena-segura',
            'password_confirmation' => 'contrasena-segura',
        ])->assertSessionHasErrors(['telefono', 'ciudad']);

        $this->assertDatabaseMissing('users', ['username' => 'sin_datos']);
    }

    public function test_errores_del_registro_se_muestran_en_espanol(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $existente = User::factory()->create(['cargo' => 'Empleado']);

        $this->actingAs($admin)->from(route('empleados.index'))->post(route('usuarios.store'), [
            'username' => $existente->username,
            'nombre' => '',
            'apellido' => 'Linares',
            'email' => $existente->email,
            'password' => 'corta',
            'password_confirmation' => 'diferente',
            'telefono' => '63324544',
            'ciudad' => 'Santa Cruz',
        ])->assertRedirect(route('empleados.index'))->assertSessionHasErrors([
            'username' => 'Ese nombre de usuario ya está registrado. Elige otro.',
            'email' => 'Ese correo electrónico ya está registrado. Usa otro.',
            'nombre' => 'El campo nombre es obligatorio.',
            'password' => 'El campo contraseña debe tener al menos 8 caracteres.',
        ]);

        $this->get(route('empleados.index'))->assertOk()
            ->assertSee('Ese nombre de usuario ya está registrado. Elige otro.')
            ->assertSee('bootstrap.Offcanvas.getOrCreateInstance', false)
            ->assertSee('value="'.$existente->username.'"', false)
            ->assertDontSee('The username has already been taken.');
    }

    public function test_el_perfil_no_permite_cambiar_el_cargo(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);

        $this->actingAs($admin)->get(route('profile.show'))->assertOk()
            ->assertSee('id="botonEditarPerfil"', false)
            ->assertSee('action="'.route('profile.update').'"', false);

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
