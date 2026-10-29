<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminSessionLock
{
    protected const SLOT = 1;
    protected const SESSION_KEY = 'admin_access_token';
    public const MESSAGE = 'Hay una sesión de administrador reservada recientemente. Cierra la sesión anterior o espera 1 minuto sin actividad e intenta de nuevo.';

    public function acquire(Request $request): bool
    {
        $token = $request->session()->get(static::SESSION_KEY) ?? Str::random(64);
        $accepted = DB::transaction(function () use ($request, $token) {
            $lock = DB::table('admin_session_lock')->where('id', static::SLOT)->lockForUpdate()->first();
            $cutoff = now()->timestamp - min((int) config('session.lifetime'), (int) config('access.idle_minutes', 1)) * 60;
            $ownsLock = $lock->token === $token && $lock->user_id === $request->user()->id;

            // La sesión se guarda al terminar la respuesta: respetar un margen
            // para no quitar el cupo a un inicio de sesión aún en curso.
            $missingSession = false;
            if (config('session.driver') === 'database' && $lock->session_id
                && $lock->last_activity < now()->timestamp - 30 && ! $ownsLock) {
                $missingSession = ! DB::connection(config('session.connection'))
                    ->table(config('session.table', 'sessions'))
                    ->where('id', $lock->session_id)
                    ->where('user_id', $lock->user_id)
                    ->where('last_activity', '>', now()->timestamp - (int) config('session.lifetime') * 60)
                    ->exists();
            }

            if ($lock->token !== null && $lock->last_activity > $cutoff && ! $ownsLock && ! $missingSession) {
                return false;
            }

            DB::table('admin_session_lock')->where('id', static::SLOT)->update([
                'user_id' => $request->user()->id,
                'token' => $token,
                'last_activity' => now()->timestamp,
                'session_id' => $request->session()->getId(),
            ]);
            return true;
        });

        if ($accepted) {
            $request->session()->put(static::SESSION_KEY, $token);
        }
        return $accepted;
    }

    public function release(Request $request): void
    {
        if ($token = $request->session()->get(static::SESSION_KEY)) {
            DB::table('admin_session_lock')->where('id', static::SLOT)->where('token', $token)
                ->update(['user_id' => null, 'token' => null, 'last_activity' => 0, 'session_id' => null]);
            $request->session()->forget(static::SESSION_KEY);
        }
    }
}
