<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['ciudad', 'notas_preferencias'] as $column) {
            if (Schema::hasColumn('clientes', $column)) {
                Schema::table('clientes', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('ciudad')->nullable()->after('telefono_alternativo');
            $table->text('notas_preferencias')->nullable()->after('tipo_cliente');
        });
    }
};
