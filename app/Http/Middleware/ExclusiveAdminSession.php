<?php

namespace App\Http\Middleware;

use App\Services\AdminSessionLock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExclusiveAdminSession
{
    public function handle(Request $request, Closure $next)
    {
        $lock = app(AdminSessionLock::class);
        if ($request->user()?->esAdministrador()) {
            if (! $lock->acquire($request)) {
                Auth::guard()->logoutCurrentDevice();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => AdminSessionLock::MESSAGE]);
            }
        } else {
            $lock->release($request);
        }

        return $next($request);
    }
}
