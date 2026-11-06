<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTunnelTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://example.devtunnels.ms']);
    }

    public function test_login_form_and_logo_use_public_url_when_host_is_localhost(): void
    {
        $this->withHeader('X-Forwarded-Host', 'example.devtunnels.ms')->get('http://localhost:8000/login')
            ->assertOk()
            ->assertSee('action="https://example.devtunnels.ms/login"', false)
            ->assertSee('src="https://example.devtunnels.ms/images/logo-delicias-dulces.webp"', false)
            ->assertDontSee('http://localhost:8000', false);
    }

    public function test_successful_login_redirects_to_public_dashboard(): void
    {
        $user = User::factory()->create(['cargo' => 'Empleado']);

        $this->withHeader('X-Forwarded-Host', 'example.devtunnels.ms')->post('http://localhost:8000/login', [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect('https://example.devtunnels.ms/dashboard');
    }

    public function test_acceso_local_no_envia_formularios_ni_recursos_al_tunel(): void
    {
        $this->get('http://localhost:8000/login')->assertOk()
            ->assertSee('action="http://localhost:8000/login"', false)
            ->assertSee('src="http://localhost:8000/images/logo-delicias-dulces.webp"', false)
            ->assertDontSee('example.devtunnels.ms');
    }

    public function test_login_local_redirige_al_dashboard_local(): void
    {
        $usuario = User::factory()->create(['cargo' => 'Empleado']);
        $this->post('http://localhost:8000/login', ['username' => $usuario->username, 'password' => 'password'])
            ->assertRedirect('http://localhost:8000/dashboard');
    }

    public function test_host_reenviado_desconocido_no_modifica_el_destino(): void
    {
        $this->withHeader('X-Forwarded-Host', 'otro.example')->get('http://localhost:8000/login')
            ->assertOk()->assertSee('action="http://localhost:8000/login"', false)
            ->assertDontSee('otro.example');
    }

    public function test_host_publico_directo_usa_https(): void
    {
        $this->get('https://example.devtunnels.ms/login')->assertOk()
            ->assertSee('action="https://example.devtunnels.ms/login"', false);
    }
}
