<?php

namespace Database\Seeders;

use App\Models\Insumo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsumosQuequeZanahoriaSeeder extends Seeder
{
    public function run(): void
    {
        $insumos = [
            ['Harina de Trigo', 'Kg', 'Harina para la masa del queque de zanahoria.'],
            ['Azúcar', 'Kg', 'Azúcar para endulzar la masa.'],
            ['Huevos', 'Unidad', 'Huevos para dar estructura a la masa.'],
            ['Mantequilla', 'Kg', 'Mantequilla para la masa del queque.'],
            ['Zanahoria', 'Kg', 'Zanahoria fresca para lavar y rallar antes de preparar el queque.'],
            ['Polvo de hornear', 'Gramos', 'Leudante para que el queque suba durante el horneado.'],
            ['Canela en polvo', 'Gramos', 'Especia para dar sabor al queque de zanahoria.'],
            ['Esencia de vainilla', 'Mililitros', 'Aromatizante para la masa del queque.'],
        ];

        DB::transaction(function () use ($insumos): void {
            foreach ($insumos as [$nombre, $unidad, $descripcion]) {
                Insumo::firstOrCreate(
                    ['nombre' => $nombre],
                    [
                        'descripcion' => $descripcion,
                        'unidad' => $unidad,
                        'stock_actual' => 0,
                        'stock_minimo' => 0,
                        'precio_unitario' => 0,
                        'estado' => 'Agotado',
                    ],
                );
            }
        });
    }
}
