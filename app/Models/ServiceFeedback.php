<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use App\Traits\LogsTenantActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeedback extends Model
{
    use BelongsToClinic, HasFactory, LogsTenantActivity;

    protected $table = 'service_feedbacks';

    protected $fillable = [
        'clinic_id',
        'appointment_id',
        'patient_id',
        'user_id',
        'rating',
        'comment',
        'category',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
