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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo_pedido', ['Personalizado', 'Predefinido']);
            $table->enum('prioridad', ['Bajo', 'Normal', 'Alto'])->default('Normal');
            $table->date('fecha_pedido');
            $table->date('fecha_entrega');
            $table->text('descripcion_especificaciones')->nullable();
            $table->text('direccion_entrega');
            $table->string('telefono_contacto');
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia']);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('anticipo_recibido', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['Pendiente', 'En proceso', 'Completado', 'Cancelado'])->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
