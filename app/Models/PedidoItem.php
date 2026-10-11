<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'pedido_items';

    public $timestamps = false;

    protected $fillable = ['pedido_id', 'producto_id', 'nombre_producto', 'precio_unitario', 'cantidad'];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto()
    {
        // El producto puede estar eliminado lógicamente; el item conserva copia.
        return $this->belongsTo(Producto::class)->withTrashed();
    }

    public function subtotal(): float
    {
        return (float) $this->precio_unitario * $this->cantidad;
    }
}
