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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->enum('unidad', ['Kg', 'Gramos', 'Litros', 'Mililitros', 'Unidad'])->default('Unidad');
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->string('proveedor');
            $table->enum('estado', ['Normal', 'Stock bajo', 'Agotado'])->default('Normal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
