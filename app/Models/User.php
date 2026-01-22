<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use App\Traits\BelongsToClinic;
use Spatie\Permission\Traits\HasRoles;


use Filament\Models\Contracts\FilamentUser;

use App\Traits\ActivityLogger;

class User extends Authenticatable implements HasTenants, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToClinic, HasRoles, ActivityLogger;



    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->clinic_id === null || $this->hasRole('super-admin');
        }

        if ($panel->getId() === 'app') {
            return $this->clinic_id !== null;
        }

        return true;
    }

    public function getTenants(Panel $panel): Collection
    {
        return Collection::make([$this->tenant]);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->clinic_id === $tenant->id;
    }

    public function clinic()
    {
        return $this->tenant();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'clinic_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
