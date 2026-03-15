<?php

return [
    // Margen minimo global para proteger rentabilidad.
    'menu_item_min_margin_default' => (float) env('MENU_ITEM_MIN_MARGIN_DEFAULT', 15),

    // Overrides por codigo de categoria de menu (menu_categories.code).
    'menu_item_min_margin_by_category' => [
        'CAT-COCTEL' => 20,
        'CAT-DEST' => 18,
    ],

    // Roles que pueden sobrepasar la regla minima de margen.
    'margin_override_roles' => ['gerente'],

    // Umbrales para semaforo visual de margen en catalogo de menu items.
    'menu_item_margin_traffic_light' => [
        'good' => (float) env('MENU_ITEM_MARGIN_GOOD', 25),
        'mid' => (float) env('MENU_ITEM_MARGIN_MID', 15),
    ],
];
