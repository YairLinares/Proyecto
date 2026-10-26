<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
        });

        DB::table('users')->orderBy('id')->each(function (object $user): void {
            $base = Str::lower(Str::slug(strstr($user->email, '@', true) ?: $user->name, '_')) ?: 'usuario';
            $base = Str::substr($base, 0, 24);
            $username = $base;
            $suffix = 1;

            while (DB::table('users')->where('username', $username)->exists()) {
                $username = Str::substr($base, 0, 30 - strlen((string) $suffix) - 1).'-'.$suffix++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
