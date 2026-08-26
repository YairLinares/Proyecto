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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('telefono_principal');
            $table->string('telefono_alternativo')->nullable();
            $table->text('direccion');
            $table->enum('tipo_cliente', ['Regular', 'Corporativo'])->default('Regular');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->decimal('total_compras', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
