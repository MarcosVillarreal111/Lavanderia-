<?php

namespace App\Livewire;

use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ModalPago extends Component
{
    use WithFileUploads;

    public bool $abierto = false;

    public $pedido;

    public string $metodo = 'tarjeta';

    public string $tarjeta = '';

    public $comprobante;

    public string $vista = 'form';

    public string $mensaje = '';

    public function mount(?Pedido $pedido = null)
    {
        $this->pedido = $pedido ?? Auth::user()->pedidos()->where('pendiente_pago', true)->latest()->first();
        $this->tarjeta = (string) (Auth::user()->tarjetaPredeterminada?->id ?? '');
    }

    #[On('abrir-pago')]
    public function abrir(): void
    {
        $this->pedido = Auth::user()->pedidos()->where('pendiente_pago', true)->latest()->first();
        $this->vista = 'form';
        $this->resetValidation();
        $this->reset('comprobante');
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
        $this->vista = 'form';
    }

    public function confirmar(): void
    {
        $this->validate([
            'metodo' => 'required|in:tarjeta,transferencia,efectivo',
        ]);

        if (! $this->pedido) {
            return;
        }

        if ($this->metodo === 'tarjeta') {
            $this->validate(
                ['tarjeta' => 'required'],
                ['tarjeta.required' => 'Selecciona una tarjeta para pagar.']
            );
        }

        if ($this->metodo === 'transferencia') {
            $this->validate(
                ['comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096'],
                [
                    'comprobante.required' => 'Adjunta tu comprobante de transferencia.',
                    'comprobante.mimes' => 'El comprobante debe ser JPG, PNG o PDF.',
                    'comprobante.max' => 'El comprobante no debe pesar más de 4 MB.',
                ]
            );
        }

        $estado = match ($this->metodo) {
            'tarjeta' => 'confirmado',
            'transferencia' => 'en_revision',
            default => 'pendiente',
        };

        $rutaComprobante = $this->comprobante
            ? $this->comprobante->store('comprobantes', 'public')
            : null;

        Pago::create([
            'user_id' => Auth::id(),
            'pedido_id' => $this->pedido->id,
            'monto' => $this->pedido->total,
            'metodo' => $this->metodo,
            'estado' => $estado,
            'comprobante' => $rutaComprobante,
        ]);

        $this->pedido->update(['pendiente_pago' => false]);

        $this->mensaje = match ($this->metodo) {
            'tarjeta' => 'El banco confirmó tu pago. ¡Listo!',
            'transferencia' => 'Recibimos tu comprobante. Confirmaremos tu pago en breve.',
            'efectivo' => 'Pedido confirmado. Pagarás en efectivo al recibir tu ropa.',
        };

        $this->vista = 'exito';
        $this->dispatch('pago-confirmado');
    }

    public function render()
    {
        return view('livewire.modal-pago', [
            'tarjetas' => Auth::user()->tarjetas->map(fn ($t) => [
                'id' => (string) $t->id,
                'marca' => $t->marca,
                'ult' => $t->ultimos,
                'vence' => $t->vence,
            ])->values(),
        ]);
    }
}
