<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('pedidos.index', [
            'enCurso' => $user->pedidos()->where('estado', '!=', 'entregado')->latest()->first(),
            'entregados' => $user->pedidos()->where('estado', 'entregado')->latest()->get(),
        ]);
    }

    public function repetir(Request $request, Pedido $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        $nuevo = $pedido->replicate();
        $nuevo->codigo = 'PED-'.strtoupper(uniqid());
        $nuevo->estado = Pedido::PASOS[1];
        $nuevo->paso_actual = 1;
        $nuevo->pendiente_pago = true;
        $nuevo->recogido_en = null;
        $nuevo->entrega_estimada = null;
        $nuevo->fecha = now();
        $nuevo->save();

        foreach ($pedido->lineas as $linea) {
            $nuevo->lineas()->create($linea->only(['nombre', 'cantidad', 'precio_unitario', 'subtotal']));
        }

        return redirect()->route('dashboard')->with('status', 'Pedido repetido. Agenda tu recogida.');
    }
}
