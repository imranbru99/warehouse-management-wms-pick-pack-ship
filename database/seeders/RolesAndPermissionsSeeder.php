<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleDefinition;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        foreach (ModuleDefinition::query()->get() as $module) {
            foreach ($module->permissions ?? [] as $permission) {
                Permission::findOrCreate($permission, $guard);
            }
        }

        // Platform roles (no team)
        Role::findOrCreate('platform_super_admin', $guard);
        Role::findOrCreate('platform_support', $guard);

        foreach (config('wms.roles') as $role) {
            Role::findOrCreate($role, $guard);
        }

        $owner = Role::findByName('tenant_owner', $guard);
        $owner->syncPermissions(Permission::all());

        $admin = Role::findByName('admin', $guard);
        $admin->syncPermissions(Permission::all());

        $auditor = Role::findByName('auditor', $guard);
        $auditor->syncPermissions(
            Permission::query()->where('name', 'like', '%.view')->get()
        );
    }
}
