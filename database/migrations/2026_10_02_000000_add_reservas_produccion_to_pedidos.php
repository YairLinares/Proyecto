<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('insumos_reservados_at')->nullable();
            $table->timestamp('produccion_iniciada_at')->nullable();
        });

        Schema::create('reservas_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
            $table->unique(['pedido_id', 'insumo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas_insumo');
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['insumos_reservados_at', 'produccion_iniciada_at']);
        });
    }
};
