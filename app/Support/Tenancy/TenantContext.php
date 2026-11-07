<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    protected ?Tenant $tenant = null;

    public function set(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function tenant(): ?Tenant
    {
        if ($this->tenant !== null) {
            return $this->tenant;
        }

        $user = Auth::user();
        if ($user && isset($user->tenant_id) && $user->tenant_id) {
            $this->tenant = Tenant::query()->find($user->tenant_id);
        }

        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant()?->id;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function hasTenant(): bool
    {
        return $this->id() !== null;
    }
}
