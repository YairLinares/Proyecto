<?php

declare(strict_types=1);

$zip = new ZipArchive();
$file = __DIR__ . '/../docs/Triptico_Delicias_Dulces.docx';

if ($zip->open($file) !== true) {
    throw new RuntimeException('No se pudo abrir el archivo DOCX.');
}

$required = ['[Content_Types].xml', '_rels/.rels', 'word/document.xml', 'word/styles.xml'];
foreach ($required as $entry) {
    if ($zip->getFromName($entry) === false) {
        throw new RuntimeException("Falta {$entry} en el DOCX.");
    }
}

$document = new DOMDocument();
if (! $document->loadXML($zip->getFromName('word/document.xml'))) {
    throw new RuntimeException('El XML principal del DOCX no es valido.');
}

$zip->close();
echo "DOCX structure OK\n";
