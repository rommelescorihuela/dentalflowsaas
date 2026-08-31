<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicSetting extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'primary_color',
        'secondary_color',
        'accent_color',
        'dark_mode',
        'landing_title',
        'landing_description',
        'landing_logo',
        'landing_hero_image',
        'landing_services',
        'landing_phone',
        'landing_email',
        'landing_address',
        'landing_facebook',
        'landing_instagram',
        'landing_whatsapp',
        'landing_enabled',
        'email_notifications',
        'appointment_reminders',
        'reminder_hours_before',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'landing_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'appointment_reminders' => 'boolean',
        'reminder_hours_before' => 'integer',
        'landing_services' => 'array',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
