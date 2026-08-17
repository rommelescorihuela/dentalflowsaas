<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Activity;
use App\Models\Clinic;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsTenantActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(
                fn (string $eventName): string => ucfirst($eventName).' '.class_basename($this)
            );
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->clinic_id = $this instanceof Clinic
            ? $this->getKey()
            : ($this->clinic_id ?? (tenancy()->initialized ? tenant('id') : null));
    }
}
