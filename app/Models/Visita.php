<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table = 'visitas';

    public $timestamps = false;

    protected $fillable = ['emprendimiento_id', 'producto_id', 'user_id', 'sesion', 'fecha'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function emprendimiento()
    {
        return $this->belongsTo(Emprendimiento::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
