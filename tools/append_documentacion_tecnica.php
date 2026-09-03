<?php

$source = 'f:\\Proyecto Grado y Emprendimiento\\Proyecto de Grado.docx';
$output = __DIR__ . '/../output/documentos/Proyecto_de_Grado_Delicias_Dulces_Completado.docx';

if (! is_file($source)) {
    fwrite(STDERR, "No se encontro el documento origen: {$source}\n");
    exit(1);
}

copy($source, $output);

function esc_xml(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
}

function p(string $text = '', string $style = 'Normal', string $align = ''): string
{
    $styleXml = $style !== 'Normal' ? '<w:pStyle w:val="' . esc_xml($style) . '"/>' : '';
    $alignXml = $align !== '' ? '<w:jc w:val="' . esc_xml($align) . '"/>' : '';
    return '<w:p><w:pPr>' . $styleXml . $alignXml . '</w:pPr><w:r><w:t xml:space="preserve">' . esc_xml($text) . '</w:t></w:r></w:p>';
}

function page_break(): string
{
    return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';
}

function heading(string $text, int $level = 1): string
{
    return p($text, 'Ttulo' . $level);
}

function bullet(string $text): string
{
    return p('- ' . $text);
}

function tbl(array $rows): string
{
    $xml = '<w:tbl><w:tblPr><w:tblW w:w="9360" w:type="dxa"/><w:tblBorders>'
        . '<w:top w:val="single" w:sz="4" w:space="0" w:color="808080"/>'
        . '<w:left w:val="single" w:sz="4" w:space="0" w:color="808080"/>'
        . '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="808080"/>'
        . '<w:right w:val="single" w:sz="4" w:space="0" w:color="808080"/>'
        . '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="BFBFBF"/>'
        . '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="BFBFBF"/>'
        . '</w:tblBorders></w:tblPr>';
    foreach ($rows as $i => $row) {
        $xml .= '<w:tr>';
        foreach ($row as $cell) {
            $fill = $i === 0 ? '<w:shd w:fill="E8EEF5"/>' : '';
            $xml .= '<w:tc><w:tcPr><w:tcW w:w="' . (int)(9360 / max(1, count($row))) . '" w:type="dxa"/>' . $fill . '</w:tcPr>'
                . '<w:p><w:r>' . ($i === 0 ? '<w:rPr><w:b/></w:rPr>' : '') . '<w:t xml:space="preserve">' . esc_xml($cell) . '</w:t></w:r></w:p></w:tc>';
        }
        $xml .= '</w:tr>';
    }
    return $xml . '</w:tbl>' . p('');
}

$content = page_break();
$content .= heading('CAPITULO IV: DISENO Y DOCUMENTACION TECNICA DEL SISTEMA', 1);
$content .= p('Este capitulo complementa la documentacion del proyecto Sistema web para el control de pedidos, ventas y stock del emprendimiento Delicias Dulces. Se organiza siguiendo la guia del documento de ejemplo proporcionado, pero adaptado al sistema real desarrollado en Laravel, con modulos de autenticacion, clientes, categorias, productos, insumos, pedidos, ventas, perfil de usuario y dashboard.');

$content .= heading('4.1 Actores del sistema', 2);
$content .= tbl([
    ['Actor', 'Descripcion', 'Responsabilidades principales'],
    ['Administrador', 'Usuario con control completo del sistema.', 'Gestionar usuarios, catalogos, pedidos, ventas, stock y reportes.'],
    ['Encargado de ventas', 'Usuario que registra clientes y pedidos.', 'Registrar pedidos, consultar ventas, actualizar estados y revisar datos del cliente.'],
    ['Encargado de produccion', 'Usuario que revisa pedidos e insumos disponibles.', 'Controlar stock, registrar movimientos y verificar disponibilidad para la preparacion.'],
    ['Cliente', 'Persona que realiza pedidos al emprendimiento.', 'Proporcionar datos de contacto, direccion, productos solicitados y forma de pago.'],
]);

