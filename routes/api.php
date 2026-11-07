<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\SalesOrderController;
use App\Support\Modules\Module;
use App\Support\Modules\ModuleRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => response()->json([
        'ok' => true,
        'product' => config('wms.short_name'),
        'version' => '0.1.0',
    ]));

    Route::middleware(['auth:sanctum', 'tenant.resolve'])->group(function (): void {
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::get('/modules', function () {
            return response()->json([
                'data' => app(ModuleRegistry::class)->catalog()->map(fn ($m) => [
                    'key' => $m->key,
                    'name' => $m->name,
                    'enabled' => Module::enabled($m->key),
                    'is_core' => $m->is_core,
                ]),
            ]);
        });

        Route::middleware('module:warehouse')->get('/warehouses', function () {
            return response()->json(['data' => []]);
        });

        Route::middleware('module:outbound')->group(function (): void {
            Route::get('/sales-orders', [SalesOrderController::class, 'index']);
            Route::post('/sales-orders', [SalesOrderController::class, 'store']);
        });
    });
});
