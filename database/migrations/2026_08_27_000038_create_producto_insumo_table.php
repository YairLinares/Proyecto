<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('insumo_producto')) {
            Schema::create('insumo_producto', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
                $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
                $table->decimal('cantidad_necesaria', 10, 2)->default(0);
                $table->timestamps();
                $table->unique(['producto_id', 'insumo_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The insumo_producto table is shared with the initial migration.
    }
};
