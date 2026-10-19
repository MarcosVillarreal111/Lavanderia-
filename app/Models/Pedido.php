<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    public const PASOS = [
        1 => 'en_espera',
        2 => 'proceso_pago',
        3 => 'pago_confirmado',
        4 => 'recogida',
        5 => 'en_proceso',
        6 => 'en_camino',
        7 => 'entregado',
    ];

    public const PASO_TEXTO = [
        1 => 'En espera de confirmación de pedido',
        2 => 'Proceso de pago',
        3 => 'Pago confirmado',
        4 => 'En recogida de su pedido',
        5 => 'En proceso',
        6 => 'Pedido en camino',
        7 => 'Pedido entregado',
    ];

    public const PASO_MENSAJE = [
        1 => 'Estamos revisando tu pedido. En breve lo confirmamos.',
        2 => 'Tu pedido fue confirmado. Realiza el pago para continuar.',
        3 => '¡Pago confirmado! Coordinamos la recogida de tu pedido.',
        4 => 'Estamos recogiendo tu pedido.',
        5 => '¡Gracias por esperar! Estamos trabajando para ti.',
        6 => 'Tu pedido va en camino.',
        7 => '¡Tu pedido fue entregado! Gracias por tu preferencia.',
    ];

    protected $fillable = [
        'user_id',
        'direccion_id',
        'codigo',
        'servicio',
        'kg',
        'paso_actual',
        'estado',
        'total',
        'express',
        'pendiente_pago',
        'fecha',
        'recogido_en',
        'entrega_estimada',
        'archivado',
        'archivado_en',
    ];

    protected function casts(): array
    {
        return [
            'kg' => 'decimal:2',
            'total' => 'decimal:2',
            'express' => 'boolean',
            'pendiente_pago' => 'boolean',
            'archivado' => 'boolean',
            'fecha' => 'date',
            'recogido_en' => 'datetime',
            'entrega_estimada' => 'date',
            'archivado_en' => 'datetime',
        ];
    }

    public function scopeVigente($query)
    {
        return $query->where('archivado', false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    public function getEstadoTextoAttribute(): string
    {
        return self::PASO_TEXTO[$this->paso_actual] ?? ucfirst($this->estado);
    }

    public function getPasoMensajeAttribute(): string
    {
        return self::PASO_MENSAJE[$this->paso_actual] ?? '';
    }

    public function getTotalPasosAttribute(): int
    {
        return count(self::PASO_TEXTO);
    }

    public function avanzar(): void
    {
        $paso = min(count(self::PASO_TEXTO), $this->paso_actual + 1);

        $this->paso_actual = $paso;
        $this->estado = self::PASOS[$paso];

        if ($paso >= 4 && $this->recogido_en === null) {
            $this->recogido_en = now();
        }

        if ($paso >= 3) {
            $this->pendiente_pago = false;
            $this->pago()->where('estado', 'en_revision')->update(['estado' => 'confirmado']);
        }

        $this->save();
    }

    public function retroceder(): void
    {
        $paso = max(1, $this->paso_actual - 1);

        $this->paso_actual = $paso;
        $this->estado = self::PASOS[$paso];

        $this->save();
    }

    public function getSiguientePasoTextoAttribute(): ?string
    {
        return self::PASO_TEXTO[$this->paso_actual + 1] ?? null;
    }
}
