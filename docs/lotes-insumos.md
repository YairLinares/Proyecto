# Lotes y caducidad de insumos

- Cada entrada crea un lote o aumenta uno existente del mismo insumo y vencimiento. El código puede generarse automáticamente.
- El stock anterior a la migración se conserva en lotes INICIAL, sin inventar fechas de caducidad.
- En el detalle del insumo se puede completar o corregir el vencimiento.
- Un lote vence al comenzar el día siguiente a su fecha. El aviso empieza siete días antes.
- Los pedidos y las salidas automáticas consumen primero el lote con vencimiento más próximo. Los lotes sin fecha se consumen al final y siguen siendo utilizables.
- El stock total incluye lo vencido hasta que se registre su salida. El detalle muestra también el stock utilizable.
- Para desechar un lote vencido: registrar una Salida, seleccionar ese lote, indicar cantidad y motivo de caducidad.
- Los ajustes indican el stock total final del insumo. Si disminuye, se descuenta de los lotes; si aumenta, se registra en el lote indicado o uno nuevo.
- Las devoluciones con movimientos posteriores a esta implementación recuperan los lotes originales y sus fechas. Las devoluciones antiguas sin desglose generan un lote sin fecha.
- Este cambio controla insumos; los lotes de productos terminados y la separación entre reserva y producción quedan para otra etapa.

Instalación en otra copia: ejecutar `php artisan migrate`. No usar `migrate:fresh` con datos existentes.

Pruebas: `php artisan test` (SQLite en memoria).
