<?php

namespace Tests\Feature;

use App\Services\ImagenProducto;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImagenProductoTest extends TestCase
{
    public function test_usa_original_si_no_hay_copia_y_no_muestra_archivos_ausentes(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/original.jpg', 'archivo original');
        $this->assertSame(asset('storage/productos/original.jpg'), ImagenProducto::url('productos/original.jpg'));
        $this->assertNull(ImagenProducto::url('productos/ausente.jpg'));
        $this->assertFalse(ImagenProducto::optimizar('productos/ausente.jpg'));
        $this->assertNull(ImagenProducto::url(null));
    }

    public function test_version_ligera_conserva_original_y_limita_dimensiones(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('La generación de WebP requiere GD.');
        }
        Storage::fake('public');
        $imagen = imagecreatetruecolor(1600, 1000);
        imagefill($imagen, 0, 0, imagecolorallocate($imagen, 160, 85, 45));
        ob_start();
        imagejpeg($imagen, null, 95);
        $original = ob_get_clean();
        imagedestroy($imagen);
        Storage::disk('public')->put('productos/prueba.jpg', $original);

        $this->assertTrue(ImagenProducto::optimizar('productos/prueba.jpg'));
        $ruta = ImagenProducto::rutaOptimizada('productos/prueba.jpg');
        $copia = Storage::disk('public')->get($ruta);
        $dimensiones = getimagesizefromstring($copia);
        $this->assertSame(960, $dimensiones[0]);
        $this->assertSame(600, $dimensiones[1]);
        $this->assertLessThan(strlen($original), strlen($copia));
        $this->assertSame($original, Storage::disk('public')->get('productos/prueba.jpg'));
        $this->assertSame(asset('storage/'.$ruta), ImagenProducto::url('productos/prueba.jpg'));
        $this->assertTrue(ImagenProducto::optimizar('productos/prueba.jpg'));
        $this->assertSame($copia, Storage::disk('public')->get($ruta));
    }

    public function test_eliminar_imagen_retira_original_y_copia(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('productos/prueba.jpg', 'original');
        Storage::disk('public')->put(ImagenProducto::rutaOptimizada('productos/prueba.jpg'), 'copia');
        ImagenProducto::eliminar('productos/prueba.jpg');
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
