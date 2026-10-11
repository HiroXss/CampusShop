<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_CONFIRMADO = 'confirmado';

    public const ESTADO_ENTREGADO = 'entregado';

    public const ESTADO_RECHAZADO = 'rechazado';

    public const ESTADO_CANCELADO = 'cancelado';

    public const PAGO_EFECTIVO = 'efectivo';

    public const PAGO_TRANSFERENCIA = 'transferencia';

    protected $fillable = [
        'comprador_id', 'emprendimiento_id', 'estado', 'metodo_pago', 'nota',
        'motivo', 'total', 'confirmado_at', 'entregado_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'confirmado_at' => 'datetime',
        'entregado_at' => 'datetime',
    ];

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function emprendimiento()
    {
        return $this->belongsTo(Emprendimiento::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function resena()
    {
        return $this->hasOne(Resena::class);
    }

    public function escribioResena(): bool
    {
        return $this->resena()->exists();
    }
}
