<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\ResolveTenant;
use App\Services\Inbound\PutawayService;
use App\Services\Inbound\ReceivingService;
use App\Services\Inventory\StockAdjustmentService;
use App\Services\Inventory\StockLedgerService;
use App\Services\Returns\ReturnService;
use App\Support\Modules\ModuleRegistry;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(ModuleRegistry::class);
        $this->app->singleton(StockLedgerService::class);
        $this->app->singleton(ReceivingService::class);
        $this->app->singleton(PutawayService::class);
        $this->app->singleton(StockAdjustmentService::class);
        $this->app->singleton(ReturnService::class);

        $this->mergeConfigFrom(base_path('config/wms.php'), 'wms');
    }

    public function boot(): void
    {
        Blade::if('module', fn (string $key): bool => module_enabled($key));

        Route::aliasMiddleware('module', EnsureModuleEnabled::class);
        Route::aliasMiddleware('tenant.resolve', ResolveTenant::class);

        $this->loadModuleRoutes();
    }

    protected function loadModuleRoutes(): void
    {
        $modulesPath = base_path('modules');
        if (! is_dir($modulesPath)) {
            return;
        }

        foreach (glob($modulesPath.'/*/routes/web.php') ?: [] as $file) {
            $this->loadRoutesFrom($file);
        }

        foreach (glob($modulesPath.'/*/routes/api.php') ?: [] as $file) {
            Route::prefix('api/v1')
                ->middleware(['api', 'auth:sanctum', 'tenant.resolve'])
                ->group($file);
        }
    }
}
