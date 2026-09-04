<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministradorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->esAdministrador()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}
