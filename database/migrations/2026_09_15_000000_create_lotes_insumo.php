<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
            $table->string('codigo', 100);
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['insumo_id', 'codigo']);
        });
        Schema::create('lote_movimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_insumo_id')->constrained('lotes_insumo')->restrictOnDelete();
            $table->foreignId('movimiento_insumo_id')->constrained('movimientos_insumo')->cascadeOnDelete();
            $table->decimal('cantidad', 10, 2);
        });
        DB::table('insumos')->orderBy('id')->chunkById(200, function ($insumos) {
            foreach ($insumos as $insumo) {
                if ($insumo->stock_actual > 0) {
                    DB::table('lotes_insumo')->insert([
                        'insumo_id' => $insumo->id, 'codigo' => 'INICIAL-'.$insumo->id,
                        'cantidad' => $insumo->stock_actual, 'fecha_vencimiento' => null,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_movimiento');
        Schema::dropIfExists('lotes_insumo');
    }
};
