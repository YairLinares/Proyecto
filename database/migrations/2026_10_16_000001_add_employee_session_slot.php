<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('admin_session_lock')->insert(['id' => 2]);
    }

    public function down(): void
    {
        DB::table('admin_session_lock')->where('id', 2)->delete();
    }
};
