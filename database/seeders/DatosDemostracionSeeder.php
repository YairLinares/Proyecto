<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatosDemostracionSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::firstOrCreate(
            ['email' => 'admin@deliciasdulces.test'],
            ['name' => 'Administradora Delicias', 'nombre' => 'Administradora', 'apellido' => 'Delicias', 'password' => Hash::make('password123'), 'telefono' => '70000000', 'ciudad' => 'La Paz', 'cargo' => 'Administradora']
        );

        $categorias = collect(['Chocolate', 'Vainilla', 'Naranja', 'Limón', 'Zanahoria', 'Coco', 'Red Velvet', 'Marmoleado'])
            ->mapWithKeys(fn (string $nombre) => [$nombre => Categoria::firstOrCreate(['nombre' => $nombre], ['slug' => Str::slug($nombre), 'descripcion' => "Queques sabor {$nombre}"])]);

        $insumos = [
            ['Harina de Trigo', 'Kg', 45, 15, 9.50, 'Molinos Andinos'],
            ['Azúcar', 'Kg', 30, 10, 8.00, 'Dulces Bolivia'],
            ['Huevos', 'Unidad', 180, 60, 1.20, 'Granja San Pedro'],
            ['Mantequilla', 'Kg', 12, 4, 38.00, 'Lácteos del Valle'],
            ['Chocolate en polvo', 'Kg', 10, 3, 52.00, 'Chocolates Andinos'],
            ['Leche', 'Litros', 20, 5, 8.50, 'Lácteos del Valle'],
            ['Vainilla', 'Mililitros', 500, 100, 0.12, 'Esencias La Paz'],
            ['Polvo de hornear', 'Gramos', 1000, 200, 0.04, 'Repostería Central'],
            ['Cocoa', 'Kg', 6, 2, 65.00, 'Chocolates Andinos'],
            ['Naranja', 'Unidad', 30, 10, 1.50, 'Mercado Rodríguez'],
            ['Zanahoria', 'Kg', 8, 3, 7.00, 'Mercado Rodríguez'],
            ['Coco rallado', 'Kg', 5, 2, 35.00, 'Tropical Foods'],
        ];

        $insumosCreados = collect($insumos)->mapWithKeys(function (array $dato) {
            [$nombre, $unidad, $stock, $minimo, $precio, $proveedor] = $dato;
            $insumo = Insumo::firstOrCreate(['nombre' => $nombre], ['unidad' => $unidad, 'stock_actual' => $stock, 'stock_minimo' => $minimo, 'precio_unitario' => $precio, 'proveedor' => $proveedor, 'estado' => $stock <= $minimo ? 'Stock bajo' : 'Normal']);
            return [$nombre => $insumo];
        });

        $productos = [
            ['Queque de Zanahoria', 'Zanahoria', 52, 2, ['Harina de Trigo' => 0.45, 'Azúcar' => 0.25, 'Huevos' => 3, 'Mantequilla' => 0.20, 'Zanahoria' => 0.40, 'Polvo de hornear' => 12]],
        ];

        $productosCreados = collect($productos)->mapWithKeys(function (array $dato) use ($categorias, $insumosCreados) {
            [$nombre, $categoria, $precio, $dias, $receta] = $dato;
            $costo = collect($receta)->reduce(
                fn (float $total, float $cantidad, string $insumo) => $total + ((float) $insumosCreados[$insumo]->precio_unitario * $cantidad),
                0
            );
            $producto = Producto::firstOrCreate(['nombre' => $nombre], ['categoria_id' => $categorias[$categoria]->id, 'descripcion' => "Delicioso {$nombre} preparado con ingredientes frescos.", 'precio_venta' => $precio, 'costo_produccion' => $costo, 'stock_disponible' => 0, 'stock_minimo' => 0, 'tiempo_preparacion_dias' => $dias, 'unidad_medida' => 'Unidad', 'estado' => 'activo']);
            if ($producto->insumos()->count() === 0) {
                $producto->insumos()->attach(collect($receta)->mapWithKeys(fn ($cantidad, $nombreInsumo) => [$insumosCreados[$nombreInsumo]->id => ['cantidad_necesaria' => $cantidad]])->all());
            }
            return [$nombre => $producto];
        });

        $clientes = collect([
            ['Andrea Flores', '70100101', 'Av. Arce 120, La Paz'], ['Bruno Quispe', '70100102', 'Calle 21 de Calacoto, La Paz'], ['Carla Méndez', '70100103', 'Av. Ballivián 450, La Paz'], ['David Choque', '70100104', 'Zona Sopocachi, La Paz'], ['Elena Rojas', '70100105', 'Cota Cota, La Paz'], ['Fernanda Lima', '70100106', 'Miraflores, La Paz'],
        ])->map(fn (array $dato) => Cliente::firstOrCreate(['nombre_completo' => $dato[0]], ['telefono_principal' => $dato[1], 'direccion' => $dato[2], 'tipo_cliente' => 'Regular', 'estado' => 'activo', 'total_compras' => 0]));

        $pedidos = [
            ['PED-DEMO-004', 3, 'Queque de Zanahoria', 1, 'Completado', -3, -1, 'Efectivo'],
        ];

        foreach ($pedidos as [$numero, $clienteIndice, $nombreProducto, $cantidad, $estado, $diasPedido, $diasEntrega, $metodo]) {
            $cliente = $clientes[$clienteIndice];
            $producto = $productosCreados[$nombreProducto];
            $subtotal = $producto->precio_venta * $cantidad;
            $pedido = Pedido::updateOrCreate(['numero_pedido' => $numero], ['cliente_id' => $cliente->id, 'usuario_id' => $usuario->id, 'tipo_pedido' => 'Predefinido', 'prioridad' => $estado === 'Pendiente' ? 'Alto' : 'Normal', 'fecha_pedido' => now()->addDays($diasPedido), 'fecha_entrega' => now()->addDays($diasEntrega), 'direccion_entrega' => $cliente->direccion, 'telefono_contacto' => $cliente->telefono_principal, 'metodo_pago' => $metodo, 'subtotal' => $subtotal, 'anticipo_recibido' => $subtotal / 2, 'descuento' => 0, 'costo_envio' => 0, 'total' => $subtotal, 'estado' => $estado]);
            DetallePedido::updateOrCreate(['pedido_id' => $pedido->id, 'producto_id' => $producto->id], ['cantidad' => $cantidad, 'precio_unitario' => $producto->precio_venta, 'subtotal' => $subtotal]);
            Pago::updateOrCreate(['pedido_id' => $pedido->id, 'referencia' => "DEMO-{$numero}"], ['monto' => $estado === 'Completado' ? $subtotal : $subtotal / 2, 'metodo_pago' => $metodo, 'fecha_pago' => now()->addDays($diasPedido), 'observacion' => 'Pago de demostración']);
        }

        foreach ($insumosCreados->take(4) as $insumo) {
            MovimientoInsumo::firstOrCreate(['insumo_id' => $insumo->id, 'motivo' => 'Carga inicial de demostración'], ['usuario_id' => $usuario->id, 'tipo' => 'Entrada', 'cantidad' => 0, 'stock_anterior' => $insumo->stock_actual, 'stock_posterior' => $insumo->stock_actual]);
        }
    }
}
