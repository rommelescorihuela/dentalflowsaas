<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivity extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = [];

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