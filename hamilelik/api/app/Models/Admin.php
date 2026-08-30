<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * İçerik panelinin kullanıcısı: editör ve gözden geçiren hekim.
 * Uygulama kullanıcılarıyla (User) hiçbir bağı yoktur.
 */
class Admin extends Authenticatable
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    /** Adın baş harfleri — panelde avatar yerine kullanılır. */
    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
