<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    use BelongsToClinic;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'batch_uuid',
        'clinic_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'referrer',
    ];

    /**
     * Audit logs are immutable: block updates and deletes from the app.
     */
    protected static function booted(): void
    {
        static::updating(function () {
            return false;
        });

        static::deleting(function () {
            return false;
        });

        static::creating(function (Activity $activity) {
            $activity->clinic_id ??= $activity->resolveClinicId();
            $activity->fillRequestMetadata();
        });
    }

    protected function resolveClinicId(): ?string
    {
        if (tenancy()->initialized) {
            return tenant('id');
        }

        if (! $this->subject_type || ! $this->subject_id) {
            return null;
        }

        if (is_a($this->subject_type, Clinic::class, true)) {
            return (string) $this->subject_id;
        }

        $subject = new $this->subject_type;

        $clinicId = DB::table($subject->getTable())
            ->where($subject->getKeyName(), $this->subject_id)
            ->value('clinic_id');

        return $clinicId ? (string) $clinicId : null;
    }

    protected function fillRequestMetadata(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $request = request();

        if (! $request) {
            return;
        }

        $this->ip_address ??= $request->ip();
        $this->user_agent ??= (string) $request->userAgent();
        $this->method ??= $request->method();
        $this->url ??= $request->fullUrl();
        $this->referrer ??= (string) $request->header('referer');
    }
}
