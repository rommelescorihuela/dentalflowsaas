<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToClinic;
use App\Traits\LogsTenantActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use BelongsToClinic, HasFactory, LogsTenantActivity;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'user_id',
        'rating',
        'comment',
        'featured',
    ];

    protected $casts = [
        'rating' => 'integer',
        'featured' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeHighRating($query, int $min = 4)
    {
        return $query->where('rating', '>=', $min);
    }
}
