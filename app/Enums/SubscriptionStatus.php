<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Trialing => 'En prueba',
            self::Active => 'Activa',
            self::PastDue => 'Pago pendiente',
            self::Suspended => 'Suspendida',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Trialing => 'info',
            self::Active => 'success',
            self::PastDue => 'warning',
            self::Suspended => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function hasAccess(): bool
    {
        return in_array($this, [self::Trialing, self::Active, self::PastDue], true);
    }
}
