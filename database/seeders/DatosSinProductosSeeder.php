<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Insumo;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatosSinProductosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@deliciasdulces.com'],
            [
                'name' => 'Dulce Administradora',
                'nombre' => 'Dulce',
                'apellido' => 'Administradora',
                'password' => Hash::make('password123'),
                'telefono' => '76543210',
                'ciudad' => 'Santa Cruz',
                'cargo' => 'Administradora',
            ]
        );

        foreach (['Chocolate', 'Vainilla', 'Naranja', 'Limon', 'Zanahoria', 'Coco'] as $nombre) {
            Categoria::updateOrCreate(
                ['slug' => Str::slug($nombre)],
                [
                    'nombre' => $nombre,
                    'descripcion' => "Queques sabor {$nombre}",
                ]
            );
        }

        $insumos = [
            ['nombre' => 'Harina de Trigo', 'descripcion' => 'Base para masas de queque y bizcochos.', 'unidad' => 'Kg', 'stock_actual' => 45, 'stock_minimo' => 20, 'precio_unitario' => 8.50, 'proveedor' => 'Distribuidora La Espiga'],
            ['nombre' => 'Azucar Refinada', 'descripcion' => 'Azucar blanca para masas, almibares y coberturas.', 'unidad' => 'Kg', 'stock_actual' => 32, 'stock_minimo' => 15, 'precio_unitario' => 7.20, 'proveedor' => 'Insumos Dulce Hogar'],
            ['nombre' => 'Mantequilla sin Sal', 'descripcion' => 'Mantequilla para batidos y cremas.', 'unidad' => 'Kg', 'stock_actual' => 9, 'stock_minimo' => 10, 'precio_unitario' => 42.00, 'proveedor' => 'Lacteos Santa Maria'],
            ['nombre' => 'Huevos Frescos', 'descripcion' => 'Huevos seleccionados para reposteria.', 'unidad' => 'Unidad', 'stock_actual' => 180, 'stock_minimo' => 60, 'precio_unitario' => 0.80, 'proveedor' => 'Avicola El Valle'],
            ['nombre' => 'Chocolate Semiamargo', 'descripcion' => 'Chocolate para masas y rellenos intensos.', 'unidad' => 'Kg', 'stock_actual' => 14, 'stock_minimo' => 8, 'precio_unitario' => 55.00, 'proveedor' => 'Cacao Fino'],
            ['nombre' => 'Ralladura de Naranja', 'descripcion' => 'Ralladura natural para saborizar queques.', 'unidad' => 'Gramos', 'stock_actual' => 500, 'stock_minimo' => 200, 'precio_unitario' => 0.08, 'proveedor' => 'Frutas del Mercado'],
            ['nombre' => 'Ralladura de Limon', 'descripcion' => 'Ralladura fresca para masas citricas.', 'unidad' => 'Gramos', 'stock_actual' => 480, 'stock_minimo' => 200, 'precio_unitario' => 0.08, 'proveedor' => 'Frutas del Mercado'],
            ['nombre' => 'Zanahoria Rallada', 'descripcion' => 'Zanahoria lista para queque casero.', 'unidad' => 'Kg', 'stock_actual' => 11, 'stock_minimo' => 5, 'precio_unitario' => 6.00, 'proveedor' => 'Verduras Las Palmas'],
            ['nombre' => 'Coco Rallado', 'descripcion' => 'Coco seco rallado para masas y decoracion.', 'unidad' => 'Kg', 'stock_actual' => 8, 'stock_minimo' => 5, 'precio_unitario' => 38.00, 'proveedor' => 'Tropical Bolivia'],
            ['nombre' => 'Vainilla Liquida', 'descripcion' => 'Esencia de vainilla para preparaciones dulces.', 'unidad' => 'Mililitros', 'stock_actual' => 650, 'stock_minimo' => 200, 'precio_unitario' => 0.12, 'proveedor' => 'Esencias del Sur'],
        ];

        foreach ($insumos as $data) {
            $insumo = Insumo::updateOrCreate(['nombre' => $data['nombre']], $data);
            $insumo->actualizarEstado();
        }

        $clientes = [
            ['nombre_completo' => 'Maria Fernanda Rojas', 'telefono_principal' => '70011223', 'telefono_alternativo' => '76004567', 'direccion' => 'Av. Mutualista, barrio Los Tusequis', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Carlos Alberto Rivero', 'telefono_principal' => '72133445', 'telefono_alternativo' => null, 'direccion' => 'Calle Libertad #245', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Andrea Patricia Vargas', 'telefono_principal' => '75566778', 'telefono_alternativo' => '69077889', 'direccion' => 'Condominio Las Palmas, bloque B', 'tipo_cliente' => 'Corporativo'],
            ['nombre_completo' => 'Luis Miguel Arteaga', 'telefono_principal' => '73445566', 'telefono_alternativo' => null, 'direccion' => 'Zona Centro, cerca de la plaza principal', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Eventos Dulce Mesa', 'telefono_principal' => '77332211', 'telefono_alternativo' => '78441122', 'direccion' => 'Av. Banzer 5to anillo', 'tipo_cliente' => 'Corporativo'],
            ['nombre_completo' => 'Paola Jimena Salazar', 'telefono_principal' => '70998877', 'telefono_alternativo' => null, 'direccion' => 'Barrio Equipetrol Norte', 'tipo_cliente' => 'Regular'],
        ];

        foreach ($clientes as $data) {
            Cliente::updateOrCreate(['telefono_principal' => $data['telefono_principal']], $data + ['estado' => 'activo']);
        }

        $producto = Producto::query()->where('nombre', 'like', '%Chocolate%')->first() ?? Producto::query()->first();

        if ($producto) {
            $clientesPedido = Cliente::query()->take(4)->get();
            foreach ($clientesPedido as $i => $cliente) {
                $cantidad = $i + 1;
                $subtotal = $producto->precio_venta * $cantidad;
                $pedido = Pedido::updateOrCreate(
                    ['numero_pedido' => 'PED-DATO-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT)],
                    [
                        'cliente_id' => $cliente->id,
                        'usuario_id' => $admin->id,
                        'tipo_pedido' => 'Predefinido',
                        'prioridad' => $i === 2 ? 'Alto' : 'Normal',
                        'fecha_pedido' => now()->subDays(6 - $i)->toDateString(),
                        'fecha_entrega' => now()->addDays($i + 1)->toDateString(),
                        'descripcion_especificaciones' => 'Pedido de muestra cargado para pruebas del sistema.',
                        'direccion_entrega' => $cliente->direccion,
                        'telefono_contacto' => $cliente->telefono_principal,
                        'metodo_pago' => $i % 2 === 0 ? 'Efectivo' : 'Transferencia',
                        'subtotal' => $subtotal,
                        'anticipo_recibido' => $subtotal / 2,
                        'descuento' => 0,
                        'costo_envio' => 10,
                        'total' => $subtotal + 10,
                        'estado' => ['Pendiente', 'En proceso', 'Completado', 'Pendiente'][$i],
                    ]
                );

                DetallePedido::updateOrCreate(
                    ['pedido_id' => $pedido->id, 'producto_id' => $producto->id],
                    [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $subtotal,
                    ]
                );
            }
        }

        $this->command->info('Datos cargados sin crear productos nuevos.');
    }
}
