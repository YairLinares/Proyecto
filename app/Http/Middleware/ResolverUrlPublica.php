<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResolverUrlPublica
{
    public function handle(Request $request, Closure $next): Response
    {
        $urlPublica = (string) config('app.url');
        $hostPublico = strtolower((string) parse_url($urlPublica, PHP_URL_HOST));
        $hostsReenviados = array_map(
            fn ($host) => strtolower(trim($host)),
            explode(',', (string) $request->header('X-Forwarded-Host', '')),
        );
        $esAccesoPublico = str_starts_with($urlPublica, 'https://') && $hostPublico !== ''
            && (strtolower($request->getHost()) === $hostPublico || in_array($hostPublico, $hostsReenviados, true));

        // El acceso local conserva su origen; solo el túnel usa la URL pública configurada.
        URL::forceRootUrl($esAccesoPublico ? $urlPublica : null);
        URL::forceScheme($esAccesoPublico ? 'https' : null);

        return $next($request);
    }
}
