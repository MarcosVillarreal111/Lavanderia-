<?php

namespace Tests\Feature;

use App\Livewire\ModalPago;
use App\Livewire\PedidoActivo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_public_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/terminos')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_renders_authenticated_client_pages(): void
    {
        $u = User::factory()->create();

        $d = $u->direcciones()->create(['etiqueta' => 'Casa', 'linea' => 'Calle 1', 'predeterminada' => true]);

        $p = $u->pedidos()->create([
            'direccion_id' => $d->id,
            'codigo' => 'PED-TEST',
            'servicio' => 'Lavado',
            'kg' => 3,
            'paso_actual' => 3,
            'estado' => 'pago_confirmado',
            'total' => 10,
            'pendiente_pago' => true,
            'fecha' => now(),
        ]);

        $p->lineas()->create(['nombre' => 'Lavado', 'cantidad' => 3, 'precio_unitario' => 3.5, 'subtotal' => 10.5]);

        $this->actingAs($u)->get('/dashboard')->assertOk();
        $this->actingAs($u)->get('/pedidos')->assertOk();
        $this->actingAs($u)->get('/pagos')->assertOk();
        $this->actingAs($u)->get('/pagos/tarjetas/crear')->assertOk();
        $this->actingAs($u)->get('/direcciones')->assertOk();
        $this->actingAs($u)->get('/direcciones/crear')->assertOk();
        $this->actingAs($u)->get('/direcciones/'.$u->direcciones()->first()->id.'/editar')->assertOk();
        $this->actingAs($u)->get('/user/profile')->assertOk();
    }

    public function test_cliente_can_create_pedido_from_web_with_quantities(): void
    {
        Role::findOrCreate('cliente');

        $u = User::factory()->create();
        $u->assignRole('cliente');

        $dir = $u->direcciones()->create(['etiqueta' => 'Casa', 'linea' => 'Calle 1', 'predeterminada' => true]);

        Livewire::actingAs($u)
            ->test(PedidoActivo::class)
            ->set('servicioSel', 'Canasto de ropa')
            ->set('cantidadSel', 2)
            ->call('agregarAlCarrito')
            ->set('servicioSel', 'Frazada 1 plaza')
            ->set('cantidadSel', 1)
            ->call('agregarAlCarrito')
            ->set('dia', now()->format('Y-m-d'))
            ->set('horario', '8:00 – 10:00')
            ->set('direccionId', $dir->id)
            ->call('guardarPedido')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pedidos', [
            'user_id' => $u->id,
            'estado' => 'en_espera',
            'paso_actual' => 1,
            'pendiente_pago' => true,
            'total' => 800,
        ]);

        $this->assertDatabaseHas('items_pedido', ['nombre' => 'Canasto de ropa', 'cantidad' => 2]);
        $this->assertDatabaseHas('items_pedido', ['nombre' => 'Frazada 1 plaza', 'cantidad' => 1]);
    }

    public function test_cliente_can_edit_confirmed_pedido_lines(): void
    {
        $u = User::factory()->create();
        $dir = $u->direcciones()->create(['etiqueta' => 'Casa', 'linea' => 'Calle 1', 'predeterminada' => true]);

        $pedido = $u->pedidos()->create([
            'direccion_id' => $dir->id,
            'codigo' => 'PED-EDIT',
            'servicio' => '1× Canasto de ropa',
            'kg' => 0,
            'paso_actual' => 1,
            'estado' => 'en_espera',
            'total' => 250,
            'pendiente_pago' => true,
            'fecha' => now(),
        ]);
        $pedido->lineas()->create(['nombre' => 'Canasto de ropa', 'cantidad' => 1, 'precio_unitario' => 250, 'subtotal' => 250]);

        Livewire::actingAs($u)
            ->test(PedidoActivo::class)
            ->call('editarPedido')
            ->assertSet('editando', true)
            ->call('sumar', 'Canasto de ropa')
            ->call('sumar', 'Canasto de ropa')
            ->set('dia', now()->format('Y-m-d'))
            ->set('horario', '8:00 – 10:00')
            ->set('direccionId', $dir->id)
            ->call('guardarPedido')
            ->assertHasNoErrors();

        $pedido->refresh();
        $this->assertSame(600.0, (float) $pedido->total);
        $this->assertSame(1, $pedido->lineas()->count());
        $this->assertDatabaseHas('items_pedido', ['pedido_id' => $pedido->id, 'nombre' => 'Canasto de ropa', 'cantidad' => 3]);
    }

    public function test_creating_pedido_notifies_admins(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('cliente');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $u = User::factory()->create();
        $u->assignRole('cliente');
        $dir = $u->direcciones()->create(['etiqueta' => 'Casa', 'linea' => 'Calle 1', 'predeterminada' => true]);

        Livewire::actingAs($u)
            ->test(PedidoActivo::class)
            ->set('servicioSel', 'Canasto de ropa')
            ->set('cantidadSel', 1)
            ->call('agregarAlCarrito')
            ->set('dia', now()->format('Y-m-d'))
            ->set('horario', '8:00 – 10:00')
            ->set('direccionId', $dir->id)
            ->call('guardarPedido')
            ->assertHasNoErrors();

        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count());
    }

    public function test_efectivo_payment_confirms_pedido(): void
    {
        $u = User::factory()->create();
        $pedido = $u->pedidos()->create([
            'codigo' => 'PED-PAGO',
            'servicio' => 'Lavado',
            'kg' => 3,
            'paso_actual' => 1,
            'estado' => 'en_espera',
            'total' => 250,
            'pendiente_pago' => true,
            'fecha' => now(),
        ]);

        Livewire::actingAs($u)
            ->test(ModalPago::class, ['pedido' => $pedido])
            ->set('metodo', 'efectivo')
            ->call('confirmar')
            ->assertSet('vista', 'exito');

        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $pedido->id,
            'metodo' => 'efectivo',
            'estado' => 'pendiente',
        ]);

        $this->assertFalse($pedido->fresh()->pendiente_pago);
    }

    public function test_admin_can_advance_pedido_states(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('cliente');

        $cliente = User::factory()->create();
        $cliente->assignRole('cliente');

        $pedido = $cliente->pedidos()->create([
            'codigo' => 'PED-ADMIN',
            'servicio' => 'Lavado',
            'kg' => 3,
            'paso_actual' => 3,
            'estado' => 'pago_confirmado',
            'total' => 10,
            'fecha' => now(),
        ]);

        $this->actingAs($cliente)->get('/admin/pedidos')->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get('/admin/pedidos')->assertOk();
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin/pedidos');

        $this->actingAs($admin)
            ->post(route('admin.pedidos.avanzar', $pedido))
            ->assertRedirect();

        $pedido->refresh();
        $this->assertSame(4, $pedido->paso_actual);
        $this->assertSame('recogida', $pedido->estado);

        $this->actingAs($admin)
            ->post(route('admin.pedidos.retroceder', $pedido))
            ->assertRedirect();

        $this->assertSame(3, $pedido->fresh()->paso_actual);
    }
}
