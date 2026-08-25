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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('costo_produccion', 10, 2);
            $table->integer('stock_disponible')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('tiempo_preparacion_dias')->default(1);
            $table->enum('unidad_medida', ['Unidad', 'Kg', 'Gramos', 'Litros', 'Mililitros'])->default('Unidad');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
