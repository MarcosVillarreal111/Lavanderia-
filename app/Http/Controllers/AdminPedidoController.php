<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminPedidoController extends Controller
{
    public function index(): View
    {
        $pedidos = Pedido::with(['user', 'direccion', 'lineas', 'pago'])
            ->vigente()
            ->latest('created_at')
            ->get();

        $columnas = collect(Pedido::PASOS)->mapWithKeys(function (string $estado, int $paso) use ($pedidos) {
            return [$paso => [
                'estado' => $estado,
                'titulo' => Pedido::PASO_TEXTO[$paso],
                'pedidos' => $pedidos->where('paso_actual', $paso)->values(),
            ]];
        });

        return view('admin.pedidos.index', [
            'columnas' => $columnas,
            'total' => $pedidos->count(),
        ]);
    }

    public function avanzar(Pedido $pedido): RedirectResponse
    {
        $anterior = $pedido->estado_texto;

        $pedido->avanzar();

        $this->registrarAuditoria($pedido, 'avanzar', $anterior);

        return back()->with('status', "Pedido #{$pedido->codigo} marcado como \"{$pedido->estado_texto}\".");
    }

    public function retroceder(Pedido $pedido): RedirectResponse
    {
        $anterior = $pedido->estado_texto;

        $pedido->retroceder();

        $this->registrarAuditoria($pedido, 'retroceder', $anterior);

        return back()->with('status', "Pedido #{$pedido->codigo} devuelto a \"{$pedido->estado_texto}\".");
    }

    public function archivar(Pedido $pedido): RedirectResponse
    {
        $pedido->update([
            'archivado' => true,
            'archivado_en' => now(),
        ]);

        $this->registrarAuditoria($pedido, 'archivar', $pedido->estado_texto);

        return back()->with('status', "Pedido #{$pedido->codigo} mandado a registros.");
    }

    private function registrarAuditoria(Pedido $pedido, string $accion, string $estadoAnterior): void
    {
        $pedido->loadMissing('user', 'pago');

        $factura = $pedido->pago ? 'FAC-'.str_pad((string) $pedido->pago->id, 6, '0', STR_PAD_LEFT) : null;

        activity('pedido')
            ->causedBy(auth()->user())
            ->performedOn($pedido)
            ->withProperties([
                'accion' => $accion,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $pedido->estado_texto,
                'pedido' => $pedido->codigo,
                'factura' => $factura,
                'cliente_numero' => $pedido->user_id,
                'cliente_nombre' => $pedido->user?->name,
                'cliente_email' => $pedido->user?->email,
                'cliente_whatsapp' => $pedido->user?->whatsapp,
                'qr' => 'PEDIDO:'.$pedido->codigo.'|CLIENTE:'.$pedido->user_id.'|TOTAL:'.$pedido->total,
            ])
            ->log("Pedido #{$pedido->codigo}: {$estadoAnterior} → {$pedido->estado_texto}");
    }
}
