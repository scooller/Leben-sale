<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case AUTHORIZED = 'authorized';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case EXPIRED = 'expired';
    case PENDING_APPROVAL = 'pending_approval'; // Para pagos manuales

    /**
     * Obtener nombre legible
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PROCESSING => 'Procesando',
            self::AUTHORIZED => 'Autorizado',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Fallido',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Reembolsado',
            self::PARTIALLY_REFUNDED => 'Reembolso Parcial',
            self::EXPIRED => 'Expirado',
            self::PENDING_APPROVAL => 'Pendiente de Aprobación',
        };
    }

    /**
     * Obtener color para badges en Filament
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'warning',
            self::AUTHORIZED => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'gray',
            self::REFUNDED, self::PARTIALLY_REFUNDED => 'warning',
            self::EXPIRED => 'gray',
            self::PENDING_APPROVAL => 'warning',
        };
    }

    /**
     * Verificar si el pago está completado
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::COMPLETED, self::AUTHORIZED]);
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPending(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING, self::PENDING_APPROVAL]);
    }

    /**
     * Verificar si el pago falló
     */
    public function isFailed(): bool
    {
        return in_array($this, [self::FAILED, self::CANCELLED, self::EXPIRED]);
    }

    /**
     * Verificar si se puede reembolsar
     */
    public function canBeRefunded(): bool
    {
        return in_array($this, [self::COMPLETED, self::AUTHORIZED]);
    }

    /**
     * Verificar si se puede aprobar manualmente
     */
    public function canBeApproved(): bool
    {
        return $this === self::PENDING_APPROVAL;
    }

    /**
     * Obtener array para Select de Filament
     */
    public static function toSelectArray(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::PROCESSING->value => self::PROCESSING->label(),
            self::AUTHORIZED->value => self::AUTHORIZED->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::FAILED->value => self::FAILED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
            self::REFUNDED->value => self::REFUNDED->label(),
            self::PARTIALLY_REFUNDED->value => self::PARTIALLY_REFUNDED->label(),
            self::EXPIRED->value => self::EXPIRED->label(),
            self::PENDING_APPROVAL->value => self::PENDING_APPROVAL->label(),
        ];
    }

    public static function fromValue(?string $value): ?self
    {
        return match ($value) {
            self::PENDING->value => self::PENDING,
            self::PROCESSING->value => self::PROCESSING,
            self::AUTHORIZED->value => self::AUTHORIZED,
            self::COMPLETED->value => self::COMPLETED,
            self::FAILED->value => self::FAILED,
            self::CANCELLED->value => self::CANCELLED,
            self::REFUNDED->value => self::REFUNDED,
            self::PARTIALLY_REFUNDED->value => self::PARTIALLY_REFUNDED,
            self::EXPIRED->value => self::EXPIRED,
            self::PENDING_APPROVAL->value => self::PENDING_APPROVAL,
            default => null,
        };
    }

    /**
     * Obtener ícono para UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::AUTHORIZED, self::COMPLETED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
            self::CANCELLED => 'heroicon-o-no-symbol',
            self::REFUNDED, self::PARTIALLY_REFUNDED => 'heroicon-o-arrow-uturn-left',
            self::EXPIRED => 'heroicon-o-exclamation-triangle',
            self::PENDING_APPROVAL => 'heroicon-o-hand-raised',
        };
    }
}
