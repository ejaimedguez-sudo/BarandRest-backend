<?php

return [
    'catalog_media_layout' => [
        // Regla general:
        // - Cuando el ancho del frame supera three_columns_min_width + hysteresis, activa 3 columnas.
        // - Cuando baja de three_columns_min_width - hysteresis, vuelve a 2 columnas.
        // - Entre ambos limites mantiene el estado previo (histeresis anti-parpadeo).
        'clasico' => [
            // Cambia antes a 3 columnas para un layout mas compacto/agil.
            'three_columns_min_width' => 1140,
            'hysteresis' => 32,
        ],
        'premium' => [
            // Cambia despues a 3 columnas para mantener mayor espacio visual.
            'three_columns_min_width' => 1200,
            'hysteresis' => 44,
        ],
    ],
];
