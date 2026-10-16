<?php

namespace App\Http\Middleware;

use App\Services\AdminSessionLock;
use App\Services\EmployeeSessionLock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExclusiveAdminSession
{
    public function handle(Request $request, Closure $next)
    {
        $admin = $request->user()->esAdministrador();
        $lock = app($admin ? AdminSessionLock::class : EmployeeSessionLock::class);
        app($admin ? EmployeeSessionLock::class : AdminSessionLock::class)->release($request);
            if (! $lock->acquire($request)) {
                Auth::guard()->logoutCurrentDevice();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => $lock::MESSAGE]);
            }

        return $next($request);
    }
}
