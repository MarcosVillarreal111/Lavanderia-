<p align="center">
  <h1 align="center">Espuma — Lavandería</h1>
  <p align="center">Sistema web para gestionar pedidos de lavandería de principio a fin: catálogo, pedidos, pagos, seguimiento en tiempo real y panel de administración.</p>
</p>

## Descripción

**Espuma** es una aplicación Laravel pensada para una lavandería que quiere operar 100 % en línea: el cliente agenda su recogida, arma su pedido, paga y sigue cada paso desde la web; el administrador gestiona los pedidos en un tablero tipo kanban con notificaciones, auditoría y facturación.

## Funcionalidades

### Cliente
- Registro e inicio de sesión (Jetstream / Fortify).
- Catálogo de servicios con carrito y cantidades.
- Agendar recogida (día, franja horaria y dirección).
- Pago con **tarjeta**, **transferencia** (subida de comprobante) o **efectivo**.
- **Seguimiento del pedido en 7 estados** con mensajes y actualización automática (polling).
- Historial de pedidos con opción de **repetir**.
- Perfil: datos personales y cambio de contraseña.

### Administrador
- **Tablero kanban** de 7 columnas (un estado por columna) con avanzar/retroceder y refresco automático.
- **Notificaciones** de pedidos nuevos con campanita y contador.
- **Auditoría**: registro de acciones por administrador, con filtros por fecha y búsqueda.
- **Ver registros**: pedidos entregados archivados, con paginación.
- **Facturación** imprimible por pedido.

## Flujo del pedido (7 pasos)

| Paso | Estado | Descripción |
|-----:|--------|-------------|
| 1 | `en_espera` | En espera de confirmación de pedido |
| 2 | `proceso_pago` | Proceso de pago |
| 3 | `pago_confirmado` | Pago confirmado |
| 4 | `recogida` | En recogida de su pedido |
| 5 | `en_proceso` | En proceso |
| 6 | `en_camino` | Pedido en camino |
| 7 | `entregado` | Pedido entregado |

## Stack

- **PHP 8.4** + **Laravel 13**
- **Livewire 3** + **Alpine.js**
- **Jetstream 5** + **Fortify** (autenticación)
- **spatie/laravel-permission** (roles: admin / cliente)
- **spatie/laravel-activitylog** (auditoría)
- **MySQL 8**
- **Tailwind CSS** + **Vite**
- **Docker** (PHP-FPM, nginx, MySQL)

## Requisitos

- Docker y Docker Compose
- Node.js 18+ (para compilar assets)

## Puesta en marcha

```bash
# 1. Clonar
git clone https://github.com/MarcosVillarreal111/Lavanderia-.git
cd Lavanderia-

# 2. Variables de entorno
cp .env.example .env

# 3. Levantar contenedores
docker compose up -d --build

# 4. Instalar dependencias y preparar la app
docker exec -w /var/www/html laravel_app composer install
docker exec -w /var/www/html laravel_app php artisan key:generate
docker exec -w /var/www/html laravel_app php artisan migrate --seed

# 5. Compilar assets
npm install && npm run build
```

La aplicación queda disponible en **http://localhost:8080**.

## Cuentas de prueba

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Admin | `admin@espuma.test` | `password` |
| Cliente | `juan.perez@example.com` | `password` |

Crear un administrador nuevo:

```bash
docker exec -w /var/www/html laravel_app php artisan make:admin {email} {password} "{nombre}"
```

## Datos de demostración

```bash
docker exec -w /var/www/html laravel_app php artisan db:seed --class=DemoSeeder
```

Genera clientes, direcciones, tarjetas, pedidos repartidos por los 7 estados, pagos, notificaciones y entradas de auditoría.

## Pruebas

```bash
docker exec -w /var/www/html laravel_app php artisan test
```

## Estructura relevante

```
app/
├── Http/Controllers/          # Admin: pedidos, auditoría, registros, facturas, notificaciones
├── Livewire/
│   ├── PedidoActivo.php       # Carrito, pedido en curso y seguimiento
│   └── ModalPago.php          # Flujo de pago (tarjeta / transferencia / efectivo)
├── Models/Pedido.php          # Estados, mensajes y transiciones
└── Console/Commands/MakeAdmin.php
database/seeders/DemoSeeder.php
resources/views/
├── admin/                     # Panel de administración
├── livewire/                  # Vistas Livewire
└── auth/                      # Login, registro, etc.
```

## Notas

- El servidor nginx escucha en el puerto **8080**.
- El polling del tablero y del estado del pedido se pausa durante modales, pagos y envío de formularios para no interrumpir al usuario.

## Licencia

Software propietario. Todos los derechos reservados.
