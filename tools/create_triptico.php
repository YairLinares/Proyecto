<?php

declare(strict_types=1);

$outputDirectory = __DIR__ . '/../docs';
$outputFile = $outputDirectory . '/Triptico_Delicias_Dulces.docx';

if (! is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}

function xmlText(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function run(string $text, int $size = 20, string $color = '15233D', bool $bold = false): string
{
    $boldTag = $bold ? '<w:b/>' : '';

    return '<w:r><w:rPr>' . $boldTag . '<w:color w:val="' . $color . '"/>'
        . '<w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/>'
        . '<w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/></w:rPr>'
        . '<w:t xml:space="preserve">' . xmlText($text) . '</w:t></w:r>';
}

function paragraph(string $text = '', int $size = 20, string $color = '34435B', bool $bold = false, string $align = 'left', int $after = 90): string
{
    $alignment = $align === 'center' ? '<w:jc w:val="center"/>' : '';

    return '<w:p><w:pPr>' . $alignment . '<w:spacing w:after="' . $after . '" w:line="260" w:lineRule="auto"/>'
        . '</w:pPr>' . run($text, $size, $color, $bold) . '</w:p>';
}

function panel(array $paragraphs, string $fill = 'FFFFFF'): string
{
    $content = '';
    foreach ($paragraphs as $item) {
        $content .= paragraph(
            $item['text'],
            $item['size'] ?? 20,
            $item['color'] ?? '34435B',
            $item['bold'] ?? false,
            $item['align'] ?? 'left',
            $item['after'] ?? 90
        );
    }

    return '<w:tc><w:tcPr><w:tcW w:w="4920" w:type="dxa"/><w:shd w:fill="' . $fill . '"/>'
        . '<w:tcMar><w:top w:w="260" w:type="dxa"/><w:start w:w="250" w:type="dxa"/>'
        . '<w:bottom w:w="260" w:type="dxa"/><w:end w:w="250" w:type="dxa"/></w:tcMar>'
        . '<w:tcBorders><w:top w:val="single" w:sz="6" w:color="E8D8DE"/>'
        . '<w:left w:val="single" w:sz="6" w:color="E8D8DE"/>'
        . '<w:bottom w:val="single" w:sz="6" w:color="E8D8DE"/>'
        . '<w:right w:val="single" w:sz="6" w:color="E8D8DE"/></w:tcBorders></w:tcPr>'
        . $content . '</w:tc>';
}

function tripticoPage(array $panels): string
{
    return '<w:tbl><w:tblPr><w:tblW w:w="14760" w:type="dxa"/><w:tblLayout w:type="fixed"/>'
        . '<w:tblBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/>'
        . '<w:insideH w:val="nil"/><w:insideV w:val="nil"/></w:tblBorders></w:tblPr>'
        . '<w:tblGrid><w:gridCol w:w="4920"/><w:gridCol w:w="4920"/><w:gridCol w:w="4920"/></w:tblGrid>'
        . '<w:tr><w:trPr><w:trHeight w:val="9800" w:hRule="atLeast"/></w:trPr>'
        . panel($panels[0]['content'], $panels[0]['fill'])
        . panel($panels[1]['content'], $panels[1]['fill'])
        . panel($panels[2]['content'], $panels[2]['fill'])
        . '</w:tr></w:tbl>';
}

$outside = tripticoPage([
    [
        'fill' => 'FFF8FC',
        'content' => [
            ['text' => 'DELICIAS DULCES', 'size' => 30, 'color' => 'C7436F', 'bold' => true, 'align' => 'center', 'after' => 180],
            ['text' => 'Sistema web para pasteleria', 'size' => 20, 'color' => '5A3D4F', 'bold' => true, 'align' => 'center', 'after' => 220],
            ['text' => 'Organiza pedidos, clientes, sabores, productos e ingredientes desde un solo lugar.', 'size' => 18, 'color' => '52617A', 'align' => 'center', 'after' => 260],
            ['text' => 'Tecnologias del proyecto', 'size' => 18, 'color' => '15233D', 'bold' => true, 'after' => 100],
            ['text' => 'Laravel - PHP - MySQL - XAMPP', 'size' => 17, 'color' => '52617A', 'after' => 240],
            ['text' => 'Proyecto academico', 'size' => 16, 'color' => '8794A9', 'align' => 'center', 'after' => 0],
        ],
    ],
    [
        'fill' => 'FFFDF7',
        'content' => [
            ['text' => 'Que puedes gestionar?', 'size' => 25, 'color' => '15233D', 'bold' => true, 'after' => 180],
            ['text' => 'Clientes', 'size' => 19, 'color' => 'C7436F', 'bold' => true, 'after' => 35],
            ['text' => 'Registra datos de contacto y consulta sus pedidos.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Pedidos', 'size' => 19, 'color' => 'C7436F', 'bold' => true, 'after' => 35],
            ['text' => 'Crea pedidos, calcula el total y controla el estado de entrega.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Productos y sabores', 'size' => 19, 'color' => 'C7436F', 'bold' => true, 'after' => 35],
            ['text' => 'Organiza el catalogo de queques y sus recetas.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Insumos', 'size' => 19, 'color' => 'C7436F', 'bold' => true, 'after' => 35],
            ['text' => 'Controla existencias, costos y alertas de stock.', 'size' => 17, 'color' => '52617A', 'after' => 0],
        ],
    ],
    [
        'fill' => 'FDEEF4',
        'content' => [
            ['text' => 'DELICIAS', 'size' => 38, 'color' => 'C7436F', 'bold' => true, 'align' => 'center', 'after' => 0],
            ['text' => 'DULCES', 'size' => 38, 'color' => '5A3D4F', 'bold' => true, 'align' => 'center', 'after' => 210],
            ['text' => 'Gestion simple para una pasteleria organizada.', 'size' => 22, 'color' => '15233D', 'bold' => true, 'align' => 'center', 'after' => 220],
            ['text' => 'Organiza pedidos', 'size' => 19, 'color' => '52617A', 'align' => 'center', 'after' => 80],
            ['text' => 'Controla ingredientes', 'size' => 19, 'color' => '52617A', 'align' => 'center', 'after' => 80],
            ['text' => 'Conoce lo que mas se vende', 'size' => 19, 'color' => '52617A', 'align' => 'center', 'after' => 280],
            ['text' => 'Sistema de gestion de pedidos e inventario', 'size' => 16, 'color' => '8794A9', 'align' => 'center', 'after' => 0],
        ],
    ],
]);

$inside = tripticoPage([
    [
        'fill' => 'FFFFFF',
        'content' => [
            ['text' => 'FUNCIONALIDAD', 'size' => 27, 'color' => 'C7436F', 'bold' => true, 'after' => 180],
            ['text' => '1. Registrar informacion', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Permite crear clientes, sabores, productos e insumos.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => '2. Configurar recetas', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Cada producto indica los ingredientes y cantidades que necesita.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => '3. Registrar pedidos', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Al guardar un pedido, calcula el total y descuenta los insumos de la receta.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => '4. Consultar dashboard', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Muestra pedidos, ventas, alertas e informacion para tomar decisiones.', 'size' => 17, 'color' => '52617A', 'after' => 0],
        ],
    ],
    [
        'fill' => 'FFF8FC',
        'content' => [
            ['text' => 'BENEFICIOS', 'size' => 27, 'color' => 'C7436F', 'bold' => true, 'after' => 180],
            ['text' => 'Menos errores manuales', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Centraliza la informacion y evita apuntes separados.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Control de inventario', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Muestra el stock disponible y alerta cuando un insumo esta por terminarse.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Costos mas claros', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Calcula el costo de una receta segun el valor de sus ingredientes.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Mejor seguimiento', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Facilita conocer el historial de pedidos de cada cliente.', 'size' => 17, 'color' => '52617A', 'after' => 0],
        ],
    ],
    [
        'fill' => 'FFFDF7',
        'content' => [
            ['text' => 'UTILIDAD', 'size' => 27, 'color' => 'C7436F', 'bold' => true, 'after' => 180],
            ['text' => 'Para la administracion', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Permite revisar ventas, pedidos pendientes y productos mas solicitados.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Para la produccion', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Ayuda a preparar los queques con los insumos correctos y en la cantidad necesaria.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Para el crecimiento', 'size' => 19, 'color' => '15233D', 'bold' => true, 'after' => 40],
            ['text' => 'Entrega informacion para decidir que sabores vender y que ingredientes reponer.', 'size' => 17, 'color' => '52617A', 'after' => 130],
            ['text' => 'Delicias Dulces convierte la gestion diaria en un proceso simple, ordenado y medible.', 'size' => 19, 'color' => '5A3D4F', 'bold' => true, 'align' => 'center', 'after' => 0],
        ],
    ],
]);

$document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:body>' . $outside
    . '<w:p><w:r><w:br w:type="page"/></w:r></w:p>' . $inside
    . '<w:sectPr><w:pgSz w:w="15840" w:h="12240" w:orient="landscape"/>'
    . '<w:pgMar w:top="520" w:right="540" w:bottom="520" w:left="540" w:header="300" w:footer="300" w:gutter="0"/>'
    . '</w:sectPr></w:body></w:document>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
    . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
    . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
    . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
    . '</Types>';

$relationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
    . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
    . '</Relationships>';

$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/></w:rPr></w:rPrDefault></w:docDefaults>'
    . '</w:styles>';

$core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
    . '<dc:title>Triptico Delicias Dulces</dc:title><dc:creator>Delicias Dulces</dc:creator>'
    . '<dcterms:created xsi:type="dcterms:W3CDTF">2026-08-28T00:00:00Z</dcterms:created></cp:coreProperties>';

$app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Delicias Dulces</Application></Properties>';

$zip = new ZipArchive();
if ($zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    throw new RuntimeException('No se pudo crear el archivo DOCX.');
}

$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $relationships);
$zip->addFromString('word/document.xml', $document);
$zip->addFromString('word/styles.xml', $styles);
$zip->addFromString('docProps/core.xml', $core);
$zip->addFromString('docProps/app.xml', $app);
$zip->close();

echo $outputFile . PHP_EOL;
