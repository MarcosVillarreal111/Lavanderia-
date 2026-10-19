<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarjeta extends Model
{
    protected $fillable = [
        'user_id',
        'marca',
        'ultimos',
        'vence',
        'predeterminada',
    ];

    protected function casts(): array
    {
        return [
            'predeterminada' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
