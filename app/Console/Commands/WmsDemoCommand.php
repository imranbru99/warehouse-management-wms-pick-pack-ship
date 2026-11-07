<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Inbound\PurchaseOrderStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Inbound\ReceivingService;
use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class WmsDemoCommand extends Command
{
    protected $signature = 'wms:demo {--fresh : Truncate demo tenants and users first}';

    protected $description = 'Seed demo platform admin, tenants, modules, and sample warehouse data';

    public function handle(
        ModuleRegistry $modules,
        ReceivingService $receiving,
        TenantContext $tenantContext,
    ): int {
        $this->call('db:seed', ['--class' => ModuleCatalogSeeder::class, '--force' => true]);
        $this->call('db:seed', ['--class' => RolesAndPermissionsSeeder::class, '--force' => true]);
        Cache::flush();
        $modules->forgetCatalogCache();

        $password = (string) config('wms.demo_password', 'password');
        $registrar = app(PermissionRegistrar::class);

        $platform = User::query()->updateOrCreate(
            ['email' => 'platform@demo.wms.test'],
            [
                'name' => 'Platform Super Admin',
                'password' => Hash::make($password),
                'is_platform_admin' => true,
                'is_demo_user' => true,
                'tenant_id' => null,
            ]
        );
        $this->syncUserRole($platform, 'platform_super_admin', 0);

        $fullTenant = $this->upsertTenant(
            name: 'Demo E-commerce Fulfillment',
            subdomain: 'demo-ecommerce',
            preset: 'ecommerce',
        );

        $minimalTenant = $this->upsertTenant(
            name: 'Demo Minimal',
            subdomain: 'demo-minimal',
            preset: 'minimal',
        );

        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@demo.wms.test'],
            [
                'name' => 'Demo Owner',
                'password' => Hash::make($password),
                'tenant_id' => $fullTenant->id,
                'is_demo_user' => true,
                'portal_type' => 'staff',
            ]
        );
        $this->syncUserRole($owner, 'tenant_owner', $fullTenant->id);

        $minimalOwner = User::query()->updateOrCreate(
            ['email' => 'minimal@demo.wms.test'],
            [
                'name' => 'Minimal Owner',
                'password' => Hash::make($password),
                'tenant_id' => $minimalTenant->id,
                'is_demo_user' => true,
                'portal_type' => 'staff',
            ]
        );
        $this->syncUserRole($minimalOwner, 'tenant_owner', $minimalTenant->id);
        $registrar->forgetCachedPermissions();

        $ecommerceModules = config('wms.presets.ecommerce.modules', []);
        $minimalModules = config('wms.presets.minimal.modules', []);

        $modules->applyPreset($fullTenant->id, $ecommerceModules);
        $modules->applyPreset($minimalTenant->id, $minimalModules);

        $tenantContext->set($fullTenant);
        $this->seedFullTenantWarehouse($fullTenant, $receiving);

        $this->newLine();
        $this->info('Demo environment ready.');
        $this->table(['Role', 'Email', 'Password', 'URL hint'], [
            ['Platform super admin', 'platform@demo.wms.test', $password, url('/platform')],
            ['E-commerce owner', 'owner@demo.wms.test', $password, url('/app')],
            ['Minimal owner', 'minimal@demo.wms.test', $password, url('/app')],
        ]);
        $this->line('Full tenant subdomain: '.$fullTenant->subdomain.'.'.config('wms.base_domain'));
        $this->line('Minimal tenant subdomain: '.$minimalTenant->subdomain.'.'.config('wms.base_domain'));

        return self::SUCCESS;
    }

    private function syncUserRole(User $user, string $roleName, int $teamId): void
    {
        $role = Role::findByName($roleName, 'web');

        DB::table('model_has_roles')
            ->where('model_type', $user->getMorphClass())
            ->where('model_id', $user->id)
            ->where('team_id', $teamId)
            ->delete();

        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => $user->getMorphClass(),
            'model_id' => $user->id,
            'team_id' => $teamId,
        ]);

        $user->unsetRelation('roles');
    }

    private function upsertTenant(string $name, string $subdomain, string $preset): Tenant
    {
        return Tenant::query()->updateOrCreate(
            ['subdomain' => $subdomain],
            [
                'name' => $name,
                'slug' => $subdomain,
                'preset' => $preset,
                'status' => 'active',
                'is_demo' => true,
                'locale' => 'en',
            ]
        );
    }

    private function seedFullTenantWarehouse(Tenant $tenant, ReceivingService $receiving): void
    {
        $warehouse = Warehouse::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'MAIN'],
            [
                'name' => 'Main DC',
                'slug' => 'main-dc',
                'is_default' => true,
                'status' => 'active',
            ]
        );

        Location::query()->firstOrCreate(
            ['warehouse_id' => $warehouse->id, 'code' => 'DOCK-01'],
            [
                'tenant_id' => $tenant->id,
                'type' => 'receiving',
                'status' => 'active',
            ]
        );

        for ($i = 1; $i <= 19; $i++) {
            $code = sprintf('A-%02d-01', $i);
            Location::query()->firstOrCreate(
                ['warehouse_id' => $warehouse->id, 'code' => $code],
                [
                    'tenant_id' => $tenant->id,
                    'type' => 'storage',
                    'aisle' => 'A',
                    'rack' => sprintf('%02d', $i),
                    'level' => '01',
                    'pick_sequence' => $i,
                    'status' => 'active',
                ]
            );
        }

        $products = collect();
        for ($p = 1; $p <= 10; $p++) {
            $products->push(Product::query()->firstOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => sprintf('DEMO-SKU-%03d', $p)],
                [
                    'name' => 'Demo Product '.$p,
                    'slug' => 'demo-product-'.$p,
                    'status' => 'active',
                ]
            ));
        }

        $supplier = Supplier::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'DEMO-SUP'],
            [
                'name' => 'Demo Supplier',
                'status' => 'active',
            ]
        );

        $dock = Location::query()->where('warehouse_id', $warehouse->id)->where('code', 'DOCK-01')->firstOrFail();

        $po = PurchaseOrder::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'number' => 'PO-DEMO-001'],
            [
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $supplier->id,
                'status' => PurchaseOrderStatus::Submitted,
            ]
        );

        $lineNumber = 1;
        $receiveLines = [];

        foreach ($products->take(5) as $product) {
            $line = PurchaseOrderLine::query()->firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'purchase_order_id' => $po->id,
                    'product_id' => $product->id,
                ],
                [
                    'line_number' => $lineNumber++,
                    'sku' => $product->sku,
                    'qty_ordered' => '50',
                    'qty_received' => '0',
                ]
            );

            if (bccomp((string) $line->qty_received, '50', 3) < 0) {
                $receiveLines[] = [
                    'purchase_order_line_id' => $line->id,
                    'qty' => bcsub('50', (string) $line->qty_received, 3),
                ];
            }
        }

        if ($receiveLines !== [] && $po->status !== PurchaseOrderStatus::Received) {
            $receiving->receive($po->fresh(), $receiveLines, $dock);
        }
    }
}
