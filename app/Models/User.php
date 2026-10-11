<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'program',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* Roles y estado (RF-05, RNF-01) */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN && (bool) $this->is_active;
    }

    /* Relaciones */

    public function emprendimiento()
    {
        return $this->hasOne(Emprendimiento::class)->withTrashed();
    }

    public function pedidosComoComprador()
    {
        return $this->hasMany(Pedido::class, 'comprador_id');
    }

    public function conversaciones()
    {
        return $this->hasMany(Conversacion::class, 'comprador_id');
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    /* Ayudas de tienda (RNF-01) */

    public function tieneEmprendimientoActivo(): bool
    {
        return (bool) $this->emprendimiento?->isVisible();
    }

    public function emprendimientoGestionable(): ?Emprendimiento
    {
        $e = $this->emprendimiento;
        if ($e && in_array($e->estado, [Emprendimiento::ESTADO_APROBADO, Emprendimiento::ESTADO_PAUSADO])) {
            return $e;
        }

        return null;
    }
}