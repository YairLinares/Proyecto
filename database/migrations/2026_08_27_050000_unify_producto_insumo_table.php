<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('producto_insumo') || ! Schema::hasTable('insumo_producto')) {
            return;
        }

        $registros = DB::table('producto_insumo')->get();

        foreach ($registros as $registro) {
            DB::table('insumo_producto')->updateOrInsert(
                [
                    'producto_id' => $registro->producto_id,
                    'insumo_id' => $registro->insumo_id,
                ],
                [
                    'cantidad_necesaria' => $registro->cantidad_necesaria,
                    'created_at' => $registro->created_at,
                    'updated_at' => $registro->updated_at,
                ]
            );
        }

        Schema::drop('producto_insumo');
    }

    public function down(): void
    {
        // The duplicate producto_insumo table is not restored.
    }
};
