<?php

namespace App\Enums;

enum ShortLinkStatus: string
{
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo',
            self::DISABLED => 'Deshabilitado',
            self::EXPIRED => 'Expirado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::DISABLED => 'gray',
            self::EXPIRED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-check-circle',
            self::DISABLED => 'heroicon-o-pause-circle',
            self::EXPIRED => 'heroicon-o-exclamation-triangle',
        };
    }

    public static function toSelectArray(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::DISABLED->value => self::DISABLED->label(),
            self::EXPIRED->value => self::EXPIRED->label(),
        ];
    }

    public static function fromValue(?string $value): ?self
    {
        return match ($value) {
            self::ACTIVE->value => self::ACTIVE,
            self::DISABLED->value => self::DISABLED,
            self::EXPIRED->value => self::EXPIRED,
            default => null,
        };
    }
}
