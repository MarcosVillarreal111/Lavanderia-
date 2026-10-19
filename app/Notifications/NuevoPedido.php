<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevoPedido extends Notification
{
    use Queueable;

    public function __construct(public Pedido $pedido) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pedido_id' => $this->pedido->id,
            'codigo' => $this->pedido->codigo,
            'cliente' => $this->pedido->user?->name,
            'total' => $this->pedido->total,
            'mensaje' => 'Nuevo pedido #'.$this->pedido->codigo.' de '.($this->pedido->user?->name ?? 'un cliente').' por $'.number_format($this->pedido->total, 2),
        ];
    }
}
