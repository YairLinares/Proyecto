<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_session_lock', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('token', 64)->nullable();
            $table->unsignedBigInteger('last_activity')->default(0);
        });
        DB::table('admin_session_lock')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_session_lock');
    }
};
