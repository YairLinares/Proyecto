<?php

namespace Tests\Feature;

use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\User;
use Database\Seeders\InsumosQuequeZanahoriaSeeder;
use Database\Seeders\InventarioEjemploQuequeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LotesInsumoTest extends TestCase
{
    use RefreshDatabase;

    private function insumo(): Insumo
    {
        return Insumo::create(['nombre' => 'Harina', 'unidad' => 'Kg', 'stock_actual' => 0, 'stock_minimo' => 1, 'precio_unitario' => 10]);
    }

    private function entrada(Insumo $insumo, string $codigo, ?string $fecha, float $cantidad = 5): MovimientoInsumo
    {
        return MovimientoInsumo::registrar($insumo, 'Entrada', $cantidad, 'Compra', codigoLote: $codigo, fechaVencimiento: $fecha);
    }

    public function test_consumo_prioriza_vencimiento_y_excluye_vencidos(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'VENCIDO', today()->subDay()->toDateString());
        $this->entrada($i, 'TARDIO', today()->addDays(10)->toDateString());
        $this->entrada($i, 'PRONTO', today()->toDateString());
        $this->entrada($i, 'SIN-FECHA', null);
        $salida = MovimientoInsumo::registrar($i, 'Salida', 7, 'Consumo');
        $this->assertEquals(5, $salida->lotes()->where('codigo', 'PRONTO')->first()->pivot->cantidad);
        $this->assertEquals(2, $salida->lotes()->where('codigo', 'TARDIO')->first()->pivot->cantidad);
        $this->assertEquals(13, $i->fresh()->stock_actual);
        $this->assertEquals(8, $i->stockUtilizable());
        $this->assertEquals(13, $i->lotes()->sum('cantidad'));
    }

    public function test_salida_insuficiente_no_modifica_stock_ni_historial(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'VENCIDO', today()->subDay()->toDateString());
        try {
            MovimientoInsumo::registrar($i, 'Salida', 2, 'Consumo');
            $this->fail('Debe rechazar el consumo vencido');
        } catch (ValidationException $e) {
            $this->assertEquals(5, $i->fresh()->stock_actual);
            $this->assertEquals(5, $i->lotes()->sum('cantidad'));
            $this->assertEquals(1, $i->movimientos()->count());
        }
    }

    public function test_devolucion_restaura_lotes_originales_y_merma_retira_vencidos(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'A', today()->toDateString());
        $salida = MovimientoInsumo::registrar($i, 'Salida', 3, 'Consumo');
        $lote = $i->lotes()->first();
        $lote->update(['fecha_vencimiento' => today()->subDay()]);
        MovimientoInsumo::registrar($i, 'Entrada', 3, 'Devolución', movimientoOrigenId: $salida->id);
        $this->assertEquals(5, $lote->fresh()->cantidad);
        $this->assertEquals(0, $i->stockUtilizable());
        MovimientoInsumo::registrar($i, 'Salida', 5, 'Merma por caducidad', loteId: $lote->id);
        $this->assertEquals(0, $i->fresh()->stock_actual);
        $this->assertEquals(0, $lote->fresh()->cantidad);
    }

    public function test_no_permite_retirar_lote_de_otro_insumo(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'A', null);
        $otro = Insumo::create(['nombre' => 'Azucar', 'unidad' => 'Kg', 'stock_actual' => 5]);
        $this->expectException(ValidationException::class);
        MovimientoInsumo::registrar($i, 'Salida', 1, 'Merma', loteId: $otro->lotes()->first()->id);
    }

    public function test_pantallas_y_actualizacion_de_fecha(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'PRUEBA', null);
        $user = User::factory()->create(['cargo' => 'Administrador']);
        $this->actingAs($user);
        $this->get(route('insumos.index'))->assertOk()
            ->assertSee('Precio unitario (Bs)')
            ->assertSee('Bs 10,00 / Kg')
            ->assertSee('Bs 50,00');
        $this->get(route('insumos.show', $i))->assertOk()->assertSee('PRUEBA')
            ->assertSee('Valor del stock físico:')
            ->assertSee('Bs 50,00');
        $this->get(route('insumos.create'))->assertOk()->assertSee('Stock físico');
        $this->get(route('insumos.edit', $i))->assertOk()->assertSee('Valor del stock físico');
        $this->get(route('insumos.movimientos.create', $i))->assertOk();
        $this->patch(route('insumos.lotes.update', [$i, $i->lotes()->first()->id]), ['fecha_vencimiento' => '2027-01-01'])->assertRedirect();
        $this->assertEquals('2027-01-01', $i->lotes()->first()->fecha_vencimiento->format('Y-m-d'));
    }

    public function test_carga_de_ejemplo_conserva_stock_previo_y_no_se_duplica(): void
    {
        $this->seed(InsumosQuequeZanahoriaSeeder::class);
        $harina = Insumo::where('nombre', 'Harina de Trigo')->firstOrFail();
        $this->entrada($harina, 'LOTE-EXISTENTE', null, 0.87);

        $this->seed(InventarioEjemploQuequeSeeder::class);
        $this->seed(InventarioEjemploQuequeSeeder::class);

        $this->assertEquals(2.87, $harina->fresh()->stock_actual);
        $this->assertEquals(9, $harina->fresh()->precio_unitario);
        $this->assertEquals(0.87, $harina->lotes()->where('codigo', 'LOTE-EXISTENTE')->firstOrFail()->cantidad);
        $this->assertEquals(2, $harina->movimientos()->count());

        $azucar = Insumo::where('nombre', 'Azúcar')->firstOrFail();
        $this->assertEquals(2, $azucar->stock_actual);
        $this->assertEquals(8, $azucar->precio_unitario);
        $this->assertEquals(1, $azucar->movimientos()->count());
        $this->assertEquals(12, Insumo::where('nombre', 'Huevos')->firstOrFail()->stock_actual);
        $this->assertEquals(100, Insumo::where('nombre', 'Polvo de hornear')->firstOrFail()->stock_actual);
    }

    public function test_administrador_recibe_alertas_de_lotes_por_vencer_y_vencidos(): void
    {
        $insumo = $this->insumo();
        $this->entrada($insumo, 'HOY', today()->toDateString());
        $this->entrada($insumo, 'PROXIMO', today()->addDays(7)->toDateString());
        $this->entrada($insumo, 'VENCIDO', today()->subDay()->toDateString());
        $this->entrada($insumo, 'FUTURO', today()->addDays(8)->toDateString());
        $insumo->lotes()->create(['codigo' => 'SIN-STOCK', 'fecha_vencimiento' => today(), 'cantidad' => 0]);

        $admin = User::factory()->create(['cargo' => 'Administrador']);
        $this->actingAs($admin)->get(route('profile.show'))->assertOk()
            ->assertSee('Por vencer: Harina (lote HOY)')
            ->assertSee('Por vencer: Harina (lote PROXIMO)')
            ->assertSee('Lote vencido: Harina (VENCIDO)')
            ->assertSee('Vence el '.today()->addDays(7)->format('d/m/Y'))
            ->assertDontSee('FUTURO')
            ->assertDontSee('SIN-STOCK');

        $empleado = User::factory()->create(['cargo' => 'Empleado']);
        $this->actingAs($empleado)->get(route('profile.show'))->assertOk()
            ->assertDontSee('Por vencer: Harina')
            ->assertDontSee('Lote vencido: Harina');
    }

    public function test_migracion_conserva_stock_preexistente(): void
    {
        $migration = require database_path('migrations/2026_09_15_000000_create_lotes_insumo.php');
        $migration->down();
        $id = DB::table('insumos')->insertGetId(['nombre' => 'Anterior', 'unidad' => 'Kg', 'stock_actual' => 12.5]);
        $migration->up();
        $i = Insumo::findOrFail($id);
        $this->assertEquals(12.5, $i->stock_actual);
        $this->assertEquals(12.5, $i->stockUtilizable());
        $this->assertNull($i->lotes()->first()->fecha_vencimiento);
    }

    public function test_ajustes_mantienen_stock_y_lotes_consistentes(): void
    {
        $i = $this->insumo();
        $this->entrada($i, 'A', null);
        MovimientoInsumo::registrar($i, 'Ajuste', 8, 'Conteo', stockAjustado: 8);
        MovimientoInsumo::registrar($i, 'Ajuste', 2, 'Conteo', stockAjustado: 2);
        $this->assertEquals(2, $i->fresh()->stock_actual);
        $this->assertEquals(2, $i->lotes()->sum('cantidad'));
    }
}
