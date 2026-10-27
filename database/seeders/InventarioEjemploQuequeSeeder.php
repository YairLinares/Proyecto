<?php

namespace Database\Seeders;

use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioEjemploQuequeSeeder extends Seeder
{
    private const MOTIVO = 'Carga de ejemplo para probar el inventario del queque de zanahoria';

    public function run(): void
    {
        // Valores ficticios de prueba; no representan precios de mercado.
        $datos = [
            ['nombre' => 'Harina de Trigo', 'unidad' => 'Kg', 'cantidad' => 2, 'precio' => 9, 'dias_vencimiento' => 90],
            ['nombre' => 'Azúcar', 'unidad' => 'Kg', 'cantidad' => 2, 'precio' => 8, 'dias_vencimiento' => 180],
            ['nombre' => 'Huevos', 'unidad' => 'Unidad', 'cantidad' => 12, 'precio' => 1, 'dias_vencimiento' => 14],
            ['nombre' => 'Mantequilla', 'unidad' => 'Kg', 'cantidad' => 0.5, 'precio' => 60, 'dias_vencimiento' => 30],
            ['nombre' => 'Zanahoria', 'unidad' => 'Kg', 'cantidad' => 1, 'precio' => 7, 'dias_vencimiento' => 10],
            ['nombre' => 'Polvo de hornear', 'unidad' => 'Gramos', 'cantidad' => 100, 'precio' => 0.10, 'dias_vencimiento' => 180],
        ];

        DB::transaction(function () use ($datos): void {
            foreach ($datos as $dato) {
                $insumo = Insumo::query()->where('nombre', $dato['nombre'])->lockForUpdate()->firstOrFail();

                if ($insumo->unidad !== $dato['unidad']) {
                    throw new \RuntimeException('La unidad de '.$dato['nombre'].' no coincide con la carga de ejemplo.');
                }

                if ($insumo->movimientos()->where('motivo', self::MOTIVO)->exists()) {
                    continue;
                }

                if ((float) $insumo->precio_unitario === 0.0) {
                    $insumo->update(['precio_unitario' => $dato['precio']]);
                }

                MovimientoInsumo::registrar(
                    $insumo,
                    'Entrada',
                    $dato['cantidad'],
                    self::MOTIVO,
                    codigoLote: 'DEMO-QUEQUE-'.$insumo->id,
                    fechaVencimiento: today()->addDays($dato['dias_vencimiento'])->toDateString(),
                );
            }
        });
    }
}
