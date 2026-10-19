<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class ReciboController extends Controller
{
    public function descargar(Pago $pago)
    {
        abort_unless($pago->user_id === Auth::id(), 403);

        $pago->load('pedido', 'user');

        $lineas = [
            'Espuma · Lavandería a domicilio',
            '==============================',
            'Recibo #'.$pago->id,
            'Fecha: '.$pago->created_at->translatedFormat('j M Y'),
            'Cliente: '.$pago->user->name,
            'Pedido: #'.$pago->pedido->codigo,
            'Método de pago: '.$pago->metodo_texto,
            'Monto: $'.number_format($pago->monto, 2),
            'Estado: '.ucfirst($pago->estado),
            '',
            'Gracias por tu preferencia.',
        ];

        $contenido = implode("\n", $lineas);

        return response()->streamDownload(function () use ($contenido) {
            echo $contenido;
        }, 'recibo-'.$pago->id.'.txt', [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
