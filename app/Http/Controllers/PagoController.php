<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pedidoPendiente = $user->pedidos()->where('pendiente_pago', true)->latest()->first();

        return view('pagos.index', [
            'pedidoPendiente' => $pedidoPendiente,
            'saldo' => $pedidoPendiente?->total ?? 0,
            'recibos' => $user->pagos()->with('pedido')->latest()->get(),
            'tarjetas' => $user->tarjetas()->orderByDesc('predeterminada')->get(),
        ]);
    }
}
