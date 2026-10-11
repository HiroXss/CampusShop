<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    public const TIPO_PRODUCTO = 'producto';

    public const TIPO_SERVICIO = 'servicio';

    public const ESTADO_DISPONIBLE = 'disponible';

    public const ESTADO_AGOTADO = 'agotado';

    public const ESTADO_INACTIVO = 'inactivo';

    protected $fillable = [
        'emprendimiento_id', 'categoria_id', 'nombre', 'descripcion', 'tipo',
        'precio', 'stock', 'estado', 'oculto',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'oculto' => 'boolean',
    ];

    /* Relaciones */

    public function emprendimiento()
    {
        return $this->belongsTo(Emprendimiento::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function conversaciones()
    {
        return $this->hasMany(Conversacion::class);
    }

    /* Visibilidad (RN-12, RN-03) */

    /**
     * Producto visible = disponible o agotado, no oculto, no eliminado y de un
     * emprendimiento visible y no pausado.
     */
    public function isVisible(): bool
    {
        return in_array($this->estado, [self::ESTADO_DISPONIBLE, self::ESTADO_AGOTADO])
            && ! $this->oculto
            && ! $this->trashed()
            && $this->emprendimiento?->isOperativo();
    }

    /**
     * Producto pedible = visible, disponible y con stock suficiente.
     */
    public function isPedible(): bool
    {
        return $this->isVisible()
            && $this->estado === self::ESTADO_DISPONIBLE
            && ($this->stock === null || $this->stock > 0);
    }

    public function scopeVisibles(Builder $q): Builder
    {
        $tabla = $q->getModel()->getTable();

        return $q->whereIn("{$tabla}.estado", [self::ESTADO_DISPONIBLE, self::ESTADO_AGOTADO])
            ->where("{$tabla}.oculto", false)
            ->whereHas('emprendimiento', fn (Builder $e) => $e
                ->where('estado', Emprendimiento::ESTADO_APROBADO)
                ->where('oculta', false));
    }

    public function scopePedibles(Builder $q): Builder
    {
        $tabla = $q->getModel()->getTable();

        return $q->visibles()
            ->where("{$tabla}.estado", self::ESTADO_DISPONIBLE)
            ->where(fn (Builder $p) => $p->whereNull("{$tabla}.stock")->orWhere("{$tabla}.stock", '>', 0));
    }
}
