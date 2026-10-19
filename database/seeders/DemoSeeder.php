<?php

namespace Database\Seeders;

use App\Models\Pedido;
use App\Models\User;
use App\Notifications\NuevoPedido;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    /** Servicios y precios del catálogo (config/servicios.php). */
    private const CATALOGO = [
        ['nombre' => 'Canasto de ropa', 'precio' => 250],
        ['nombre' => 'Promo 3 canastos o más', 'precio' => 200],
        ['nombre' => 'Acolchado de guata (1 plaza)', 'precio' => 350],
        ['nombre' => 'Acolchado de guata (2 plazas)', 'precio' => 450],
        ['nombre' => 'Acolchado de pluma (1 plaza)', 'precio' => 450],
        ['nombre' => 'Acolchado de pluma (2 plazas)', 'precio' => 550],
        ['nombre' => 'Frazada 1 plaza', 'precio' => 300],
        ['nombre' => 'Frazada 2 plazas', 'precio' => 350],
        ['nombre' => 'Cubrecama 1 plaza', 'precio' => 250],
        ['nombre' => 'Cubrecama 2 plazas', 'precio' => 300],
    ];

    private const CALLES = [
        'Av. Reforma 123', 'Calle Hidalgo 45', 'Blvd. Independencia 789', 'Calle Allende 12',
        'Av. Juárez 300', 'Calle Madero 67', 'Privada López 89', 'Av. Universidad 1500',
        'Calle Morelos 23', 'Blvd. Zaragoza 456', 'Calle Galeana 34', 'Av. Hidalgo 210',
    ];

    public function run(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('cliente');

        $this->limpiar();
        $admins = $this->admins();
        $clientes = $this->clientes();
        $this->pedidos($clientes, $admins);
    }

    private function limpiar(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('items_pedido')->truncate();
        DB::table('pagos')->truncate();
        DB::table('pedidos')->truncate();
        DB::table('tarjetas')->truncate();
        DB::table('direcciones')->truncate();
        DB::table('notifications')->truncate();
        DB::table('activity_log')->truncate();
        DB::table('users')->where('email', '!=', 'marcosvillarreal2020@gmail.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function admins(): array
    {
        $marcos = User::where('email', 'marcosvillarreal2020@gmail.com')->first();

        if (! $marcos) {
            $marcos = User::create([
                'name' => 'Marcos Villarreal',
                'email' => 'marcosvillarreal2020@gmail.com',
                'password' => bcrypt('m92959802'),
            ]);
            $marcos->forceFill(['email_verified_at' => now()])->save();
        }
        $marcos->assignRole('admin');

        $admin = User::create([
            'name' => 'Ana García',
            'email' => 'admin@espuma.test',
            'password' => bcrypt('password'),
        ]);
        $admin->forceFill(['email_verified_at' => now()])->save();
        $admin->assignRole('admin');

        return [$marcos, $admin];
    }

    private function clientes(): array
    {
        $nombres = [
            'María López', 'Juan Pérez', 'Ana Martínez', 'Carlos Sánchez', 'Laura Gómez',
            'Pedro Ramírez', 'Sofía Torres', 'Miguel Flores', 'Elena Díaz', 'Ricardo Morales',
            'Valeria Cruz', 'Andrés Herrera', 'Camila Vargas', 'Fernando Ríos', 'Isabel Medina',
        ];

        $clientes = [];

        foreach ($nombres as $i => $nombre) {
            $email = Str::slug($nombre, '.').'@example.com';

            $user = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => bcrypt('password'),
                'whatsapp' => '521'.fake()->numerify('55########'),
                'notif_whatsapp_lista' => (bool) random_int(0, 1),
                'notif_en_camino' => (bool) random_int(0, 1),
                'notif_promos' => (bool) random_int(0, 1),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->assignRole('cliente');

            $cantidadDirecciones = random_int(1, 2);
            $direccionPredeterminada = null;

            for ($d = 0; $d < $cantidadDirecciones; $d++) {
                $dir = $user->direcciones()->create([
                    'etiqueta' => $d === 0 ? 'Casa' : 'Oficina',
                    'linea' => self::CALLES[($i + $d) % count(self::CALLES)],
                    'referencia' => fake()->optional()->sentence(4),
                    'predeterminada' => $d === 0,
                ]);

                if ($d === 0) {
                    $direccionPredeterminada = $dir;
                }
            }

            if (random_int(0, 2) !== 0) {
                $user->tarjetas()->create([
                    'marca' => fake()->randomElement(['Visa', 'Mastercard', 'Amex']),
                    'ultimos' => fake()->numerify('####'),
                    'vence' => fake()->numerify('##/##'),
                    'predeterminada' => true,
                ]);
            }

            $clientes[] = ['user' => $user, 'direccion' => $direccionPredeterminada];
        }

        return $clientes;
    }

    private function pedidos(array $clientes, array $admins): void
    {
        $totalPedidos = 42;

        for ($i = 0; $i < $totalPedidos; $i++) {
            $cliente = $clientes[array_rand($clientes)];

            $paso = match (true) {
                $i < 6 => 1,
                $i < 12 => 2,
                $i < 18 => 3,
                $i < 24 => 4,
                $i < 30 => 5,
                $i < 36 => 6,
                default => 7,
            };

            $express = random_int(0, 4) === 0;
            $lineas = $this->lineas();
            $subtotal = collect($lineas)->sum('subtotal');
            $total = $express ? round($subtotal * 1.5, 2) : round($subtotal, 2);

            $fecha = now()->subDays(random_int(0, 20));

            $pedido = Pedido::create([
                'user_id' => $cliente['user']->id,
                'direccion_id' => $cliente['direccion']?->id,
                'codigo' => 'PED-'.strtoupper(Str::random(5)),
                'servicio' => $this->resumenServicios($lineas),
                'kg' => 0,
                'paso_actual' => $paso,
                'estado' => Pedido::PASOS[$paso],
                'total' => $total,
                'express' => $express,
                'pendiente_pago' => $paso < 3,
                'fecha' => $fecha,
                'recogido_en' => $paso >= 4 ? $fecha->copy()->addDays(1) : null,
                'entrega_estimada' => $fecha->copy()->addDays($express ? 1 : 3),
            ]);

            foreach ($lineas as $linea) {
                $pedido->lineas()->create($linea);
            }

            if ($paso >= 2) {
                $metodo = $paso >= 3
                    ? fake()->randomElement(['tarjeta', 'transferencia', 'efectivo'])
                    : fake()->randomElement(['tarjeta', 'transferencia']);

                $estado = match ($metodo) {
                    'tarjeta' => 'confirmado',
                    'transferencia' => $paso >= 3 ? 'confirmado' : 'en_revision',
                    default => 'pendiente',
                };

                $pago = $cliente['user']->pagos()->create([
                    'pedido_id' => $pedido->id,
                    'monto' => $total,
                    'metodo' => $metodo,
                    'estado' => $estado,
                ]);

                if ($metodo === 'transferencia' && $paso >= 3) {
                    $pago->comprobante = 'comprobantes/'.Str::random(16).'.jpg';
                    $pago->save();
                }
            }

            if ($paso === 1 && random_int(0, 2) === 0) {
                Notification::send($admins, new NuevoPedido($pedido->fresh()->load('user')));
            }

            $this->auditar($pedido, $admins);
        }
    }

    private function auditar(Pedido $pedido, array $admins): void
    {
        $admin = $admins[array_rand($admins)];
        $paso = $pedido->paso_actual;
        $factura = $pedido->pago ? 'FAC-'.str_pad((string) $pedido->pago->id, 6, '0', STR_PAD_LEFT) : null;

        $pasos = range(1, max(1, $paso));
        shuffle($pasos);
        $pasos = array_slice($pasos, 0, min(3, count($pasos)));

        foreach ($pasos as $p) {
            $desde = Pedido::PASO_TEXTO[max(1, $p - 1)] ?? Pedido::PASO_TEXTO[1];
            $hasta = Pedido::PASO_TEXTO[$p];

            activity('pedido')
                ->causedBy($admin)
                ->performedOn($pedido)
                ->withProperties([
                    'accion' => 'avanzar',
                    'estado_anterior' => $desde,
                    'estado_nuevo' => $hasta,
                    'pedido' => $pedido->codigo,
                    'factura' => $factura,
                    'cliente_numero' => $pedido->user_id,
                    'cliente_nombre' => $pedido->user?->name,
                    'cliente_email' => $pedido->user?->email,
                    'cliente_whatsapp' => $pedido->user?->whatsapp,
                    'qr' => 'PEDIDO:'.$pedido->codigo.'|CLIENTE:'.$pedido->user_id.'|TOTAL:'.$pedido->total,
                ])
                ->log("Pedido #{$pedido->codigo}: {$desde} → {$hasta}");
        }
    }

    private function lineas(): array
    {
        $cantidadLineas = random_int(1, 3);
        $seleccion = collect(self::CATALOGO)->random($cantidadLineas);
        $lineas = [];

        foreach ($seleccion as $item) {
            $cantidad = $item['nombre'] === 'Canasto de ropa' && random_int(0, 3) === 0
                ? random_int(3, 5)
                : random_int(1, 2);

            $precio = $item['nombre'] === 'Canasto de ropa' && $cantidad >= 3
                ? 200
                : $item['precio'];

            $lineas[] = [
                'nombre' => $item['nombre'],
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => round($precio * $cantidad, 2),
            ];
        }

        return $lineas;
    }

    private function resumenServicios(array $lineas): string
    {
        return collect($lineas)
            ->map(fn ($l) => $l['cantidad'].'× '.$l['nombre'])
            ->implode(' + ');
    }
}
