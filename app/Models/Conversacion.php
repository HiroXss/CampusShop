<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasFactory;

    protected $table = 'conversaciones';

    protected $fillable = ['emprendimiento_id', 'comprador_id', 'producto_id'];

    public function emprendimiento()
    {
        return $this->belongsTo(Emprendimiento::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    /**
     * Último mensaje de la conversación.
     */
    public function ultimoMensaje()
    {
        return $this->hasOne(Mensaje::class)->latestOfMany();
    }

    /**
     * Mensajes no leídos por un usuario (los recibidos cuya leido_at está vacío).
     */
    public function mensajesNoLeidos(int $userId)
    {
        return $this->mensajes()
            ->where('emisor_id', '!=', $userId)
            ->whereNull('leido_at');
    }
}
