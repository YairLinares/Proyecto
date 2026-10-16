<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSessionLock
{
    public const MESSAGE = 'El sistema ya está siendo utilizado por un administrador. Intenta nuevamente cuando cierre su sesión.';

    public function acquire(Request $request): bool
    {
        $token = $request->session()->get('admin_access_token') ?? Str::random(64);
        $accepted = DB::transaction(function () use ($request, $token) {
            $lock = DB::table('admin_session_lock')->where('id', 1)->lockForUpdate()->first();
            $cutoff = now()->timestamp - (int) config('session.lifetime') * 60;
            $ownsLock = $lock->token === $token && $lock->user_id === $request->user()->id;

            if ($lock->token !== null && $lock->last_activity > $cutoff && ! $ownsLock) {
                return false;
            }

            DB::table('admin_session_lock')->where('id', 1)->update([
                'user_id' => $request->user()->id,
                'token' => $token,
                'last_activity' => now()->timestamp,
            ]);
            return true;
        });

        if ($accepted) {
            $request->session()->put('admin_access_token', $token);
        }
        return $accepted;
    }

    public function release(Request $request): void
    {
        if ($token = $request->session()->get('admin_access_token')) {
            DB::table('admin_session_lock')->where('id', 1)->where('token', $token)
                ->update(['user_id' => null, 'token' => null, 'last_activity' => 0]);
            $request->session()->forget('admin_access_token');
        }
    }
}