$content .= heading('4.2 Casos de uso generales', 2);
$content .= tbl([
    ['Codigo', 'Caso de uso', 'Actor principal', 'Resultado esperado'],
    ['CU-01', 'Iniciar sesion', 'Administrador / usuario', 'Acceso seguro al panel principal.'],
    ['CU-02', 'Gestionar clientes', 'Administrador / ventas', 'Clientes registrados, editados, consultados o eliminados segun corresponda.'],
    ['CU-03', 'Gestionar categorias', 'Administrador', 'Categorias disponibles para clasificar productos.'],
    ['CU-04', 'Gestionar productos', 'Administrador', 'Productos con precio, costo, stock minimo y receta de insumos.'],
    ['CU-05', 'Gestionar insumos', 'Administrador / produccion', 'Insumos con stock actual, stock minimo, unidad y proveedor.'],
    ['CU-06', 'Registrar movimiento de insumo', 'Produccion', 'Entrada o salida registrada y stock actualizado.'],
    ['CU-07', 'Registrar pedido', 'Ventas', 'Pedido creado con productos, cliente, fechas, anticipo y total.'],
    ['CU-08', 'Cambiar estado de pedido', 'Ventas / produccion', 'Pedido actualizado a Pendiente, En proceso, Completado o Cancelado.'],
    ['CU-09', 'Consultar ventas', 'Administrador / ventas', 'Listado filtrado de ventas y pedidos completados.'],
    ['CU-10', 'Actualizar perfil', 'Usuario autenticado', 'Datos personales o contrasena actualizados.'],
]);

$content .= heading('4.3 Descripcion de casos de uso', 2);
$useCases = [
    ['CU-01 Iniciar sesion', 'El usuario ingresa correo y contrasena. El sistema valida las credenciales y redirige al dashboard. Si los datos son incorrectos, muestra un mensaje de error y mantiene el acceso restringido.'],
    ['CU-02 Gestionar clientes', 'Permite registrar nombre completo, telefono principal, telefono alternativo, direccion, tipo de cliente y estado. Tambien permite buscar, editar, visualizar detalle y eliminar clientes cuando no afecte la integridad de los pedidos.'],
    ['CU-04 Gestionar productos', 'Permite crear productos vinculados a una categoria, definir precio de venta, costo de produccion, stock disponible, stock minimo, tiempo de preparacion y estado. Tambien se define la receta mediante la relacion producto-insumo.'],
    ['CU-05 Gestionar insumos', 'Permite registrar materia prima, unidad de medida, stock actual, stock minimo, precio unitario, proveedor y estado. El sistema identifica stock normal, bajo o agotado.'],
    ['CU-07 Registrar pedido', 'El encargado selecciona cliente y productos, define tipo de pedido, prioridad, fecha de entrega, direccion, telefono, anticipo, descuento y costo de envio. El sistema calcula subtotal y total, valida stock y descuenta automaticamente los insumos de la receta.'],
    ['CU-08 Cambiar estado de pedido', 'El usuario actualiza el estado del pedido. Si se cancela un pedido, el sistema devuelve al inventario los insumos consumidos mediante movimientos de entrada, evitando perdida de control de stock.'],
    ['CU-09 Consultar ventas', 'Permite revisar pedidos/ventas por busqueda, cliente, codigo de pedido o filtros de fecha/estado, apoyando el control comercial del emprendimiento.'],
];
foreach ($useCases as [$title, $desc]) {
    $content .= heading($title, 3) . p($desc);
}

$content .= heading('4.4 Prototipos de interfaz del sistema', 2);
$content .= p('Los prototipos representan las pantallas principales implementadas en el sistema. Para la defensa o presentacion final se recomienda reemplazar o complementar estas descripciones con capturas reales del sistema funcionando.');
$content .= tbl([
    ['Pantalla', 'Elementos principales', 'Funcion'],
    ['Inicio de sesion', 'Formulario de correo, contrasena y boton ingresar.', 'Autenticar usuarios y proteger los modulos internos.'],
    ['Dashboard', 'Indicadores de pedidos, clientes, productos, stock bajo y accesos rapidos.', 'Presentar una vista resumida del estado del negocio.'],
    ['Clientes', 'Buscador, tabla de clientes, acciones ver/editar/eliminar y formulario de registro.', 'Administrar informacion de contacto y entrega.'],
    ['Productos', 'Listado, formulario, categoria, precio, costo, stock, estado y receta de insumos.', 'Controlar el catalogo de productos ofrecidos.'],
    ['Insumos', 'Listado, stock actual, stock minimo, proveedor, estado y movimientos.', 'Controlar materia prima disponible.'],
    ['Pedidos', 'Formulario con cliente, productos, cantidades, fecha de entrega, anticipo y total.', 'Registrar pedidos y descontar insumos automaticamente.'],
    ['Detalle de pedido', 'Datos del cliente, productos, importes, estado y pagos relacionados.', 'Consultar informacion completa del pedido.'],
    ['Ventas', 'Tabla de ventas/pedidos con filtros.', 'Apoyar el control de ingresos y seguimiento comercial.'],
    ['Perfil', 'Datos del usuario y cambio de contrasena.', 'Actualizar informacion personal de acceso.'],
]);

