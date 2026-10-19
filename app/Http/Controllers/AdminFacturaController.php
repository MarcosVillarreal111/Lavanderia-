<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\View\View;

class AdminFacturaController extends Controller
{
    public function show(Pedido $pedido): View
    {
        $pedido->load(['user', 'direccion', 'lineas', 'pago']);

        return view('admin.facturas.show', [
            'pedido' => $pedido,
            'factura' => $pedido->pago ? 'FAC-'.str_pad((string) $pedido->pago->id, 6, '0', STR_PAD_LEFT) : 'SIN-PAGO',
        ]);
    }
}
