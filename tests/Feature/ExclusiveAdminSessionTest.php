<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExclusiveAdminSessionTest extends TestCase
{
    use RefreshDatabase;

    private function occupy(User $admin, int $age = 0): void
    {
        DB::table('admin_session_lock')->where('id', 1)->update([
            'user_id' => $admin->id, 'token' => 'another-device',
            'last_activity' => now()->timestamp - $age,
        ]);
    }

    public function test_first_admin_can_login_and_logout_releases_access(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $this->post('/login', ['username' => $admin->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('admin_session_lock', ['id' => 1, 'user_id' => $admin->id]);
        $this->get('/perfil')->assertOk();
        $this->post('/logout')->assertRedirect();
        $this->assertDatabaseHas('admin_session_lock', ['id' => 1, 'token' => null]);
    }

    public function test_second_admin_is_blocked_without_displacing_first(): void
    {
        $first = User::factory()->create(['cargo' => 'Administrador']);
        $second = User::factory()->create(['cargo' => 'Administrador']);
        $this->occupy($first);
        $this->post('/login', ['username' => $second->username, 'password' => 'password'])
            ->assertRedirect(route('login'))->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertDatabaseHas('admin_session_lock', ['token' => 'another-device', 'user_id' => $first->id]);
    }

    public function test_same_account_cannot_login_from_another_session(): void
    {
        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $this->occupy($admin);
        $this->post('/login', ['username' => $admin->username, 'password' => 'password'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_employee_can_login_while_admin_is_active(): void
    {
        $this->occupy(User::factory()->create(['cargo' => 'Administrador']));
        $employee = User::factory()->create(['cargo' => 'Empleado']);
        $this->post('/login', ['username' => $employee->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($employee);
        $this->post('/logout');
        $this->assertDatabaseHas('admin_session_lock', ['token' => 'another-device']);
    }

    public function test_inactive_reservation_can_be_replaced_and_old_session_is_blocked(): void
    {
        $first = User::factory()->create(['cargo' => 'Administrador']);
        $second = User::factory()->create(['cargo' => 'Administrador']);
        $this->occupy($first, (int) config('session.lifetime') * 60 + 1);
        $this->post('/login', ['username' => $second->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($first)->withSession(['admin_access_token' => 'another-device'])
            ->get('/perfil')->assertRedirect(route('login'));
        $this->assertDatabaseHas('admin_session_lock', ['user_id' => $second->id]);
    }
}
