<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';

    public $timestamps = false;

    protected $fillable = ['pedido_id', 'comprador_id', 'emprendimiento_id', 'puntuacion', 'comentario'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function emprendimiento()
    {
        return $this->belongsTo(Emprendimiento::class);
    }
}
