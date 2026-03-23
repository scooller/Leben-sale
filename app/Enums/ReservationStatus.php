<?php

declare(strict_types=1);

namespace App\Enums;

enum ReservationStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
    case RELEASED = 'released';

    /**
     * Obtener nombre legible
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activa',
            self::COMPLETED => 'Completada',
            self::EXPIRED => 'Expirada',
            self::RELEASED => 'Liberada',
        };
    }

    /**
     * Obtener color para badges en Filament
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'warning',
            self::COMPLETED => 'success',
            self::EXPIRED => 'gray',
            self::RELEASED => 'info',
        };
    }

    /**
     * Obtener icono para UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-clock',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::EXPIRED => 'heroicon-o-exclamation-triangle',
            self::RELEASED => 'heroicon-o-arrow-uturn-left',
        };
    }

    /**
     * Verificar si la reserva esta activa
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Verificar si la reserva esta resuelta (ya no bloquea la planta)
     */
    public function isResolved(): bool
    {
        return in_array($this, [self::COMPLETED, self::EXPIRED, self::RELEASED]);
    }

    /**
     * Obtener array para Select de Filament
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $status) => [$status->value => $status->label()]
        )->toArray();
    }

    public static function fromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        foreach (self::cases() as $status) {
            if ($status->value === $value) {
                return $status;
            }
        }

        return null;
    }
}
