<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tipo_documento', 'numero_documento', 'email'] as $column) {
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
            $table->string('tipo_documento', 30)->nullable()->after('nombre_completo');
            $table->string('numero_documento')->nullable()->unique()->after('tipo_documento');
            $table->string('email')->nullable()->after('telefono_alternativo');
        });
    }
};
