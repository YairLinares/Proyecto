<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nombre')) {
                $table->string('nombre')->nullable()->after('name');
                $table->string('apellido')->nullable()->after('nombre');
                $table->string('telefono')->nullable()->after('apellido');
                $table->string('ciudad')->nullable()->after('telefono');
                $table->string('cargo')->default('Empleado')->after('ciudad');
            }
        });

        DB::table('users')
            ->whereNull('nombre')
            ->update([
                'nombre' => DB::raw('name'),
                'cargo' => 'Empleado',
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nombre')) {
                $table->dropColumn(['nombre', 'apellido', 'telefono', 'ciudad', 'cargo']);
            }
        });
    }
};
