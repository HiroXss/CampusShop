<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emprendimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'emprendimientos';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADO = 'aprobado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_PAUSADO = 'pausado';
    public const ESTADO_SUSPENDIDO = 'suspendido';

    protected $fillable = [
        'user_id', 'categoria_id', 'nombre', 'slug', 'descripcion', 'logo', 'banner',
        'whatsapp', 'instagram', 'estado', 'motivo_rechazo', 'revisado_por', 'revisado_at',
        'destacado', 'oculta',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'oculta' => 'boolean',
        'revisado_at' => 'datetime',
    ];

    /**
     * Enlaces públicos por slug (SRS 8.3: /emprendimientos/{slug}).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        // Genera el slug único a partir del nombre al crear.
        static::saving(function (Emprendimiento $emprendimiento) {
            if (empty($emprendimiento->slug) && $emprendimiento->nombre) {
                $emprendimiento->slug = self::unicoSlug($emprendimiento->nombre);
            }
        });
    }

    public static function unicoSlug(string $nombre, ?int $ignorarId = null): string
    {
        $base = \Illuminate\Support\Str::slug($nombre);
        $slug = $base;
        $i = 1;
        while (self::query()->where('slug', $slug)->where('id', '!=', $ignorarId)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /* Relaciones */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function conversaciones()
    {
        return $this->hasMany(Conversacion::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class);
    }

    /* Visibilidad (RN-12, RN-02) */

    /**
     * Emprendimiento visible = aprobado, no oculto y no eliminado.
     */
    public function isVisible(): bool
    {
        return $this->estado === self::ESTADO_APROBADO && ! $this->oculta && ! $this->trashed();
    }

    /**
     * Satisfecha = visible y además no pausado (aparece en catálogo y recibe pedidos).
     */
    public function isOperativo(): bool
    {
        return $this->isVisible() && $this->estado === self::ESTADO_APROBADO;
    }

    public function scopeVisibles(Builder $q): Builder
    {
        return $q->where('estado', self::ESTADO_APROBADO)->where('oculta', false);
    }

    public function scopeVisiblesOperativos(Builder $q): Builder
    {
        return $q->visibles();
    }

    /* Calificación */

    public function getPromedioResenasAttribute(): ?float
    {
        $valor = $this->attributes['resenas_avg_puntuacion'] ?? null;

        return $valor === null ? null : round((float) $valor, 1);
    }

    public function getResenasCountAttribute($value = null): int
    {
        if ($value !== null) {
            return (int) $value;
        }

        return $this->resenas()->count();
    }
}