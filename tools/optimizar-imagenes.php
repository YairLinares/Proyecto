<?php

// Genera versiones para web sin modificar los originales.
// Ejecutar con: php -d extension=gd tools/optimizar-imagenes.php
$imagenes = [
    'logo-delicias-dulces' => 400,
    'ilustraciones/queque-rebanado' => 600,
];

foreach ($imagenes as $nombre => $limite) {
    $base = __DIR__.'/../public/images/'.$nombre;
    $origen = imagecreatefrompng($base.'.png');
    $escala = min(1, $limite / max(imagesx($origen), imagesy($origen)));
    $ancho = (int) round(imagesx($origen) * $escala);
    $alto = (int) round(imagesy($origen) * $escala);
    $destino = imagecreatetruecolor($ancho, $alto);
    imagealphablending($destino, false);
    imagesavealpha($destino, true);
    imagecopyresampled($destino, $origen, 0, 0, 0, 0, $ancho, $alto, imagesx($origen), imagesy($origen));
    if (! imagewebp($destino, $base.'.webp', 85)) {
        throw new RuntimeException('No se pudo convertir '.$nombre);
    }
    echo $nombre.': '.filesize($base.'.png').' -> '.filesize($base.'.webp')." bytes\n";
    imagedestroy($origen);
    imagedestroy($destino);
}
