# Catalog UI Design Spec (Ordena Facil)

## Objetivo
Definir un estandar visual unico para todos los catalogos CRUD del sistema (productos, medidas, tipos, categorias, menu items), con foco en:
- balance visual,
- consistencia de escala,
- mantenimiento centralizado.

## Fuente de verdad
La base visual compartida vive en:
- `backend/public/assets/ui-frames-pro.css`

Las vistas que quieran heredar este estandar deben usar:
- `<body class="catalog-standard">`

## Tokens globales
Tokens definidos en `ui-frames-pro.css`:
- Espaciado:
  - `--of-space-xs: 4px`
  - `--of-space-sm: 6px`
  - `--of-space-md: 8px`
  - `--of-space-lg: 10px`
  - `--of-space-xl: 12px`
- Radio:
  - `--of-radius-sm: 7px`
  - `--of-radius-md: 8px`
  - `--of-radius-lg: 9px`
- Tipografia UI:
  - `--of-font-xs: 11px`
  - `--of-font-sm: 11.5px`
  - `--of-font-md: 12px`
  - `--of-font-ui: 12.5px`
- Controles:
  - `--of-control-h: 32px`
  - `--of-btn-h: 28px`

## Reglas de layout
1. Estructura base
- `wrap` con ritmo compacto.
- `hero` + `panel` principal + `editor-frame`.

2. Jerarquia tipografica
- Titulo de hero: `clamp(19px, 2.1vw, 26px)`.
- Subtitulo: 12px con line-height ~1.45.
- Titulos de panel: 15px.

3. Formularios
- Formularios de alta densidad: 3 columnas desktop, 2 columnas en ancho medio, 1 columna en mobile.
- Todos los campos deben forzar:
  - `width: 100%`
  - `max-width: 100%`
  - `min-width: 0` en contenedores grid
- Altura visual de inputs/select/textarea compacta y consistente.

4. Botones
- Escala unificada en:
  - header de panel,
  - footer de tabla,
  - header de editor,
  - acciones de formulario,
  - acciones auxiliares en paneles especiales.

5. Tabla
- Encabezados y celdas compactos, legibles y homogéneos.
- Mantener sticky header donde aplique.

## Checklist para nuevos catalogos
1. Incluir assets:
- `/assets/ui-action-buttons.css`
- `/assets/ui-frames-pro.css`

2. Agregar clase en body:
- `<body class="catalog-standard">`

3. Usar estructura:
- hero
- panel con toolbar + tabla + frame-footer
- editor-overlay + editor-frame

4. Validar responsividad:
- Desktop: layout principal sin desbordes.
- Tablet: colapso progresivo de columnas.
- Mobile: una columna para formularios.

5. Validar consistencia visual:
- botones con escala uniforme
- mensajes/status compactos
- tipografia y espaciado balanceados

## Convenciones de mantenimiento
- Cambios globales de escala se hacen primero en `ui-frames-pro.css`.
- Evitar hardcodear nuevos tamanos en cada vista si ya existe token.
- Si un catalogo requiere una excepcion, documentar el motivo en el comentario CSS local.

## Estado actual
Catalogos homologados al estandar:
- `backend/resources/views/catalog-products.blade.php`
- `backend/resources/views/catalog-measures.blade.php`
- `backend/resources/views/catalog-product-types.blade.php`
- `backend/resources/views/catalog-menu-categories.blade.php`
- `backend/resources/views/catalog-menu-items.blade.php`
