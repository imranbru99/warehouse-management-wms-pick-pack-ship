<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'locale',
        'timezone',
        'is_platform_admin',
        'is_demo_user',
        'portal_type',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
            'is_demo_user' => 'boolean',
            'preferences' => 'array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'platform' => (bool) $this->is_platform_admin,
            'app' => $this->tenant_id !== null && ! in_array($this->portal_type, ['supplier', 'customer', 'carrier'], true),
            'supplier' => $this->portal_type === 'supplier',
            'portal' => $this->portal_type === 'customer',
            'carrier' => $this->portal_type === 'carrier',
            'rf' => $this->tenant_id !== null && in_array($this->portal_type, [null, 'staff', 'rf'], true),
            default => false,
        };
    }
}