$content .= heading('4.5 Modelo de base de datos', 2);
$content .= p('La base de datos esta orientada al control de clientes, productos, insumos, pedidos, detalle de pedidos, pagos, usuarios y movimientos de inventario. Las relaciones principales aseguran trazabilidad entre la venta, el producto vendido y el consumo de insumos.');
$content .= tbl([
    ['Tabla', 'Campos principales', 'Descripcion'],
    ['users', 'id, name, email, password, rol, telefono, direccion, estado', 'Usuarios autenticados del sistema.'],
    ['clientes', 'id, nombre_completo, telefono_principal, telefono_alternativo, direccion, tipo_cliente, estado, total_compras', 'Clientes registrados para pedidos y entregas.'],
    ['categorias', 'id, nombre, slug, descripcion', 'Clasificacion de productos.'],
    ['productos', 'id, categoria_id, nombre, descripcion, precio_venta, costo_produccion, stock_disponible, stock_minimo, tiempo_preparacion_dias, unidad_medida, estado', 'Productos ofrecidos por el emprendimiento.'],
    ['insumos', 'id, nombre, descripcion, unidad, stock_actual, stock_minimo, precio_unitario, proveedor, estado', 'Materia prima utilizada en la elaboracion de productos.'],
    ['insumo_producto', 'id, producto_id, insumo_id, cantidad_necesaria', 'Tabla intermedia que define la receta de cada producto.'],
    ['pedidos', 'id, numero_pedido, cliente_id, usuario_id, tipo_pedido, prioridad, fecha_pedido, fecha_entrega, direccion_entrega, telefono_contacto, metodo_pago, subtotal, anticipo_recibido, descuento, costo_envio, total, estado', 'Pedidos realizados por clientes.'],
    ['detalles_pedidos', 'id, pedido_id, producto_id, cantidad, precio_unitario, subtotal', 'Detalle de productos incluidos en cada pedido.'],
    ['pagos', 'id, pedido_id, monto, metodo_pago, fecha_pago, referencia, observacion', 'Pagos asociados a pedidos.'],
    ['movimientos_insumo', 'id, insumo_id, pedido_id, usuario_id, tipo, cantidad, stock_anterior, stock_nuevo, motivo, movimiento_origen_id, revertido_at', 'Historial de entradas, salidas y devoluciones de inventario.'],
]);

$content .= heading('4.6 Relaciones de la base de datos', 2);
$relations = [
    'Una categoria tiene muchos productos; un producto pertenece a una categoria.',
    'Un cliente tiene muchos pedidos; un pedido pertenece a un cliente.',
    'Un usuario puede registrar muchos pedidos; un pedido puede pertenecer al usuario que lo registro.',
    'Un pedido tiene muchos detalles; cada detalle pertenece a un pedido.',
    'Un producto puede aparecer en muchos detalles de pedido; cada detalle corresponde a un producto.',
    'Un producto utiliza muchos insumos y un insumo puede utilizarse en muchos productos mediante la tabla insumo_producto.',
    'Un pedido puede tener varios pagos registrados.',
    'Un insumo tiene muchos movimientos de inventario. Los movimientos pueden estar asociados a pedidos para justificar salidas o devoluciones.',
];
foreach ($relations as $relation) {
    $content .= bullet($relation);
}

