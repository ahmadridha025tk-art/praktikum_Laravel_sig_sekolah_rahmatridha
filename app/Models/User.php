<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sekolah_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
    ];
}

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

   public function isAdmin(): bool
{
    return $this->role === Role::ADMIN;
}

public function isDinas(): bool
{
    return $this->role === Role::DINAS;
}

public function isSekolah(): bool
{
    return $this->role === Role::SEKOLAH;
}
    public function roleLabel(): string
{
    return $this->role?->label() ?? 'Tidak diketahui';
}
}