<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // USUARIO ADMINISTRADOR
        // ==========================================
        $admin = User::create([
            'name' => 'Laura Campos',
            'nombre' => 'Laura',
            'apellido' => 'Campos',
            'email' => 'admin@deliciasdulces.co',
            'password' => Hash::make('password123'),
            'telefono' => '+57 310 456 7890',
            'ciudad' => 'Bogotá',
            'cargo' => 'Administradora',
        ]);

        // ==========================================
        // CATEGORÍAS (Sabores de Queque Clásico)
        // ==========================================
        $categorias = [
            'Chocolate', 'Vainilla', 'Naranja', 'Limón', 'Zanahoria', 'Coco'
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $nombre) {
            $categoriasCreadas[$nombre] = Categoria::create([
                'nombre' => $nombre,
                'slug' => Str::slug($nombre),
                'descripcion' => "Queques sabor $nombre",
            ]);
        }

        // ==========================================
        // INSUMOS
        // ==========================================
        $insumosData = [
            ['nombre' => 'Harina de Trigo', 'unidad' => 'Kg', 'stock_actual' => 45, 'stock_minimo' => 20, 'precio_unitario' => 2800, 'proveedor' => 'Molinos del Norte'],
            ['nombre' => 'Azúcar Refinada', 'unidad' => 'Kg', 'stock_actual' => 30, 'stock_minimo' => 15, 'precio_unitario' => 3200, 'proveedor' => 'Industrias Dulces'],
            ['nombre' => 'Mantequilla sin Sal', 'unidad' => 'Kg', 'stock_actual' => 8, 'stock_minimo' => 10, 'precio_unitario' => 18500, 'proveedor' => 'Lácteos Premium'],
            ['nombre' => 'Huevos Frescos', 'unidad' => 'Unidad', 'stock_actual' => 180, 'stock_minimo' => 60, 'precio_unitario' => 458, 'proveedor' => 'Avícola El Campo'],
            ['nombre' => 'Chocolate Semiamargo', 'unidad' => 'Kg', 'stock_actual' => 15, 'stock_minimo' => 8, 'precio_unitario' => 22000, 'proveedor' => 'Chocolatería Suprema'],
            ['nombre' => 'Ralladura de Naranja', 'unidad' => 'Gramos', 'stock_actual' => 500, 'stock_minimo' => 200, 'precio_unitario' => 30, 'proveedor' => 'Frutas del Valle'],
            ['nombre' => 'Ralladura de Limón', 'unidad' => 'Gramos', 'stock_actual' => 500, 'stock_minimo' => 200, 'precio_unitario' => 30, 'proveedor' => 'Frutas del Valle'],
            ['nombre' => 'Zanahoria Rallada', 'unidad' => 'Kg', 'stock_actual' => 10, 'stock_minimo' => 5, 'precio_unitario' => 2200, 'proveedor' => 'Frutas del Valle'],
            ['nombre' => 'Coco Rallado', 'unidad' => 'Kg', 'stock_actual' => 8, 'stock_minimo' => 5, 'precio_unitario' => 15000, 'proveedor' => 'Tropical Foods'],
            ['nombre' => 'Vainilla Líquida', 'unidad' => 'Mililitros', 'stock_actual' => 500, 'stock_minimo' => 200, 'precio_unitario' => 45, 'proveedor' => 'Esencias Naturales'],
        ];

        $insumosCreados = [];
        foreach ($insumosData as $data) {
            $insumo = Insumo::create($data);
            $insumo->actualizarEstado();
            $insumosCreados[$data['nombre']] = $insumo;
        }

        // ==========================================
        // PRODUCTOS (Queques Clásicos por Sabor)
        // ==========================================
        $productosData = [
            [
                'categoria' => 'Chocolate',
                'nombre' => 'Queque Clásico de Chocolate',
                'descripcion' => 'Queque esponjoso de chocolate, receta tradicional',
                'precio_venta' => 45000,
                'costo_produccion' => 20000,
                'stock_disponible' => 10,
                'stock_minimo' => 3,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.8, 'Mantequilla sin Sal' => 0.3, 'Huevos Frescos' => 4, 'Chocolate Semiamargo' => 0.4],
            ],
            [
                'categoria' => 'Vainilla',
                'nombre' => 'Queque Clásico de Vainilla',
                'descripcion' => 'Queque tradicional de vainilla, suave y esponjoso',
                'precio_venta' => 40000,
                'costo_produccion' => 17000,
                'stock_disponible' => 12,
                'stock_minimo' => 3,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.8, 'Mantequilla sin Sal' => 0.3, 'Huevos Frescos' => 4, 'Vainilla Líquida' => 15],
            ],
            [
                'categoria' => 'Naranja',
                'nombre' => 'Queque Clásico de Naranja',
                'descripcion' => 'Queque húmedo con ralladura y jugo natural de naranja',
                'precio_venta' => 42000,
                'costo_produccion' => 18000,
                'stock_disponible' => 8,
                'stock_minimo' => 3,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.7, 'Mantequilla sin Sal' => 0.3, 'Huevos Frescos' => 4, 'Ralladura de Naranja' => 20],
            ],
            [
                'categoria' => 'Limón',
                'nombre' => 'Queque Clásico de Limón',
                'descripcion' => 'Queque fresco con ralladura de limón natural',
                'precio_venta' => 42000,
                'costo_produccion' => 18000,
                'stock_disponible' => 9,
                'stock_minimo' => 3,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.7, 'Mantequilla sin Sal' => 0.3, 'Huevos Frescos' => 4, 'Ralladura de Limón' => 20],
            ],
            [
                'categoria' => 'Zanahoria',
                'nombre' => 'Queque Clásico de Zanahoria',
                'descripcion' => 'Queque de zanahoria con especias y cobertura cremosa',
                'precio_venta' => 48000,
                'costo_produccion' => 21000,
                'stock_disponible' => 6,
                'stock_minimo' => 2,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.8, 'Huevos Frescos' => 4, 'Zanahoria Rallada' => 0.6],
            ],
            [
                'categoria' => 'Coco',
                'nombre' => 'Queque Clásico de Coco',
                'descripcion' => 'Queque tropical con coco rallado natural',
                'precio_venta' => 46000,
                'costo_produccion' => 20000,
                'stock_disponible' => 7,
                'stock_minimo' => 2,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1, 'Azúcar Refinada' => 0.7, 'Mantequilla sin Sal' => 0.3, 'Huevos Frescos' => 4, 'Coco Rallado' => 0.3],
            ],
        ];

        $productosCreados = [];
        foreach ($productosData as $data) {
            $insumosProducto = $data['insumos'];
            unset($data['insumos']);
            $categoriaNombre = $data['categoria'];
            unset($data['categoria']);

            $data['categoria_id'] = $categoriasCreadas[$categoriaNombre]->id;

            $producto = Producto::create($data);

            foreach ($insumosProducto as $insumoNombre => $cantidad) {
                if (isset($insumosCreados[$insumoNombre])) {
                    $producto->insumos()->attach($insumosCreados[$insumoNombre]->id, ['cantidad_necesaria' => $cantidad]);
                }
            }

            $productosCreados[] = $producto;
        }

        // ==========================================
        // CLIENTES
        // ==========================================
        $clientesData = [
            ['nombre_completo' => 'María González López', 'numero_documento' => '1023456789', 'telefono_principal' => '+57 310 234 5678', 'email' => 'maria.g@gmail.com', 'ciudad' => 'Bogotá', 'direccion' => 'Calle 45 #12-34, Apto 301', 'tipo_cliente' => 'Corporativo'],
            ['nombre_completo' => 'Carlos Ramírez', 'numero_documento' => '1045678901', 'telefono_principal' => '+57 315 876 5432', 'email' => 'c.ramirez@outlook.com', 'ciudad' => 'Medellín', 'direccion' => 'Carrera 20 #34-56', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Ana Martínez', 'numero_documento' => '1034567890', 'telefono_principal' => '+57 320 111 2233', 'email' => 'ana.martinez@gmail.com', 'ciudad' => 'Cali', 'direccion' => 'Avenida 6 #23-10', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Luis Hernández', 'numero_documento' => '1056789012', 'telefono_principal' => '+57 300 998 7654', 'email' => 'luis.h@yahoo.com', 'ciudad' => 'Bogotá', 'direccion' => 'Calle 100 #15-20', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Sofía Torres', 'numero_documento' => '1067890123', 'telefono_principal' => '+57 312 445 6677', 'email' => 'sofia.torres@gmail.com', 'ciudad' => 'Bogotá', 'direccion' => 'Carrera 7 #85-40', 'tipo_cliente' => 'Corporativo'],
            ['nombre_completo' => 'Diego Morales', 'numero_documento' => '1078901234', 'telefono_principal' => '+57 318 223 3445', 'email' => 'diego.morales@hotmail.com', 'ciudad' => 'Barranquilla', 'direccion' => 'Calle 72 #45-12', 'tipo_cliente' => 'Regular'],
        ];

        $clientesCreados = [];
        foreach ($clientesData as $data) {
            $data['tipo_documento'] = 'Cédula de Ciudadanía';
            $clientesCreados[] = Cliente::create($data);
        }

        // ==========================================
        // PEDIDOS DE EJEMPLO
        // ==========================================
        $pedidosData = [
            ['cliente' => 0, 'producto' => 0, 'cantidad' => 1, 'estado' => 'Pendiente', 'dias_entrega' => 4],
            ['cliente' => 1, 'producto' => 1, 'cantidad' => 2, 'estado' => 'En proceso', 'dias_entrega' => 2],
            ['cliente' => 2, 'producto' => 2, 'cantidad' => 1, 'estado' => 'Completado', 'dias_entrega' => -1],
            ['cliente' => 3, 'producto' => 3, 'cantidad' => 3, 'estado' => 'Completado', 'dias_entrega' => -3],
            ['cliente' => 4, 'producto' => 4, 'cantidad' => 1, 'estado' => 'En proceso', 'dias_entrega' => 9],
        ];

        foreach ($pedidosData as $i => $data) {
            $cliente = $clientesCreados[$data['cliente']];
            $producto = $productosCreados[$data['producto']];
            $subtotal = $producto->precio_venta * $data['cantidad'];

            $pedido = Pedido::create([
                'numero_pedido' => 'PED-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'cliente_id' => $cliente->id,
                'tipo_pedido' => 'Personalizado',
                'prioridad' => 'Normal',
                'fecha_pedido' => now()->subDays(2),
                'fecha_entrega' => now()->addDays($data['dias_entrega']),
                'direccion_entrega' => $cliente->direccion,
                'telefono_contacto' => $cliente->telefono_principal,
                'subtotal' => $subtotal,
                'descuento' => 0,
                'costo_envio' => 0,
                'total' => $subtotal,
                'metodo_pago' => 'Efectivo',
                'anticipo_recibido' => $subtotal * 0.5,
                'estado' => $data['estado'],
                'usuario_id' => $admin->id,
            ]);

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => $data['cantidad'],
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal,
            ]);

            if ($data['estado'] == 'Completado') {
                $cliente->total_compras = $cliente->pedidos()->where('estado', 'Completado')->sum('total');
                $cliente->save();
            }
        }

        $this->command->info('✅ Base de datos poblada exitosamente:');
        $this->command->info('   - 1 usuario administrador');
        $this->command->info('   - 6 categorías (sabores de queque)');
        $this->command->info('   - 10 insumos');
        $this->command->info('   - 6 productos (queques clásicos)');
        $this->command->info('   - 6 clientes');
        $this->command->info('   - 5 pedidos de ejemplo');
    }
}