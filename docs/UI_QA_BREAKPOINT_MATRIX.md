# Matriz de QA UI Responsive (Catalogos)

Fecha: 2026-03-14
Alcance: catalog-products, catalog-menu-items, catalog-measures, catalog-menu-categories, catalog-product-types, welcome sidebar

## 1. Criterios de verificacion

- Movil: <= 760px
- Tablet/Laptop: 761px-1200px (o 761px-1080px en products)
- Escritorio amplio: > 1200px (o > 1080px en products)
- No superposicion de campos
- Distancias uniformes (gaps, labels e inputs)
- Transiciones estables sin saltos bruscos

## 2. Estado por modulo

| Modulo | Regla de columnas | Umbral dinamico 2/3 | Densidad visual unificada | Estado |
|---|---|---:|---|---|
| catalog-products | 1 (movil), 2 (tablet/laptop), 2/3 (escritorio) | Si (`--fields-layout-3col-min-width`) | Si (`--form-*`) | OK tecnico |
| catalog-menu-items | 1 (movil), 2 (tablet/laptop), 2/3 (escritorio) | Si (`--fields-layout-3col-min-width`) | Si (`--form-*`) | OK tecnico |
| catalog-measures | 1 (movil), 2 (tablet/laptop/escritorio) | No requerido | Si (`--form-*`) | OK tecnico |
| catalog-menu-categories | 1 (movil), 2 (tablet/laptop/escritorio) | No requerido | Si (`--form-*`) | OK tecnico |
| catalog-product-types | 1 (movil), 2 (tablet/laptop/escritorio) | No requerido | Si (`--form-*`) | OK tecnico |
| sidebar welcome | Scrollbar estilizada y desplazada al borde seguro | N/A | N/A | OK tecnico |

## 3. Evidencia de implementacion (archivos)

- backend/resources/views/catalog-products.blade.php
- backend/resources/views/catalog-menu-items.blade.php
- backend/resources/views/catalog-measures.blade.php
- backend/resources/views/catalog-menu-categories.blade.php
- backend/resources/views/catalog-product-types.blade.php
- backend/resources/views/welcome.blade.php

## 4. Nota de validacion

- Verificacion realizada por inspeccion de reglas CSS/JS y estado de errores del workspace.
- Existe falso positivo de analizador en Blade con `@json(...)` dentro de `catalog-menu-items.blade.php` (sin impacto runtime).

## 5. Checklist manual recomendado (cierre release UI)

- [ ] 1366px: abrir editor de products y menu-items, confirmar 3 columnas en estado amplio.
- [ ] 1280px: alternar menu expandido/contraido en welcome y validar transicion 2/3 en products/menu-items.
- [ ] 1024px: confirmar 2 columnas estables sin solapamiento.
- [ ] 768px: confirmar 1 columna y scroll natural.
- [ ] 390px: validar botones de accion y campos largos sin recorte.
- [ ] Sidebar: confirmar barra de desplazamiento alineada al borde derecho sin sobreponer contenido.

## 6. Mejoras de performance aplicadas (14-mar-2026)

- `catalog-products`:
	- Sincronizacion de columnas por `requestAnimationFrame` (coalescing de `resize` y `ResizeObserver`).
	- Prevencion de recalculo cuando ancho de viewport/frame no cambia.
	- Filtro de tabla con `debounce` (90ms) para evitar repintado por cada tecla.
	- Cache local con TTL (5 min) para catalogos auxiliares (`measures`, `product-types`).
- `catalog-menu-items`:
	- Sincronizacion de layout por `requestAnimationFrame`.
	- Prevencion de recalculo cuando no cambia el ancho efectivo.
	- Aplicacion de clase `layout-3` solo cuando cambia el estado (menos recorridos DOM).
	- Filtro de tabla con `debounce` (90ms).
	- Cache local con TTL (5 min) para catalogos auxiliares (`products`, `product-types`, `menu-categories`).

Resultado esperado: menor jitter al redimensionar, menor trabajo de CPU en formularios complejos y tiempo de carga percibido mas rapido al abrir catalogos repetidamente.

## 7. Mejoras de profesionalizacion visual aplicadas (14-mar-2026)

- `ui-frames-pro.css`:
	- Anillo de foco unificado para navegacion por teclado (`:focus-visible`) en enlaces, botones e inputs dentro de vistas.
	- Mejora de legibilidad tipografica con `text-rendering` y `font-smoothing` en contenedores principales.
	- Fallback visual para overlay cuando `backdrop-filter` no esta disponible.
	- Ajuste tactil en dispositivos `pointer: coarse` (botones mas comodos y sin micro-animacion de filas).
	- Estabilidad de layout con `scrollbar-gutter` cuando el navegador lo soporta.
- `ui-action-buttons.css`:
	- Interaccion tactil mas fluida (`touch-action: manipulation`, `-webkit-tap-highlight-color: transparent`).
	- Mejor foco accesible (`outline-offset` ampliado).
	- Botones deshabilitados realmente no interactivos (`pointer-events: none`).
	- Respeto de accesibilidad para usuarios con `prefers-reduced-motion`.

Resultado esperado: interfaz mas profesional y consistente en desktop/movil, mejor accesibilidad y menor fatiga visual sin alterar el concepto de diseno existente.
