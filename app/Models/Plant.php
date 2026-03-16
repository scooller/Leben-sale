<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = [
        'salesforce_product_id',
        'salesforce_proyecto_id',
        'name',
        'product_code',
        'orientacion',
        'programa',
        'programa2',
        'piso',
        'precio_base',
        'precio_lista',
        'superficie_total_principal',
        'superficie_interior',
        'superficie_util',
        'opportunity_id',
        'superficie_terraza',
        'superficie_vendible',
        'cover_image_id',
        'interior_image_id',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'precio_lista' => 'decimal:2',
        'superficie_total_principal' => 'decimal:2',
        'superficie_interior' => 'decimal:2',
        'superficie_util' => 'decimal:2',
        'superficie_terraza' => 'decimal:2',
        'superficie_vendible' => 'decimal:2',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Relación con Proyecto
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'salesforce_proyecto_id', 'salesforce_id');
    }

    public function coverImageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    public function interiorImageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'interior_image_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPrograma($query, string $programa)
    {
        return $query->where('programa', $programa);
    }

    public function scopeByPiso($query, string $piso)
    {
        return $query->where('piso', $piso);
    }

    public function scopeByProgramaPiso($query, string $programa, string $piso)
    {
        return $query->where('programa', $programa)->where('piso', $piso);
    }

    /**
     * Relacion con reservas
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(PlantReservation::class);
    }

    /**
     * Obtener reserva activa actual (si existe)
     */
    public function activeReservation(): HasOne
    {
        return $this->hasOne(PlantReservation::class)
            ->where('status', ReservationStatus::ACTIVE)
            ->where('expires_at', '>', now())
            ->latest();
    }

    /**
     * Verificar si la planta esta reservada actualmente
     */
    public function isReserved(): bool
    {
        return $this->activeReservation()->exists();
    }
}
