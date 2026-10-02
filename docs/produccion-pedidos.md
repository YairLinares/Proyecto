# Reserva de insumos y preparación de pedidos

## Flujo

1. Un pedido nuevo queda **Pendiente**. Se aparta la cantidad necesaria de cada insumo; el stock físico permanece igual. El detalle del pedido y el inventario muestran la reserva.
2. Al pulsar **Iniciar preparación**, el pedido pasa a **En proceso**. En una transacción se libera su reserva, se consumen lotes vigentes por fecha de vencimiento y se guardan los movimientos de salida.
3. **Marcar terminado** pasa el pedido a **Completado**.
4. Si se cancela un pedido pendiente, se libera la reserva. Si se cancela tras iniciar la preparación, el consumo queda en el historial y el stock no aumenta.

Las salidas y los ajustes manuales no pueden gastar el stock apartado para otros pedidos. Si los lotes vencen mientras el pedido está pendiente, se debe registrar una entrada vigente antes de iniciar la preparación. La reserva guarda cantidades por insumo, no un lote concreto; al preparar se eligen los lotes vigentes que vencen antes.

Los pedidos anteriores a esta migración tienen `insumos_reservados_at` vacío: sus insumos ya se descontaron con el flujo anterior. Cambiar su estado no los descuenta de nuevo y cancelar tampoco genera una entrada que pudiera inflar el inventario. Los pedidos con consumo registrado se conservan como historial.

Para instalar en otra copia del proyecto, ejecutar `php artisan migrate`. El cambio agrega columnas y una tabla; no modifica cantidades existentes.
