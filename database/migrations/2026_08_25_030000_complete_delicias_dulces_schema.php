<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'nombre_completo')) {
                $table->string('nombre_completo')->after('id');
                $table->string('tipo_documento', 30)->after('nombre_completo');
                $table->string('numero_documento')->unique()->after('tipo_documento');
                $table->string('telefono_principal')->after('numero_documento');
                $table->string('telefono_alternativo')->nullable()->after('telefono_principal');
                $table->string('email')->after('telefono_alternativo');
                $table->string('ciudad')->after('email');
                $table->text('direccion')->after('ciudad');
                $table->enum('tipo_cliente', ['Regular', 'Corporativo'])->default('Regular')->after('direccion');
                $table->text('notas_preferencias')->nullable()->after('tipo_cliente');
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('notas_preferencias');
                $table->decimal('total_compras', 10, 2)->default(0)->after('estado');
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias', 'nombre')) {
                $table->string('nombre')->unique()->after('id');
                $table->string('slug')->unique()->after('nombre');
                $table->text('descripcion')->nullable()->after('slug');
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'categoria_id')) {
                $table->foreignId('categoria_id')->after('id')->constrained('categorias')->restrictOnDelete();
                $table->string('nombre')->unique()->after('categoria_id');
                $table->text('descripcion')->nullable()->after('nombre');
                $table->decimal('precio_venta', 10, 2)->after('descripcion');
                $table->decimal('costo_produccion', 10, 2)->after('precio_venta');
                $table->integer('stock_disponible')->default(0)->after('costo_produccion');
                $table->integer('stock_minimo')->default(0)->after('stock_disponible');
                $table->integer('tiempo_preparacion_dias')->default(1)->after('stock_minimo');
                $table->enum('unidad_medida', ['Unidad', 'Kg', 'Gramos', 'Litros', 'Mililitros'])->default('Unidad')->after('tiempo_preparacion_dias');
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('unidad_medida');
            }
        });

        Schema::table('insumos', function (Blueprint $table) {
            if (! Schema::hasColumn('insumos', 'nombre')) {
                $table->string('nombre')->unique()->after('id');
                $table->text('descripcion')->nullable()->after('nombre');
                $table->enum('unidad', ['Kg', 'Gramos', 'Litros', 'Mililitros', 'Unidad'])->default('Unidad')->after('descripcion');
                $table->decimal('stock_actual', 10, 2)->default(0)->after('unidad');
                $table->decimal('stock_minimo', 10, 2)->default(0)->after('stock_actual');
                $table->decimal('precio_unitario', 10, 2)->default(0)->after('stock_minimo');
                $table->string('proveedor')->after('precio_unitario');
                $table->enum('estado', ['Normal', 'Stock bajo', 'Agotado'])->default('Normal')->after('proveedor');
            }
        });

        Schema::table('pedidos', function (Blueprint $table) {
            if (! Schema::hasColumn('pedidos', 'numero_pedido')) {
                $table->string('numero_pedido')->unique()->after('id');
                $table->foreignId('cliente_id')->after('numero_pedido')->constrained('clientes')->restrictOnDelete();
                $table->foreignId('usuario_id')->nullable()->after('cliente_id')->constrained('users')->nullOnDelete();
                $table->enum('tipo_pedido', ['Personalizado', 'Predefinido'])->after('usuario_id');
                $table->enum('prioridad', ['Bajo', 'Normal', 'Alto'])->default('Normal')->after('tipo_pedido');
                $table->date('fecha_pedido')->after('prioridad');
                $table->date('fecha_entrega')->after('fecha_pedido');
                $table->text('descripcion_especificaciones')->nullable()->after('fecha_entrega');
                $table->text('direccion_entrega')->after('descripcion_especificaciones');
                $table->string('telefono_contacto')->after('direccion_entrega');
                $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia'])->after('telefono_contacto');
                $table->decimal('subtotal', 10, 2)->default(0)->after('metodo_pago');
                $table->decimal('anticipo_recibido', 10, 2)->default(0)->after('subtotal');
                $table->decimal('descuento', 10, 2)->default(0)->after('anticipo_recibido');
                $table->decimal('costo_envio', 10, 2)->default(0)->after('descuento');
                $table->decimal('total', 10, 2)->default(0)->after('costo_envio');
                $table->enum('estado', ['Pendiente', 'En proceso', 'Completado', 'Cancelado'])->default('Pendiente')->after('total');
            }
        });

        if (Schema::hasTable('detalle_pedidos') && ! Schema::hasTable('detalles_pedidos')) {
            Schema::rename('detalle_pedidos', 'detalles_pedidos');
        }

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

        Schema::table('detalles_pedidos', function (Blueprint $table) {
            if (! Schema::hasColumn('detalles_pedidos', 'pedido_id')) {
                $table->foreignId('pedido_id')->after('id')->constrained('pedidos')->cascadeOnDelete();
                $table->foreignId('producto_id')->after('pedido_id')->constrained('productos')->restrictOnDelete();
                $table->integer('cantidad')->after('producto_id');
                $table->decimal('precio_unitario', 10, 2)->after('cantidad');
                $table->decimal('subtotal', 10, 2)->default(0)->after('precio_unitario');
            }
        });

        Schema::table('pagos', function (Blueprint $table) {
            if (! Schema::hasColumn('pagos', 'pedido_id')) {
                $table->foreignId('pedido_id')->after('id')->constrained('pedidos')->cascadeOnDelete();
                $table->decimal('monto', 10, 2)->after('pedido_id');
                $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia'])->after('monto');
                $table->date('fecha_pago')->after('metodo_pago');
                $table->string('referencia')->nullable()->after('fecha_pago');
                $table->text('observacion')->nullable()->after('referencia');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insumo_producto');
    }
};
