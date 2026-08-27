<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('cargo', ['Usuario', 'Vendedor', 'Repostero'])
            ->update(['cargo' => 'Empleado']);

        DB::table('users')
            ->where('cargo', 'Administradora')
            ->update(['cargo' => 'Administrador']);
    }

    public function down(): void
    {
        // Los roles anteriores no se pueden reconstruir de forma fiable.
    }
};
