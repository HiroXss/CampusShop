<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    public function emprendimientos()
    {
        return $this->hasMany(Emprendimiento::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
