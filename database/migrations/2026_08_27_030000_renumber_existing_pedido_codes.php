<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pedidos = DB::table('pedidos')->orderBy('id')->get(['id']);

        foreach ($pedidos as $pedido) {
            DB::table('pedidos')->where('id', $pedido->id)->update([
                'numero_pedido' => 'TMP-' . $pedido->id,
            ]);
        }

        foreach ($pedidos as $indice => $pedido) {
            DB::table('pedidos')->where('id', $pedido->id)->update([
                'numero_pedido' => 'PED-' . str_pad((string) ($indice + 1), 3, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down(): void
    {
        // Los códigos anteriores incluían la fecha y no se pueden reconstruir de forma fiable.
    }
};