$content .= heading('4.7 Diagrama entidad-relacion propuesto', 2);
$content .= p('Representacion textual del DER para ser convertida en diagrama grafico:');
$content .= p('USERS 1--N PEDIDOS; CLIENTES 1--N PEDIDOS; CATEGORIAS 1--N PRODUCTOS; PEDIDOS 1--N DETALLES_PEDIDOS; PRODUCTOS 1--N DETALLES_PEDIDOS; PRODUCTOS N--N INSUMOS mediante INSUMO_PRODUCTO; PEDIDOS 1--N PAGOS; INSUMOS 1--N MOVIMIENTOS_INSUMO; PEDIDOS 1--N MOVIMIENTOS_INSUMO.');

$content .= heading('4.8 Reglas de negocio', 2);
$rules = [
    'El acceso a los modulos internos requiere autenticacion.',
    'Cada pedido debe tener un cliente y al menos un producto.',
    'El numero de pedido debe ser unico.',
    'La fecha de entrega no debe ser anterior a la fecha actual al registrar un pedido.',
    'El sistema calcula subtotal y total considerando productos, descuento, anticipo y costo de envio.',
    'Al registrar un pedido se valida la disponibilidad de insumos asociados a los productos.',
    'Al cancelar o eliminar un pedido pendiente, los insumos consumidos se devuelven mediante movimientos de entrada.',
    'Los insumos pueden tener estado Normal, Stock bajo o Agotado segun stock actual y stock minimo.',
    'Los pedidos manejan los estados Pendiente, En proceso, Completado y Cancelado.',
];
foreach ($rules as $rule) {
    $content .= bullet($rule);
}

$content .= heading('4.9 Requerimientos funcionales complementarios', 2);
$content .= tbl([
    ['Codigo', 'Requerimiento'],
    ['RF-01', 'El sistema debe permitir registrar, modificar, listar y eliminar clientes.'],
    ['RF-02', 'El sistema debe permitir registrar productos con categoria, precio, costo y stock.'],
    ['RF-03', 'El sistema debe permitir asociar insumos a productos para formar recetas.'],
    ['RF-04', 'El sistema debe controlar entradas y salidas de insumos.'],
    ['RF-05', 'El sistema debe registrar pedidos con multiples productos.'],
    ['RF-06', 'El sistema debe calcular automaticamente subtotal y total del pedido.'],
    ['RF-07', 'El sistema debe mostrar ventas y pedidos mediante filtros de busqueda.'],
    ['RF-08', 'El sistema debe permitir actualizar el perfil del usuario autenticado.'],
]);

$content .= heading('4.10 Requerimientos no funcionales', 2);
$content .= tbl([
    ['Codigo', 'Requerimiento'],
    ['RNF-01', 'La aplicacion debe ser accesible desde un navegador web.'],
    ['RNF-02', 'El sistema debe proteger rutas internas mediante autenticacion.'],
    ['RNF-03', 'La informacion debe almacenarse en una base de datos relacional.'],
    ['RNF-04', 'Las operaciones criticas de pedidos e inventario deben ejecutarse con transacciones para mantener consistencia.'],
    ['RNF-05', 'La interfaz debe ser clara, responsiva y facil de utilizar para el emprendimiento.'],
]);

$content .= heading('4.11 Guia para incorporar capturas o prototipos graficos', 2);
$content .= p('Para completar la presentacion visual solicitada por el docente, se recomienda insertar capturas reales debajo de cada prototipo descrito: login, dashboard, clientes, productos, insumos, pedidos, detalle de pedido y ventas. Cada captura debe llevar titulo de figura, descripcion breve y relacion con el caso de uso correspondiente.');

$zip = new ZipArchive();
if ($zip->open($output) !== true) {
    fwrite(STDERR, "No se pudo abrir la copia DOCX\n");
    exit(1);
}

$documentXml = $zip->getFromName('word/document.xml');
if ($documentXml === false) {
    fwrite(STDERR, "No se encontro word/document.xml\n");
    exit(1);
}

$pos = strrpos($documentXml, '<w:sectPr');
if ($pos === false) {
    $pos = strrpos($documentXml, '</w:body>');
}

$documentXml = substr($documentXml, 0, $pos) . $content . substr($documentXml, $pos);
$zip->addFromString('word/document.xml', $documentXml);
$zip->close();

echo realpath($output) . PHP_EOL;
