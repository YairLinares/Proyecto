<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExclusiveEmployeeSessionTest extends TestCase
{
    use RefreshDatabase;

    private function occupy(User $admin, int $age = 0): void
    {
        DB::table('admin_session_lock')->where('id', 2)->update([
            'user_id' => $admin->id, 'token' => 'another-device',
            'last_activity' => now()->timestamp - $age,
        ]);
    }

    public function test_first_employee_can_login_and_logout_releases_access(): void
    {
        $admin = User::factory()->create(['cargo' => 'Empleado']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('admin_session_lock', ['id' => 2, 'user_id' => $admin->id]);
        $this->get('/perfil')->assertOk();
        $this->post('/logout')->assertRedirect();
        $this->assertDatabaseHas('admin_session_lock', ['id' => 2, 'token' => null]);
    }

    public function test_abandoned_slot_expires_after_five_minutes(): void
    {
        $employee = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($employee, 301);
        $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($employee);
    }

    public function test_missing_database_session_does_not_block_login(): void
    {
        config(['session.driver' => 'database']);
        $employee = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($employee, 60);
        DB::table('admin_session_lock')->where('id', 2)->update(['session_id' => 'deleted-session']);
        $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_login_in_progress_keeps_its_slot_before_session_is_saved(): void
    {
        config(['session.driver' => 'database']);
        $employee = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($employee, 10);
        DB::table('admin_session_lock')->where('id', 2)->update(['session_id' => 'pending-session']);
        $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_second_employee_is_blocked_without_displacing_first(): void
    {
        $first = User::factory()->create(['cargo' => 'Empleado']);
        $second = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($first);
        $this->post('/login', ['email' => $second->email, 'password' => 'password'])
            ->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseHas('admin_session_lock', ['token' => 'another-device', 'user_id' => $first->id]);
    }

    public function test_same_account_cannot_login_from_another_session(): void
    {
        $admin = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($admin);
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_login_while_employee_is_active(): void
    {
        $this->occupy(User::factory()->create(['cargo' => 'Empleado']));
        $employee = User::factory()->create(['cargo' => 'Administrador']);
        $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($employee);
        $this->post('/logout');
        $this->assertDatabaseHas('admin_session_lock', ['token' => 'another-device']);
    }

    public function test_inactive_reservation_can_be_replaced_and_old_session_is_blocked(): void
    {
        $first = User::factory()->create(['cargo' => 'Empleado']);
        $second = User::factory()->create(['cargo' => 'Empleado']);
        $this->occupy($first, (int) config('session.lifetime') * 60 + 1);
        $this->post('/login', ['email' => $second->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($first)->withSession(['employee_access_token' => 'another-device'])
            ->get('/perfil')->assertRedirect(route('login'));
        $this->assertDatabaseHas('admin_session_lock', ['user_id' => $second->id]);
    }
}
