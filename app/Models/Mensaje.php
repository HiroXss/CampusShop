<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = ['conversacion_id', 'emisor_id', 'contenido', 'leido_at'];

    protected $casts = [
        'leido_at' => 'datetime',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class);
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }
}