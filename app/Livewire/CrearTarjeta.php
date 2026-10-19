<?php

namespace App\Livewire;

use App\Models\Tarjeta;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.cliente', ['heading' => 'Agregar tarjeta'])]
class CrearTarjeta extends Component
{
    public string $marca = 'Visa';

    public string $numero = '';

    public string $vence = '';

    public string $cvc = '';

    public bool $predeterminada = false;

    protected $rules = [
        'marca' => 'required|in:Visa,Mastercard',
        'numero' => 'required|string|size:16|numeric',
        'vence' => 'required|string|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
        'cvc' => 'required|string|size:3|numeric',
    ];

    protected $messages = [
        'numero.size' => 'El número debe tener 16 dígitos.',
        'numero.numeric' => 'El número solo debe contener dígitos.',
        'vence.regex' => 'El formato debe ser MM/AA.',
        'cvc.size' => 'El CVC debe tener 3 dígitos.',
        'cvc.numeric' => 'El CVC solo debe contener dígitos.',
    ];

    public function guardar()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'marca' => $this->marca,
            'ultimos' => substr($this->numero, -4),
            'vence' => $this->vence,
            'predeterminada' => $this->predeterminada,
        ];

        if ($this->predeterminada) {
            Auth::user()->tarjetas()->update(['predeterminada' => false]);
        }

        Tarjeta::create($data);

        $this->redirectRoute('pagos.index');
    }

    public function render()
    {
        return view('livewire.crear-tarjeta');
    }
}
