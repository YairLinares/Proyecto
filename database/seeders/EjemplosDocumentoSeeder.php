<?php

namespace Database\Seeders;

use App\Models\{Categoria, Cliente, Insumo, MovimientoInsumo, Pedido, Producto, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

/** Datos ficticios independientes: no modifica ni elimina registros existentes. */
class EjemplosDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $usuario = User::where('cargo', 'Administrador')->firstOrFail();
            User::firstOrCreate(['email' => 'empleada.documento@example.test'], [
                'username' => 'demo.documento', 'name' => 'Lucía Rojas', 'nombre' => 'Lucía',
                'apellido' => 'Rojas', 'cargo' => 'Empleado', 'ciudad' => 'La Paz',
                'telefono' => '70001001', 'password' => Hash::make('Ejemplo2026!'),
            ]);

            $insumos = collect();
            foreach ([
                ['Harina de repostería', 'Kg', 80, 10, 9],
                ['Azúcar blanca', 'Kg', 50, 10, 8],
                ['Huevos seleccionados', 'Unidad', 360, 30, 1.2],
                ['Mantequilla para queques', 'Kg', 30, 5, 40],
                ['Leche entera', 'Litros', 30, 5, 9],
                ['Cacao de repostería', 'Kg', 15, 3, 55],
                ['Esencia de vainilla', 'Mililitros', 1500, 100, .12],
                ['Naranja fresca', 'Unidad', 100, 10, 1.5],
                ['Limón fresco', 'Unidad', 100, 10, 1],
                ['Zanahoria fresca', 'Kg', 30, 3, 7],
                ['Coco para repostería', 'Kg', 15, 2, 38],
                ['Polvo leudante', 'Gramos', 2000, 200, .04],
                ['Nueces para decoración', 'Kg', 1, 2, 65],
                ['Cerezas para decoración', 'Kg', 0, 1, 45],
            ] as [$nombre, $unidad, $stock, $minimo, $precio]) {
                $insumo = Insumo::firstOrCreate(['nombre' => $nombre.' (demo)'], [
                    'descripcion' => 'Insumo ficticio para las capturas del documento.',
                    'unidad' => $unidad, 'stock_actual' => 0, 'stock_minimo' => $minimo,
                    'precio_unitario' => $precio,
                ]);
                if ($insumo->wasRecentlyCreated) {
                    if ($stock > 0) {
                        MovimientoInsumo::registrar($insumo, 'Entrada', $stock,
                            'Compra inicial de ejemplo para el documento', $usuario->id,
                            codigoLote: 'DOC-'.$insumo->id,
                            fechaVencimiento: today()->addMonths(3)->toDateString());
                    } else {
                        $insumo->actualizarEstado();
                    }
                }
                $insumos[$nombre] = $insumo->fresh();
            }

            $productos = collect();
            foreach ([
                ['Chocolate', 65, ['Cacao de repostería' => .12]],
                ['Vainilla', 48, ['Esencia de vainilla' => 10]],
                ['Naranja', 50, ['Naranja fresca' => 2]],
                ['Limón', 50, ['Limón fresco' => 2]],
                ['Zanahoria', 55, ['Zanahoria fresca' => .4]],
                ['Coco', 58, ['Coco para repostería' => .15]],
            ] as [$sabor, $precio, $extras]) {
                $categoria = Categoria::firstOrCreate(['slug' => Str::slug($sabor)], [
                    'nombre' => $sabor, 'descripcion' => 'Queques de sabor '.$sabor,
                ]);
                $receta = array_merge(['Harina de repostería' => .45, 'Azúcar blanca' => .25,
                    'Huevos seleccionados' => 3, 'Mantequilla para queques' => .15,
                    'Leche entera' => .2, 'Polvo leudante' => 12], $extras);
                $costo = 0;
                $pivot = [];
                foreach ($receta as $nombre => $cantidad) {
                    $costo += $insumos[$nombre]->precio_unitario * $cantidad;
                    $pivot[$insumos[$nombre]->id] = ['cantidad_necesaria' => $cantidad];
                }
                $producto = Producto::firstOrCreate(['nombre' => 'Queque de '.$sabor.' (demo)'], [
                    'categoria_id' => $categoria->id, 'descripcion' => 'Queque artesanal de 12 porciones. Ejemplo ficticio para documentación.',
                    'precio_venta' => $precio, 'costo_produccion' => round($costo, 2),
                    'stock_disponible' => 0, 'stock_minimo' => 0, 'unidad_medida' => 'Unidad',
                    'tiempo_preparacion_dias' => 1, 'estado' => 'activo',
                ]);
                if ($producto->wasRecentlyCreated) {
                    $producto->insumos()->attach($pivot);
                }
                $productos->push($producto);
            }

            $clientes = collect();
            foreach (['Ana Flores', 'Bruno Quispe', 'Carla Méndez', 'Diego Choque',
                'Elena Rojas', 'Fernanda Lima', 'Gabriel Vargas', 'Helena Salazar',
                'Eventos Dulce Mesa', 'Cafetería Primavera', 'Oficina Los Andes', 'Isabel Rivero'] as $i => $nombre) {
                $clientes->push(Cliente::firstOrCreate(['nombre_completo' => $nombre.' (demo)'], [
                    'telefono_principal' => '70002'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'telefono_alternativo' => '71002'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'direccion' => ['Sopocachi', 'Miraflores', 'Calacoto', 'San Miguel'][$i % 4].', calle Ejemplo Nº '.(100 + $i).', La Paz',
                    'tipo_cliente' => $i >= 8 && $i <= 10 ? 'Corporativo' : 'Regular',
                    'estado' => 'activo', 'total_compras' => 0,
                ]));
            }

            for ($i = 0; $i < 24; $i++) {
                $numero = 'PED-DOC-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
                if (Pedido::where('numero_pedido', $numero)->exists()) {
                    continue;
                }
                $estado = $i < 14 ? 'Completado' : ($i < 18 ? 'Pendiente' : ($i < 22 ? 'En proceso' : 'Cancelado'));
                $cliente = $clientes[$i % 12];
                $producto = $productos[$i % 6];
                $cantidad = 1 + $i % 3;
                $subtotal = $producto->precio_venta * $cantidad;
                $envio = $i % 2 ? 10 : 0;
                $descuento = $i % 5 === 0 ? 5 : 0;
                $total = $subtotal + $envio - $descuento;
                $fecha = today()->subDays($i < 14 ? $i * 2 + 2 : 2);
                $metodo = ['Efectivo', 'Transferencia', 'Tarjeta'][$i % 3];
                $anticipo = $estado === 'Cancelado' ? 0 : round($total / 2, 2);
                $pedido = Pedido::create([
                    'numero_pedido' => $numero, 'cliente_id' => $cliente->id, 'usuario_id' => $usuario->id,
                    'tipo_pedido' => $i % 2 ? 'Personalizado' : 'Predefinido',
                    'prioridad' => ['Normal', 'Alto', 'Bajo'][$i % 3], 'fecha_pedido' => $fecha,
                    'fecha_entrega' => $i < 14 ? $fecha->copy()->addDay() : today()->addDays(1 + $i % 4),
                    'descripcion_especificaciones' => 'Ejemplo ficticio para documentación. '.($i % 2 ? 'Dedicatoria: ¡Feliz cumpleaños! Presentación para regalo.' : 'Presentación tradicional de 12 porciones.'),
                    'direccion_entrega' => $cliente->direccion, 'telefono_contacto' => $cliente->telefono_principal,
                    'metodo_pago' => $metodo, 'subtotal' => $subtotal, 'descuento' => $descuento,
                    'costo_envio' => $envio, 'total' => $total, 'anticipo_recibido' => $anticipo, 'estado' => $estado,
                    'insumos_reservados_at' => $estado !== 'Cancelado' ? $fecha : null,
                    'produccion_iniciada_at' => in_array($estado, ['Completado', 'En proceso']) ? $fecha : null,
                ]);
                $pedido->detalles()->create(['producto_id' => $producto->id, 'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio_venta, 'subtotal' => $subtotal]);
                foreach ($producto->insumos as $insumo) {
                    $consumo = round($insumo->pivot->cantidad_necesaria * $cantidad, 2);
                    if ($estado === 'Pendiente') {
                        $pedido->reservas()->create(['insumo_id' => $insumo->id, 'cantidad' => $consumo]);
                    } elseif (in_array($estado, ['Completado', 'En proceso'])) {
                        $movimiento = MovimientoInsumo::registrar($insumo, 'Salida', $consumo,
                            'Producción de ejemplo '.$numero, $usuario->id, $pedido->id);
                        $movimiento->timestamps = false;
                        $movimiento->created_at = $fecha;
                        $movimiento->updated_at = $fecha;
                        $movimiento->save();
                    }
                }
                if ($anticipo > 0) {
                    $pedido->pagos()->create(['monto' => $anticipo, 'metodo_pago' => $metodo,
                        'fecha_pago' => $fecha, 'referencia' => $numero.'-ANT', 'observacion' => 'Anticipo ficticio de ejemplo']);
                }
                if ($estado === 'Completado') {
                    $pedido->pagos()->create(['monto' => $total - $anticipo, 'metodo_pago' => $metodo,
                        'fecha_pago' => $fecha->copy()->addDay(), 'referencia' => $numero.'-SAL', 'observacion' => 'Saldo ficticio de ejemplo']);
                }
            }
            foreach ($clientes as $cliente) {
                $cliente->update(['total_compras' => $cliente->pedidos()->where('estado', 'Completado')->sum('total')]);
            }
        });
        $this->command->info('Ejemplos del documento cargados sin eliminar datos existentes.');
    }
}
