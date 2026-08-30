<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory;

    protected $fillable = ['email', 'name', 'locale', 'timezone'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }

    /** @return HasMany<Pregnancy, $this> */
    public function pregnancies(): HasMany
    {
        return $this->hasMany(Pregnancy::class);
    }

    /** @return HasMany<Device, $this> */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /** @return HasMany<Pregnancy, $this> */
    public function activePregnancy(): HasMany
    {
        return $this->pregnancies()->whereNotNull('active_flag');
    }
}
