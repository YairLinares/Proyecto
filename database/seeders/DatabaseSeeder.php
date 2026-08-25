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
        // CATEGORÍAS
        // ==========================================
        $categorias = [
            'Tortas', 'Cupcakes', 'Pasteles', 'Macarons', 'Postres', 'Panadería', 'Tartas'
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $nombre) {
            $categoriasCreadas[$nombre] = Categoria::create([
                'nombre' => $nombre,
                'slug' => Str::slug($nombre),
                'descripcion' => "Productos de la categoría $nombre",
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
            ['nombre' => 'Fresas Frescas', 'unidad' => 'Kg', 'stock_actual' => 12, 'stock_minimo' => 5, 'precio_unitario' => 9500, 'proveedor' => 'Frutas del Valle'],
            ['nombre' => 'Crema de Leche', 'unidad' => 'Litros', 'stock_actual' => 20, 'stock_minimo' => 10, 'precio_unitario' => 8900, 'proveedor' => 'Lácteos Premium'],
            ['nombre' => 'Vainilla Líquida', 'unidad' => 'Mililitros', 'stock_actual' => 500, 'stock_minimo' => 200, 'precio_unitario' => 45, 'proveedor' => 'Esencias Naturales'],
        ];

        $insumosCreados = [];
        foreach ($insumosData as $data) {
            $insumo = Insumo::create($data);
            $insumo->actualizarEstado();
            $insumosCreados[$data['nombre']] = $insumo;
        }

        // ==========================================
        // PRODUCTOS
        // ==========================================
        $productosData = [
            [
                'categoria' => 'Tortas',
                'nombre' => 'Torta de Cumpleaños Clásica',
                'descripcion' => 'Torta personalizada 2 pisos, fondant y flores',
                'precio_venta' => 95000,
                'costo_produccion' => 45000,
                'stock_disponible' => 8,
                'stock_minimo' => 2,
                'tiempo_preparacion_dias' => 2,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 2, 'Azúcar Refinada' => 1.5, 'Mantequilla sin Sal' => 0.5, 'Huevos Frescos' => 6],
            ],
            [
                'categoria' => 'Cupcakes',
                'nombre' => 'Cupcakes Clásicos',
                'descripcion' => 'Cupcake con buttercream decorado, 12 sabores',
                'precio_venta' => 4000,
                'costo_produccion' => 1800,
                'stock_disponible' => 48,
                'stock_minimo' => 12,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 0.1, 'Azúcar Refinada' => 0.08, 'Huevos Frescos' => 1],
            ],
            [
                'categoria' => 'Pasteles',
                'nombre' => 'Cheesecake de Fresa',
                'descripcion' => 'Cheesecake cremoso con coulis de fresa fresca',
                'precio_venta' => 75000,
                'costo_produccion' => 32000,
                'stock_disponible' => 3,
                'stock_minimo' => 3,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Crema de Leche' => 1, 'Fresas Frescas' => 0.5, 'Azúcar Refinada' => 0.3],
            ],
            [
                'categoria' => 'Macarons',
                'nombre' => 'Macarons Variados',
                'descripcion' => 'Macarons franceses, 8 sabores disponibles',
                'precio_venta' => 1500,
                'costo_produccion' => 600,
                'stock_disponible' => 120,
                'stock_minimo' => 30,
                'tiempo_preparacion_dias' => 2,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Azúcar Refinada' => 0.03, 'Huevos Frescos' => 0.2],
            ],
            [
                'categoria' => 'Tortas',
                'nombre' => 'Torta de Chocolate Premium',
                'descripcion' => 'Torta húmeda de chocolate con ganache',
                'precio_venta' => 110000,
                'costo_produccion' => 52000,
                'stock_disponible' => 5,
                'stock_minimo' => 2,
                'tiempo_preparacion_dias' => 2,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 1.8, 'Chocolate Semiamargo' => 0.8, 'Huevos Frescos' => 8],
            ],
            [
                'categoria' => 'Postres',
                'nombre' => 'Mousse de Chocolate',
                'descripcion' => 'Mousse individual de chocolate belga',
                'precio_venta' => 12000,
                'costo_produccion' => 5000,
                'stock_disponible' => 25,
                'stock_minimo' => 10,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Chocolate Semiamargo' => 0.15, 'Crema de Leche' => 0.2],
            ],
            [
                'categoria' => 'Panadería',
                'nombre' => 'Pan Artesanal',
                'descripcion' => 'Pan de masa madre horneado diariamente',
                'precio_venta' => 8500,
                'costo_produccion' => 3200,
                'stock_disponible' => 15,
                'stock_minimo' => 5,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 0.5],
            ],
            [
                'categoria' => 'Tartas',
                'nombre' => 'Tarta de Limón',
                'descripcion' => 'Tarta de limón con merengue italiano',
                'precio_venta' => 68000,
                'costo_produccion' => 28000,
                'stock_disponible' => 4,
                'stock_minimo' => 2,
                'tiempo_preparacion_dias' => 1,
                'unidad_medida' => 'Unidad',
                'insumos' => ['Harina de Trigo' => 0.6, 'Huevos Frescos' => 4, 'Azúcar Refinada' => 0.4],
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
            ['nombre_documento' => null, 'nombre_completo' => 'Carlos Ramírez', 'numero_documento' => '1045678901', 'telefono_principal' => '+57 315 876 5432', 'email' => 'c.ramirez@outlook.com', 'ciudad' => 'Medellín', 'direccion' => 'Carrera 20 #34-56', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Ana Martínez', 'numero_documento' => '1034567890', 'telefono_principal' => '+57 320 111 2233', 'email' => 'ana.martinez@gmail.com', 'ciudad' => 'Cali', 'direccion' => 'Avenida 6 #23-10', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Luis Hernández', 'numero_documento' => '1056789012', 'telefono_principal' => '+57 300 998 7654', 'email' => 'luis.h@yahoo.com', 'ciudad' => 'Bogotá', 'direccion' => 'Calle 100 #15-20', 'tipo_cliente' => 'Regular'],
            ['nombre_completo' => 'Sofía Torres', 'numero_documento' => '1067890123', 'telefono_principal' => '+57 312 445 6677', 'email' => 'sofia.torres@gmail.com', 'ciudad' => 'Bogotá', 'direccion' => 'Carrera 7 #85-40', 'tipo_cliente' => 'Corporativo'],
            ['nombre_completo' => 'Diego Morales', 'numero_documento' => '1078901234', 'telefono_principal' => '+57 318 223 3445', 'email' => 'diego.morales@hotmail.com', 'ciudad' => 'Barranquilla', 'direccion' => 'Calle 72 #45-12', 'tipo_cliente' => 'Regular'],
        ];

        $clientesCreados = [];
        foreach ($clientesData as $data) {
            unset($data['nombre_documento']);
            $data['tipo_documento'] = 'Cédula de Ciudadanía';
            $clientesCreados[] = Cliente::create($data);
        }

        // ==========================================
        // PEDIDOS DE EJEMPLO
        // ==========================================
        $pedidosData = [
            ['cliente' => 0, 'producto' => 0, 'cantidad' => 1, 'estado' => 'Pendiente', 'dias_entrega' => 4],
            ['cliente' => 1, 'producto' => 1, 'cantidad' => 24, 'estado' => 'En proceso', 'dias_entrega' => 2],
            ['cliente' => 2, 'producto' => 2, 'cantidad' => 1, 'estado' => 'Completado', 'dias_entrega' => -1],
            ['cliente' => 3, 'producto' => 3, 'cantidad' => 36, 'estado' => 'Completado', 'dias_entrega' => -3],
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
        $this->command->info('   - 7 categorías');
        $this->command->info('   - 8 insumos');
        $this->command->info('   - 8 productos');
        $this->command->info('   - 6 clientes');
        $this->command->info('   - 5 pedidos de ejemplo');
    }
}