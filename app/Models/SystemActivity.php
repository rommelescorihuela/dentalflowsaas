<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivity extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'uuid',
        'clinic_id',
        'user_id',
        'user_type',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'referrer',
        'device',
        'platform',
        'browser',
        'payload',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'payload' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}