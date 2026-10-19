<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp',
        'notif_whatsapp_lista',
        'notif_en_camino',
        'notif_promos',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'initials',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notif_whatsapp_lista' => 'boolean',
            'notif_en_camino' => 'boolean',
            'notif_promos' => 'boolean',
        ];
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class);
    }

    public function direccionPredeterminada(): HasOne
    {
        return $this->hasOne(Direccion::class)->where('predeterminada', true);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function tarjetas(): HasMany
    {
        return $this->hasMany(Tarjeta::class);
    }

    public function tarjetaPredeterminada(): HasOne
    {
        return $this->hasOne(Tarjeta::class)->where('predeterminada', true);
    }
}
