<?php

namespace App\Traits;

use App\Models\SystemActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait ActivityLogger
{
    public static function bootActivityLogger()
    {
        static::created(function (Model $model) {
            self::logActivity($model, 'create', null, $model->toArray());
        });

        static::updated(function (Model $model) {
            self::logActivity($model, 'update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            self::logActivity($model, 'delete', $model->toArray(), null);
        });
    }

    protected static function logActivity(Model $model, string $action, ?array $oldValues = [], ?array $newValues = [])
    {
        // Basic filtering for sensitive fields
        $hidden = $model->getHidden();
        $oldValues = $oldValues ? array_diff_key($oldValues, array_flip($hidden)) : null;
        $newValues = $newValues ? array_diff_key($newValues, array_flip($hidden)) : null;

        SystemActivity::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'clinic_id' => session('tenant_id') ?? $model->clinic_id ?? null, // Attempt to capture tenant
            'user_id' => Auth::id(),
            'user_type' => Auth::user() ? class_basename(Auth::user()) : null,
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'description' => ucfirst($action) . ' ' . class_basename($model) . ' #' . $model->getKey(),

            // Request Metadata
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'method' => Request::method(),
            'url' => Request::fullUrl(),
            'referrer' => Request::header('referer'),
            'device' => null, // parsers required for this, keeping simple for now
            'platform' => null,
            'browser' => null,

            // Payloads
            'payload' => Request::all(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
