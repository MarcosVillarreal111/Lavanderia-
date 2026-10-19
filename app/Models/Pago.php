<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'user_id',
        'pedido_id',
        'monto',
        'metodo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function getMetodoTextoAttribute(): string
    {
        return match ($this->metodo) {
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'efectivo' => 'Efectivo',
            default => ucfirst($this->metodo),
        };
    }

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            'confirmado', 'completado' => 'Confirmado',
            'en_revision' => 'En revisión',
            'pendiente' => 'Pago al recibir',
            default => ucfirst($this->estado),
        };
    }
}
