<?php

namespace App\Livewire;

use App\Models\Pedido;
use App\Models\User;
use App\Notifications\NuevoPedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class PedidoActivo extends Component
{
    public $pedido;

    public array $carrito = [];

    public string $servicioSel = '';

    public int $cantidadSel = 1;

    public bool $creando = false;

    public bool $editando = false;

    public string $dia = '';

    public string $horario = '';

    public ?int $direccionId = null;

    public bool $express = false;

    public bool $agendada = false;

    public $anteriores;

    public function mount(?Pedido $pedido = null)
    {
        $this->pedido = $pedido ?? Auth::user()->pedidos()->where('estado', '!=', 'entregado')->latest()->first();
        $this->direccionId = Auth::user()->direccionPredeterminada?->id;
        $this->anteriores = Auth::user()->pedidos()->where('estado', 'entregado')->latest()->take(5)->get();

        if ($this->pedido) {
            $this->agendada = (bool) $this->pedido->recogido_en;
        }
    }

    #[On('pago-confirmado')]
    public function actualizarTrasPago(): void
    {
        $this->pedido = $this->pedido?->fresh();
    }

    public function refrescarEstado(): void
    {
        if (! $this->pedido) {
            return;
        }

        $this->pedido = $this->pedido->fresh(['lineas', 'pago', 'direccion']);
        $this->anteriores = Auth::user()->pedidos()->where('estado', 'entregado')->latest()->take(5)->get();
    }

    public function agregarAlCarrito(): void
    {
        $this->validate(
            ['servicioSel' => 'required', 'cantidadSel' => 'required|integer|min:1|max:50'],
            ['servicioSel.required' => 'Elige un servicio.', 'cantidadSel.min' => 'La cantidad mínima es 1.']
        );

        $this->carrito[$this->servicioSel] = [
            'nombre' => $this->servicioSel,
            'cantidad' => ($this->carrito[$this->servicioSel]['cantidad'] ?? 0) + $this->cantidadSel,
        ];

        $this->reset('servicioSel');
        $this->cantidadSel = 1;
    }

    public function sumar(string $key): void
    {
        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        }
    }

    public function restar(string $key): void
    {
        if (! isset($this->carrito[$key])) {
            return;
        }

        $this->carrito[$key]['cantidad']--;

        if ($this->carrito[$key]['cantidad'] < 1) {
            unset($this->carrito[$key]);
        }
    }

    public function quitar(string $key): void
    {
        unset($this->carrito[$key]);
    }

    public function iniciarNuevoPedido(): void
    {
        $this->carrito = [];
        $this->creando = true;
        $this->editando = false;
        $this->reset('dia', 'horario');
        $this->agendada = false;
    }

    public function editarPedido(): void
    {
        if (! $this->pedido || $this->pedido->estado === 'entregado') {
            return;
        }

        $this->carrito = $this->pedido->lineas
            ->mapWithKeys(fn ($l) => [$l->nombre => ['nombre' => $l->nombre, 'cantidad' => (int) $l->cantidad]])
            ->all();

        $this->direccionId = $this->pedido->direccion_id ?? $this->direccionId;
        $this->express = (bool) $this->pedido->express;
        $this->editando = true;
        $this->creando = false;
        $this->reset('dia', 'horario');
    }

    public function cancelar(): void
    {
        $this->carrito = [];
        $this->creando = false;
        $this->editando = false;
        $this->reset('dia', 'horario', 'servicioSel');
        $this->cantidadSel = 1;
        $this->agendada = (bool) $this->pedido?->recogido_en;
    }

    public function guardarPedido(): void
    {
        $this->validate([
            'carrito' => 'required|array|min:1',
            'dia' => 'required',
            'horario' => 'required',
            'direccionId' => 'required|exists:direcciones,id',
        ], ['carrito.required' => 'Agrega al menos un servicio al pedido.']);

        $lineas = $this->getLineasProperty();

        if ($this->editando && $this->pedido) {
            $this->pedido->update([
                'direccion_id' => $this->direccionId,
                'express' => $this->express,
                'servicio' => $this->resumenServicios($lineas),
                'total' => $this->total,
                'entrega_estimada' => now()->addDays($this->express ? 0 : 2),
            ]);

            $this->pedido->lineas()->delete();
            $this->guardarLineas($this->pedido, $lineas);
        } else {
            $this->crearPedido($lineas);
        }

        $this->carrito = [];
        $this->creando = false;
        $this->editando = false;
        $this->agendada = true;
        $this->dispatch('pedido-guardado');
    }

    private function crearPedido(array $lineas): void
    {
        $this->pedido = Auth::user()->pedidos()->create([
            'direccion_id' => $this->direccionId,
            'codigo' => $this->codigoUnico(),
            'servicio' => $this->resumenServicios($lineas),
            'kg' => 0,
            'paso_actual' => 1,
            'estado' => Pedido::PASOS[1],
            'total' => $this->total,
            'express' => $this->express,
            'pendiente_pago' => true,
            'fecha' => now(),
            'entrega_estimada' => now()->addDays($this->express ? 0 : 2),
        ]);

        $this->guardarLineas($this->pedido, $lineas);

        $admins = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'admin'))
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NuevoPedido($this->pedido->fresh()->load('user')));
        }
    }

    private function guardarLineas(Pedido $pedido, array $lineas): void
    {
        foreach ($lineas as $linea) {
            $pedido->lineas()->create([
                'nombre' => $linea['nombre'],
                'cantidad' => $linea['cantidad'],
                'precio_unitario' => $linea['precio_unitario'],
                'subtotal' => $linea['subtotal'],
            ]);
        }
    }

    private function resumenServicios(array $lineas): string
    {
        return collect($lineas)
            ->map(fn ($l) => $l['cantidad'].'× '.$l['nombre'])
            ->implode(' + ');
    }

    public function agendar()
    {
        if (! $this->pedido) {
            return;
        }

        $this->validate([
            'dia' => 'required',
            'horario' => 'required',
            'direccionId' => 'required|exists:direcciones,id',
        ]);

        $this->pedido->update([
            'direccion_id' => $this->direccionId,
            'express' => $this->express,
            'recogido_en' => now(),
            'entrega_estimada' => now()->addDays($this->express ? 0 : 2),
        ]);

        $this->agendada = true;
    }

    public function getLineasProperty(): array
    {
        return collect($this->carrito)
            ->map(function ($l, $key) {
                $unidad = $this->precioUnitario($l['nombre'], $l['cantidad']);

                return [
                    'key' => $key,
                    'nombre' => $l['nombre'],
                    'cantidad' => $l['cantidad'],
                    'precio_unitario' => $unidad,
                    'subtotal' => round($unidad * $l['cantidad'], 2),
                ];
            })
            ->values()
            ->all();
    }

    public function getTotalProperty(): float
    {
        $subtotal = collect($this->getLineasProperty())->sum('subtotal');

        return round($this->express ? $subtotal * 1.5 : $subtotal, 2);
    }

    private function precioUnitario(string $nombre, int $cantidad): float
    {
        if ($nombre === 'Canasto de ropa' && $cantidad >= 3) {
            return 200.0;
        }

        foreach (config('servicios') as $categoria) {
            foreach ($categoria['items'] as $item) {
                if ($item['nombre'] === $nombre) {
                    return (float) $item['precio'];
                }
            }
        }

        return 0.0;
    }

    private function codigoUnico(): string
    {
        do {
            $codigo = 'PED-'.strtoupper(Str::random(5));
        } while (Pedido::where('codigo', $codigo)->exists());

        return $codigo;
    }

    public function render()
    {
        return view('livewire.pedido-activo', [
            'direcciones' => Auth::user()->direcciones,
            'dias' => collect(range(0, 4))->map(fn ($i) => now()->addDays($i)),
            'horarios' => ['8:00 – 10:00', '10:00 – 12:00', '16:00 – 18:00', '18:00 – 20:00'],
            'catalogo' => config('servicios'),
        ]);
    }
}
