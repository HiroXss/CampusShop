<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_DESCARTADO = 'descartado';
    public const ESTADO_ACEPTADO = 'aceptado';

    protected $fillable = [
        'user_id', 'reportable_type', 'reportable_id', 'motivo', 'estado',
        'revisado_por', 'revisado_at',
    ];

    protected $casts = [
        'revisado_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica: apunta a un Producto o a un Emprendimiento.
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    public function scopePendientes($q)
    {
        return $q->where('estado', self::ESTADO_PENDIENTE);
    }
}