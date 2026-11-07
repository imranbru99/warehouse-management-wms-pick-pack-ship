<?php

declare(strict_types=1);

return [
    'name' => 'Warehouse Management (WMS): bin locations, pick, pack, ship',
    'short_name' => 'WMS',
    'base_domain' => env('WMS_BASE_DOMAIN', 'wms.test'),
    'demo_mode' => env('WMS_DEMO_MODE', false),
    'demo_password' => env('WMS_DEMO_PASSWORD', 'password'),
    'allow_negative_stock' => env('WMS_ALLOW_NEGATIVE_STOCK', false),

    'presets' => [
        'small_warehouse' => [
            'label' => 'Small warehouse',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'reporting', 'notifications'],
        ],
        'ecommerce' => [
            'label' => 'E-commerce fulfillment',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'returns', 'channels', 'reporting', 'notifications', 'automation', 'ai'],
        ],
        '3pl' => [
            'label' => '3PL',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'returns', 'billing_3pl', 'yard', 'labor', 'reporting', 'notifications', 'automation'],
        ],
        'manufacturing' => [
            'label' => 'Manufacturing',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'kitting', 'quality', 'procurement', 'reporting', 'notifications'],
        ],
        'wholesale' => [
            'label' => 'Wholesale distribution',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'procurement', 'reporting', 'notifications'],
        ],
        'retail_multi_store' => [
            'label' => 'Retail multi-store',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'channels', 'reporting', 'notifications'],
        ],
        'cold_chain' => [
            'label' => 'Cold chain / food',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'quality', 'reporting', 'notifications'],
        ],
        'pharma' => [
            'label' => 'Pharma',
            'modules' => ['warehouse', 'products', 'inbound', 'inventory', 'outbound', 'pack_ship', 'quality', 'returns', 'reporting', 'notifications'],
        ],
        'custom' => [
            'label' => 'Custom',
            'modules' => ['warehouse', 'products'],
        ],
        'minimal' => [
            'label' => 'Minimal (demo toggle proof)',
            'modules' => ['warehouse', 'products'],
        ],
    ],

    'roles' => [
        'tenant_owner',
        'admin',
        'warehouse_manager',
        'inventory_controller',
        'receiving_clerk',
        'putaway_operator',
        'picker',
        'packer',
        'shipping_clerk',
        'material_handler',
        'cycle_counter',
        'qc_inspector',
        'returns_processor',
        'procurement_officer',
        'sales_desk',
        'customer_service',
        'finance',
        'hr_labor_supervisor',
        'auditor',
        'integration_manager',
        'api_user',
        'supplier_user',
        'customer_user',
        'carrier_user',
    ],
];
