<?php

return [
    // Roles operativos definidos para la aplicacion.
    'known_roles' => [
        'admin',
        'gerente',
        'caja',
        'cocina',
        'mesero',
        'user',
        'guest',
    ],

    // Capacidades operativas consumidas por la UI.
    'capabilities' => [
        'admin' => [
            'view_dashboard',
            'manage_system',
            'manage_orders',
            'manage_kitchen',
            'manage_tables',
            'manage_customers',
            'manage_expenses',
            'manage_reports',
            'manage_commissions',
            'manage_payments',
            'manage_catalog',
            'manage_users',
        ],
        'gerente' => [
            'view_dashboard',
            'manage_system',
            'manage_orders',
            'manage_kitchen',
            'manage_tables',
            'manage_customers',
            'manage_expenses',
            'manage_reports',
            'manage_commissions',
            'manage_payments',
            'manage_catalog',
        ],
        'caja' => [
            'view_dashboard',
            'manage_orders',
            'manage_payments',
            'manage_customers',
            'manage_reports',
        ],
        'cocina' => [
            'view_dashboard',
            'manage_kitchen',
            'manage_orders',
        ],
        'mesero' => [
            'view_dashboard',
            'manage_orders',
            'manage_tables',
            'manage_customers',
        ],
        'user' => [
            'view_dashboard',
        ],
        'guest' => [
            'view_dashboard',
        ],
    ],
];
