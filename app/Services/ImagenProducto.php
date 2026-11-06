<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImagenProducto
{
    public static function rutaOptimizada(string $ruta): string
    {
        return 'productos/optimizadas/'.hash('sha256', $ruta).'.webp';
    }

    public static function url(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }
        $disco = Storage::disk('public');
        $optimizada = self::rutaOptimizada($ruta);
        if ($disco->exists($optimizada)) {
            return asset('storage/'.$optimizada);
        }

        return $disco->exists($ruta) ? asset('storage/'.$ruta) : null;
    }

    public static function optimizar(string $ruta): bool
    {
        if (! function_exists('imagewebp')) {
            return false;
        }
        $disco = Storage::disk('public');
        $destino = self::rutaOptimizada($ruta);
        if ($disco->exists($destino)) {
            return true;
        }
        if (! $disco->exists($ruta)) {
            return false;
        }
        $origen = $miniatura = null;
        $nivelBuffer = ob_get_level();
        try {
            $contenido = $disco->get($ruta);
            $dimensiones = @getimagesizefromstring($contenido);
            if (! $dimensiones || $dimensiones[0] * $dimensiones[1] > 20000000) {
                return false;
            }
            $origen = @imagecreatefromstring($contenido);
            if (! $origen) {
                return false;
            }
            $escala = min(1, 960 / max($dimensiones[0], $dimensiones[1]));
            $ancho = max(1, (int) round($dimensiones[0] * $escala));
            $alto = max(1, (int) round($dimensiones[1] * $escala));
            $miniatura = imagecreatetruecolor($ancho, $alto);
            imagealphablending($miniatura, false);
            imagesavealpha($miniatura, true);
            imagecopyresampled($miniatura, $origen, 0, 0, 0, 0, $ancho, $alto, $dimensiones[0], $dimensiones[1]);
            ob_start();
            $convertida = imagewebp($miniatura, null, 82);
            $webp = ob_get_clean();

            return $convertida && $webp !== '' && $disco->put($destino, $webp);
        } catch (\Throwable) {
            // La versión original sigue disponible si no se puede generar la copia.
            return false;
        } finally {
            while (ob_get_level() > $nivelBuffer) {
                ob_end_clean();
            }
            if ($origen) {
                imagedestroy($origen);
            }
            if ($miniatura) {
                imagedestroy($miniatura);
            }
        }
    }

    public static function eliminar(string $ruta): void
    {
        Storage::disk('public')->delete([$ruta, self::rutaOptimizada($ruta)]);
    }
}
