<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PublicTunnelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://example.devtunnels.ms']);
        (new AppServiceProvider($this->app))->boot();
    }

    public function test_login_form_and_logo_use_public_url_when_host_is_localhost(): void
    {
        $this->get('http://localhost:8000/login')
            ->assertOk()
            ->assertSee('action="https://example.devtunnels.ms/login"', false)
            ->assertSee('src="https://example.devtunnels.ms/images/logo-delicias-dulces.webp"', false)
            ->assertDontSee('http://localhost:8000', false);
    }

    public function test_successful_login_redirects_to_public_dashboard(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturn(true);

        $this->post('http://localhost:8000/login', [
            'email' => 'test@example.com',
            'password' => 'test-password',
        ])->assertRedirect('https://example.devtunnels.ms/dashboard');
    }
}
