@php
    $catalogLayout = config('ui_layout.catalog_media_layout', []);
    $layoutClasico = $catalogLayout['clasico'] ?? [];
    $layoutPremium = $catalogLayout['premium'] ?? [];
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalogo de Productos - Ordena Facil</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/ui-action-buttons.css?v={{ $assetVersion }}">
    <link rel="stylesheet" href="/assets/ui-frames-pro.css?v={{ $assetVersion }}">
    <style>
        :root,
        :root[data-theme="clasico"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg: #fdf2e8;
            --panel: #fffaf7;
            --panel-soft: #fff4ea;
            --text: #3a1a0e;
            --muted: #8f5f45;
            --border: #f3d9c8;
            --ok-bg: rgba(16, 185, 129, 0.1);
            --ok-border: rgba(16, 185, 129, 0.35);
            --ok-text: #065f46;
            --warn-bg: rgba(185, 28, 28, 0.08);
            --warn-border: rgba(185, 28, 28, 0.28);
            --warn-text: #991b1b;
            --form-field-row-gap: 10px;
            --form-field-col-gap: 12px;
            --form-label-min-height: 26px;
            --form-input-min-height: 35px;
            --fields-layout-3col-min-width: {{ (int) ($layoutClasico['fields_three_columns_min_width'] ?? 940) }};
            --fields-layout-hysteresis: {{ (int) ($layoutClasico['fields_hysteresis'] ?? 30) }};
            --media-layout-3col-min-width: {{ (int) ($layoutClasico['three_columns_min_width'] ?? 1140) }};
            --media-layout-hysteresis: {{ (int) ($layoutClasico['hysteresis'] ?? 32) }};
        }

        :root[data-theme="premium"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg: #1b0f0b;
            --panel: #26140f;
            --panel-soft: #1f110d;
            --text: #f8e9d6;
            --muted: #d8bca4;
            --border: rgba(242, 194, 48, 0.2);
            --ok-bg: rgba(16, 185, 129, 0.14);
            --ok-border: rgba(16, 185, 129, 0.4);
            --ok-text: #a7f3d0;
            --warn-bg: rgba(239, 68, 68, 0.12);
            --warn-border: rgba(248, 113, 113, 0.32);
            --warn-text: #fecaca;
            --form-field-row-gap: 11px;
            --form-field-col-gap: 13px;
            --form-label-min-height: 26px;
            --form-input-min-height: 35px;
            --fields-layout-3col-min-width: {{ (int) ($layoutPremium['fields_three_columns_min_width'] ?? 960) }};
            --fields-layout-hysteresis: {{ (int) ($layoutPremium['fields_hysteresis'] ?? 34) }};
            --media-layout-3col-min-width: {{ (int) ($layoutPremium['three_columns_min_width'] ?? 1200) }};
            --media-layout-hysteresis: {{ (int) ($layoutPremium['hysteresis'] ?? 44) }};
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 440px at -10% -40%, rgba(242, 145, 27, 0.2), transparent 56%),
                radial-gradient(900px 440px at 110% -40%, rgba(242, 70, 7, 0.2), transparent 56%),
                var(--bg);
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .hero {
            background: linear-gradient(155deg, var(--panel), var(--panel-soft));
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(19px, 2.1vw, 26px);
            line-height: 1.14;
            letter-spacing: .22px;
        }

        .hero p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .badge {
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 5px 10px;
            font-size: 11px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.06);
        }

        .grid {
            display: grid;
            gap: 10px;
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.13);
            min-width: 0;
        }

        .panel .head {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            padding: 10px 10px 0;
        }

        .panel .head h2 {
            margin: 0;
            font-size: 15px;
            letter-spacing: .2px;
        }

        .panel .body {
            padding: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: minmax(300px, .92fr) minmax(0, 1.38fr);
            gap: 12px 14px;
            align-items: start;
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 8px 0 12px;
        }

        .form-grid > * {
            min-width: 0;
        }

        .field-wide,
        .form-actions {
            grid-column: 1 / -1;
        }

        .form-grid > input[type="hidden"] {
            display: none;
        }

        .form-main-fields {
            grid-column: 2;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--form-field-row-gap) var(--form-field-col-gap);
            align-content: start;
            transition: grid-template-columns .18s ease, gap .18s ease;
        }

        .form-main-fields.cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-main-fields.cols-1 {
            grid-template-columns: 1fr;
        }

        .form-main-fields.cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .form-main-fields.cols-3 .field-span-2 {
            grid-column: span 2;
        }

        .form-main-fields .field {
            margin: 0;
        }

        .form-main-fields .field-wide,
        .form-main-fields .form-actions {
            grid-column: 1 / -1;
        }

        .media-field {
            grid-column: 1;
            border: 1px dashed var(--border);
            border-radius: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            grid-template-columns: 1fr;
            gap: 9px;
            align-items: start;
        }

        .media-preview {
            grid-column: 1;
            grid-row: 1;
        }

        .media-controls {
            grid-column: 1;
            display: grid;
            gap: 6px;
            min-width: 0;
        }

        .media-file-input-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .field-hint {
            margin: 0;
            font-size: 11px;
            line-height: 1.35;
            color: var(--muted);
        }

        .media-preview {
            min-height: 222px;
            height: clamp(222px, 32vh, 344px);
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .media-preview:hover,
        .media-preview:focus-visible {
            border-color: var(--c2);
            box-shadow: 0 0 0 2px rgba(240, 184, 3, 0.2);
            outline: none;
        }

        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
            background: rgba(0, 0, 0, 0.14);
        }

        .media-preview.loading {
            pointer-events: none;
            position: relative;
            opacity: 0.9;
        }

        .media-preview.dragover {
            border-color: var(--c2);
            box-shadow: 0 0 0 2px rgba(240, 184, 3, 0.28);
            background: rgba(240, 184, 3, 0.08);
        }

        .media-preview.loading::before {
            content: 'Subiendo...';
            position: absolute;
            left: 8px;
            bottom: 8px;
            font-size: 11px;
            color: #fff;
            background: rgba(0, 0, 0, 0.58);
            padding: 3px 6px;
            border-radius: 6px;
            z-index: 2;
        }

        .media-preview.loading::after {
            content: '';
            position: absolute;
            right: 10px;
            bottom: 10px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.85);
            border-top-color: transparent;
            animation: media-spin .8s linear infinite;
            z-index: 2;
        }

        .media-empty {
            color: var(--muted);
            text-align: center;
            padding: 10px;
            display: grid;
            gap: 4px;
            justify-items: center;
        }

        .media-empty-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .media-empty-sub {
            font-size: 11px;
            line-height: 1.35;
            max-width: 160px;
        }

        .toast-stack {
            position: fixed;
            top: 16px;
            right: 16px;
            display: grid;
            gap: 8px;
            z-index: 1300;
            pointer-events: none;
        }

        .toast {
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            border: 1px solid var(--border);
            color: var(--text);
            background: color-mix(in srgb, var(--panel) 90%, #000 10%);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            animation: toast-in .18s ease-out;
        }

        .toast.ok {
            border-color: rgba(43, 174, 102, 0.45);
        }

        .toast.error {
            border-color: rgba(220, 70, 70, 0.48);
        }

        @keyframes media-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .field {
            display: grid;
            gap: 5px;
            min-width: 0;
            align-content: start;
        }

        .field label {
            display: flex;
            align-items: flex-end;
            min-height: var(--form-label-min-height);
            font-size: 11.5px;
            color: var(--muted);
            line-height: 1.2;
        }

        .field label.required::after {
            content: ' *';
            color: var(--c3);
            font-weight: 700;
        }

        .field input,
        .field select {
            width: 100%;
            max-width: 100%;
            margin-left: 0;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12.5px;
            min-height: var(--form-input-min-height);
        }

        .field input:focus-visible,
        .field select:focus-visible {
            outline: 2px solid color-mix(in srgb, var(--c1) 76%, #fff 24%);
            outline-offset: 1px;
        }


        .form-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 2px;
        }

        .editor-head .btn,
        .form-actions .btn {
            min-height: 28px;
            padding: 3px 8px;
            border-radius: 7px;
            font-size: 11px;
            letter-spacing: .1px;
        }

        .status {
            margin-top: 8px;
            border-radius: 9px;
            border: 1px solid var(--border);
            padding: 8px;
            font-size: 11.5px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            min-height: 34px;
            white-space: pre-wrap;
            line-height: 1.4;
        }

        .status.ok {
            background: var(--ok-bg);
            border-color: var(--ok-border);
            color: var(--ok-text);
        }

        .status.error {
            background: var(--warn-bg);
            border-color: var(--warn-border);
            color: var(--warn-text);
        }

        .toolbar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .toolbar input {
            flex: 1;
            min-width: 180px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12px;
            min-height: 32px;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: scroll;
            height: min(58vh, 540px);
            background: rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 12px;
            vertical-align: middle;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: color-mix(in srgb, var(--panel) 88%, #000 12%);
            font-size: 11px;
            color: var(--muted);
            letter-spacing: .2px;
        }

        tbody tr {
            cursor: pointer;
            transition: background-color .16s ease;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        tbody tr.selected {
            background: rgba(242, 145, 27, 0.22);
        }

        .img-col {
            width: 66px;
            min-width: 66px;
            text-align: center;
        }

        .table-thumb {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            display: inline-block;
        }

        .table-thumb-empty {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: 1px dashed var(--border);
            color: var(--muted);
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .frame-footer {
            margin-top: 8px;
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .frame-footer .btn,
        .panel .head .btn {
            min-height: 28px;
            padding: 3px 8px;
            border-radius: 7px;
            font-size: 11px;
            letter-spacing: .1px;
        }

        .editor-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.48);
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
        }

        .editor-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .editor-frame {
            position: fixed;
            z-index: 101;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(.98);
            width: min(1040px, calc(100vw - 28px));
            max-height: calc(100vh - 28px);
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 28px 50px rgba(0, 0, 0, 0.35);
            padding: 12px;
            display: grid;
            gap: 8px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }

        .editor-frame.active {
            opacity: 1;
            pointer-events: auto;
            transform: translate(-50%, -50%) scale(1);
        }

        .editor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
        }

        .editor-head h2 {
            margin: 0;
            font-size: 16px;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            letter-spacing: .2px;
        }

        .editor-body {
            overflow: auto;
            overflow-x: hidden;
            max-height: calc(100vh - 170px);
            padding-right: 2px;
        }

        @media (max-width: 1080px) {
            .form-grid {
                grid-template-columns: minmax(0, 1fr);
                max-width: 860px;
                padding: 0 2px;
            }

            .form-main-fields,
            .media-field {
                grid-column: 1;
            }

            .form-main-fields {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px 10px;
            }
        }

        .empty {
            padding: 14px;
            color: var(--muted);
            font-size: 12px;
        }

        @media (max-width: 760px) {
            .table-wrap { height: min(46vh, 420px); }

            .form-grid {
                grid-template-columns: 1fr;
                padding: 0;
            }

            .form-main-fields {
                grid-template-columns: 1fr;
                gap: 9px;
            }

            .form-main-fields .field-span-2 {
                grid-column: auto;
            }

            .form-grid > .field {
                grid-column: auto;
            }

            .field input,
            .field select {
                width: 100%;
                margin-left: 0;
            }

            .field label {
                min-height: 0;
            }

            .media-preview,
            .media-controls {
                grid-column: 1;
            }

            .media-preview {
                grid-row: auto;
            }

            .field-wide,
            .form-actions {
                grid-column: auto;
            }

            .frame-footer {
                justify-content: stretch;
            }

            .frame-footer .btn {
                flex: 0 1 auto;
            }
        }
    </style>
</head>
<body class="catalog-standard">
<main class="wrap">
    <section class="grid">
        <article class="panel">
            <div class="head">
                <span class="badge">Vista operativa</span>
            </div>
            <div class="body">
                <div class="toolbar">
                    <input id="tableFilter" type="search" placeholder="Buscar por nombre, SKU o unidad...">
                    <button id="btnRefresh" class="btn btn-compact" type="button">Actualizar</button>
                </div>
                <div id="tableContainer" class="table-wrap"></div>
                <div class="frame-footer">
                    <button id="btnAdd" class="btn btn-compact btn-add" type="button">Agregar</button>
                    <button id="btnEdit" class="btn btn-compact btn-edit" type="button">Editar</button>
                    <button id="btnDelete" class="btn btn-compact btn-delete" type="button">Eliminar</button>
                </div>
                <div id="status" class="status">Selecciona un registro y usa Agregar, Editar o Eliminar.</div>
            </div>
        </article>
    </section>
</main>

<div id="editorOverlay" class="editor-overlay" aria-hidden="true"></div>
<section id="editorFrame" class="editor-frame" aria-hidden="true">
    <div class="editor-head">
        <h2 id="formTitle">Nuevo producto</h2>
        <button id="btnCloseEditor" class="btn btn-compact" type="button">Cerrar</button>
    </div>
    <div class="editor-body">
        <form id="productForm" class="form-grid">
            <input id="productId" type="hidden">

            <div class="field media-field">
                <div id="imagePreview" class="media-preview" role="button" tabindex="0" title="Haz clic para seleccionar una imagen" aria-label="Seleccionar imagen del producto">
                    <span class="media-empty">Sin imagen seleccionada</span>
                </div>
                <div class="media-controls">
                    <input id="image_url" name="image_url" type="hidden" maxlength="2048">
                    <input id="image_file" name="image_file" class="media-file-input-hidden" type="file" accept="image/jpeg,image/png,image/webp">
                    <p class="field-hint">Haz clic en el marco para seleccionar imagen. Formatos JPG/PNG/WEBP (max 5 MB).</p>
                </div>
            </div>

            <div class="form-main-fields">
                <div class="field">
                    <label for="sku">SKU</label>
                    <input id="sku" name="sku" type="text" maxlength="120" placeholder="Opcional">
                </div>

                <div class="field field-span-2">
                    <label for="name" class="required">Nombre</label>
                    <input id="name" name="name" type="text" maxlength="255" required>
                </div>

                <div class="field">
                    <label for="product_type_id">Tipo de producto</label>
                    <select id="product_type_id" name="product_type_id">
                        <option value="">Sin tipo</option>
                    </select>
                </div>

                <div class="field">
                    <label for="unit" class="required">Unidad</label>
                    <input id="unit" name="unit" type="text" maxlength="50" placeholder="kg, lt, pieza" list="unitOptions" required>
                    <datalist id="unitOptions"></datalist>
                </div>

                <div class="field field-span-2">
                    <label for="presentation">Presentacion</label>
                    <input id="presentation" name="presentation" type="text" maxlength="120" placeholder="750 ml, 1L, 350 ml...">
                </div>

                <div class="field">
                    <label for="cost">Costo</label>
                    <input id="cost" name="cost" type="number" min="0" step="0.01" placeholder="0.00">
                </div>

                <div class="field">
                    <label for="stock">Stock actual</label>
                    <input id="stock" name="stock" type="number" step="0.001" placeholder="0">
                </div>

                <div class="field">
                    <label for="daily_consumption">Consumo diario</label>
                    <input id="daily_consumption" name="daily_consumption" type="number" min="0" step="0.001" placeholder="0">
                </div>

                <div class="field">
                    <label for="coverage_days">Dias de cobertura</label>
                    <input id="coverage_days" name="coverage_days" type="number" step="0.01" readonly placeholder="Calculado automaticamente">
                </div>

                <div class="field">
                    <label for="initial_stock">Inventario inicial</label>
                    <input id="initial_stock" name="initial_stock" type="number" min="0" step="0.001" placeholder="0">
                </div>

                <div class="field">
                    <label for="stock_min">Stock minimo</label>
                    <input id="stock_min" name="stock_min" type="number" min="0" step="0.001" placeholder="0">
                </div>

                <div class="field">
                    <label for="stock_max">Stock maximo</label>
                    <input id="stock_max" name="stock_max" type="number" min="0" step="0.001" placeholder="0">
                </div>

                <div class="field">
                    <label for="reorder_point">Punto de reorden</label>
                    <input id="reorder_point" name="reorder_point" type="number" min="0" step="0.001" placeholder="0">
                </div>

                <div class="form-actions">
                    <button id="btnSubmit" class="btn btn-compact btn-add" type="submit">Guardar</button>
                    <button id="btnCancelEdit" class="btn btn-compact" type="button">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
    const params = new URLSearchParams(window.location.search);
    const theme = (params.get('theme') === 'premium' || localStorage.getItem('ordena-facil-theme') === 'premium') ? 'premium' : 'clasico';

    const roleBadge = document.getElementById('roleBadge');
    const productForm = document.getElementById('productForm');
    const formTitle = document.getElementById('formTitle');
    const statusBox = document.getElementById('status');
    const tableContainer = document.getElementById('tableContainer');
    const tableFilter = document.getElementById('tableFilter');
    const editorFrame = document.getElementById('editorFrame');
    const editorOverlay = document.getElementById('editorOverlay');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const unitOptions = document.getElementById('unitOptions');
    const btnAdd = document.getElementById('btnAdd');
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');
    const btnCloseEditor = document.getElementById('btnCloseEditor');
    const imageFileInput = document.getElementById('image_file');
    const imagePreview = document.getElementById('imagePreview');
    const formMainFields = document.querySelector('.form-main-fields');

    const fields = {
        id: document.getElementById('productId'),
        sku: document.getElementById('sku'),
        name: document.getElementById('name'),
        product_type_id: document.getElementById('product_type_id'),
        unit: document.getElementById('unit'),
        presentation: document.getElementById('presentation'),
        image_url: document.getElementById('image_url'),
        cost: document.getElementById('cost'),
        stock: document.getElementById('stock'),
        daily_consumption: document.getElementById('daily_consumption'),
        coverage_days: document.getElementById('coverage_days'),
        initial_stock: document.getElementById('initial_stock'),
        stock_min: document.getElementById('stock_min'),
        stock_max: document.getElementById('stock_max'),
        reorder_point: document.getElementById('reorder_point'),
    };

    let products = [];
    let measuresCatalog = [];
    let productTypesCatalog = [];
    let canManageCatalog = false;
    let selectedProductId = null;
    let committedImageUrl = '';
    let productPreviewToken = 0;
    let fieldsLayoutObserver = null;
    let fieldsLayoutIsThreeColumns = null;
    let mainFieldsLayoutRafId = 0;
    let lastMainFieldsLayoutViewportWidth = null;
    let lastMainFieldsLayoutFrameWidth = null;
    let lastMenuCollapsedState = null;
    let tableFilterDebounceId = 0;
    const defaultFieldsThreeColumnsMinWidth = 940;
    const defaultFieldsLayoutHysteresisPx = 30;
    const menuCollapsedStorageKey = 'ordena-facil-menu-collapsed';
    const catalogCachePrefix = `barandrest:catalog-cache:${role}:`;
    const catalogCacheTtlMs = 5 * 60 * 1000;
    const maxUploadSizeBytes = 5 * 1024 * 1024;
    const allowedUploadTypes = new Set(['image/jpeg', 'image/png', 'image/webp']);
    const UI_TEXT = {
        noManagePermission: 'No tienes permisos para administrar el catalogo.',
        noManageRole: 'Tu rol actual no tiene permisos para administrar el catalogo.',
        noEditSelection: 'Selecciona un registro para editar.',
        noDeleteSelection: 'Selecciona un registro para eliminar.',
        noCreateEditPermission: 'No tienes permisos para crear o editar registros del catalogo.',
        noUploadPermission: 'No tienes permisos para subir imagenes.',
        ready: 'Selecciona un registro y usa Agregar, Editar o Eliminar.',
        deleted: 'Registro eliminado correctamente.',
        updated: 'Registro actualizado correctamente.',
        created: 'Registro creado correctamente.',
        canceled: 'Edicion cancelada.',
        refreshed: 'Listado actualizado.',
        uploadingImage: 'Subiendo imagen del producto...',
        uploadImageOk: 'Imagen subida correctamente. Guarda el producto para persistir el enlace.',
    };
    const toastRoot = document.createElement('div');
    toastRoot.className = 'toast-stack';
    document.body.appendChild(toastRoot);

    document.documentElement.setAttribute('data-theme', theme);
    if (roleBadge) {
        roleBadge.textContent = `Rol: ${role}`;
    }

    function setStatus(message, type) {
        statusBox.textContent = message;
        statusBox.classList.remove('ok', 'error');
        if (type) {
            statusBox.classList.add(type);
        }
    }

    function showToast(message, type = 'ok') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toastRoot.appendChild(toast);
        window.setTimeout(() => {
            toast.remove();
        }, 2200);
    }

    function normalizeNumber(value) {
        if (value === '' || value === null || value === undefined) return null;
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : null;
    }

    function collectPayload() {
        return {
            sku: fields.sku.value.trim() || null,
            name: fields.name.value.trim(),
            product_type_id: normalizeNumber(fields.product_type_id.value),
            unit: fields.unit.value.trim(),
            presentation: fields.presentation.value.trim() || null,
            image_url: fields.image_url.value.trim() || null,
            cost: normalizeNumber(fields.cost.value),
            stock: normalizeNumber(fields.stock.value),
            daily_consumption: normalizeNumber(fields.daily_consumption.value),
            initial_stock: normalizeNumber(fields.initial_stock.value),
            stock_min: normalizeNumber(fields.stock_min.value),
            stock_max: normalizeNumber(fields.stock_max.value),
            reorder_point: normalizeNumber(fields.reorder_point.value),
        };
    }

    function clearForm() {
        fields.id.value = '';
        fields.sku.value = '';
        fields.name.value = '';
        fields.product_type_id.value = '';
        fields.unit.value = '';
        fields.presentation.value = '';
        fields.image_url.value = '';
        imageFileInput.value = '';
        fields.cost.value = '';
        fields.stock.value = '';
        fields.daily_consumption.value = '';
        fields.coverage_days.value = '';
        fields.initial_stock.value = '';
        fields.stock_min.value = '';
        fields.stock_max.value = '';
        fields.reorder_point.value = '';
        formTitle.textContent = 'Nuevo producto';
        btnSubmit.textContent = 'Guardar';
        renderUnitOptions('');
        updateImagePreview('');
        committedImageUrl = '';
    }

    function imageUrlFromValue(value) {
        return String(value || '').trim();
    }

    function appendPreviewBuster(urlValue) {
        const url = imageUrlFromValue(urlValue);
        if (!url) return '';
        const separator = url.includes('?') ? '&' : '?';
        return `${url}${separator}preview_ts=${Date.now()}`;
    }

    function renderImagePreviewEmpty(message = 'Haz clic en este marco para seleccionar una imagen') {
        imagePreview.innerHTML = `<span class="media-empty"><span class="media-empty-title">Sin imagen</span><span class="media-empty-sub">${escapeHtml(message)}</span></span>`;
    }

    function renderImagePreview(urlValue, fallbackUrl = '') {
        const url = imageUrlFromValue(urlValue);
        if (!url) {
            renderImagePreviewEmpty();
            return;
        }

        const token = ++productPreviewToken;
        const safeLabel = fields.name.value.trim() || 'Producto';
        const img = document.createElement('img');
        img.alt = `Imagen de ${safeLabel}`;
        img.decoding = 'async';
        img.loading = 'eager';
        const previousContent = imagePreview.innerHTML;

        const fallback = imageUrlFromValue(fallbackUrl);
        let usedFallback = false;

        img.addEventListener('error', () => {
            if (token !== productPreviewToken) return;
            if (!usedFallback && fallback && fallback !== url) {
                usedFallback = true;
                img.src = appendPreviewBuster(fallback);
                return;
            }

            if (!previousContent.includes('<img')) {
                renderImagePreviewEmpty('No se pudo cargar la imagen. Vuelve a subirla.');
            }
        });

        img.addEventListener('load', () => {
            if (token !== productPreviewToken) return;
            imagePreview.replaceChildren(img);
        });

        img.src = url;
    }

    function updateImagePreview(urlValue) {
        renderImagePreview(urlValue, committedImageUrl);
    }

    function setImagePreviewLoading(isLoading) {
        imagePreview.classList.toggle('loading', Boolean(isLoading));
    }

    function normalizeUploadMime(file) {
        const mime = String(file?.type || '').toLowerCase();
        if (mime === 'image/jpg') return 'image/jpeg';
        return mime;
    }

    function validateImageFile(file) {
        if (!file) {
            return { valid: false, message: 'Selecciona una imagen antes de subir.' };
        }

        const mime = normalizeUploadMime(file);
        if (!allowedUploadTypes.has(mime)) {
            return { valid: false, message: 'Formato no permitido. Usa JPG, PNG o WEBP.' };
        }

        if (file.size > maxUploadSizeBytes) {
            return { valid: false, message: 'El archivo excede 5 MB.' };
        }

        return { valid: true, message: '' };
    }

    function updateImagePreviewFromFile(file) {
        const reader = new FileReader();
        reader.onload = () => {
            const safeLabel = escapeHtml(fields.name.value.trim() || 'Producto');
            const safeUrl = escapeHtml(String(reader.result || ''));
            if (!safeUrl) return;
            imagePreview.innerHTML = `<img src="${safeUrl}" alt="Imagen de ${safeLabel}">`;
        };
        reader.readAsDataURL(file);
    }

    function syncUploadedImageInPanel(url) {
        const editingId = Number(fields.id.value || 0);
        if (!editingId) return;

        const product = products.find((row) => Number(row.id) === editingId);
        if (!product) return;

        product.image_url = url;
        applyFilter();
    }

    async function uploadProductImage(file) {
        const formData = new FormData();
        formData.append('image', file);

        const response = await fetch('/api/catalog/media/upload', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-USER-ROLE': role,
            },
            body: formData,
        });

        if (!response.ok) {
            let message = `HTTP ${response.status}`;
            try {
                const payload = await response.json();
                const structuredErrors = payload?.error?.details?.errors && typeof payload.error.details.errors === 'object'
                    ? payload.error.details.errors
                    : null;
                const legacyErrors = payload?.errors && typeof payload.errors === 'object' ? payload.errors : null;
                const errors = structuredErrors || legacyErrors;
                if (errors) {
                    const first = Object.values(errors).flat().find(Boolean);
                    if (first) message = String(first);
                } else if (payload?.error?.message) {
                    message = String(payload.error.message);
                } else if (payload?.message) {
                    message = String(payload.message);
                }
            } catch (_error) {
                const text = await response.text();
                if (text) message = `${message} - ${text}`;
            }

            throw new Error(message);
        }

        return response.json();
    }

    function normalizeText(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getCachedCatalog(name) {
        try {
            const raw = localStorage.getItem(`${catalogCachePrefix}${name}`);
            if (!raw) return null;

            const parsed = JSON.parse(raw);
            if (!parsed || !Array.isArray(parsed.data) || !Number.isFinite(parsed.timestamp)) {
                return null;
            }

            if ((Date.now() - parsed.timestamp) > catalogCacheTtlMs) {
                return null;
            }

            return parsed.data;
        } catch (_error) {
            return null;
        }
    }

    function setCachedCatalog(name, data) {
        try {
            localStorage.setItem(`${catalogCachePrefix}${name}`, JSON.stringify({
                timestamp: Date.now(),
                data,
            }));
        } catch (_error) {
            // Continue silently when storage is not available.
        }
    }

    function renderUnitOptions(term) {
        const query = normalizeText(term);
        const filtered = measuresCatalog.filter((measure) => {
            const line = normalizeText(`${measure.name || ''} ${measure.abbreviation || ''} ${measure.description || ''}`);
            return !query || line.includes(query);
        });

        unitOptions.innerHTML = filtered.map((measure) => {
            const code = measure.abbreviation || measure.name;
            return `<option value="${escapeHtml(code)}">${escapeHtml(measure.name || code)}</option>`;
        }).join('');
    }

    async function loadMeasuresCatalog() {
        const cached = getCachedCatalog('measures');
        if (cached) {
            measuresCatalog = cached;
            renderUnitOptions(fields.unit.value || '');
        }

        try {
            const data = await requestJson('/api/measures');
            measuresCatalog = Array.isArray(data) ? data : [];
            setCachedCatalog('measures', measuresCatalog);
            renderUnitOptions(fields.unit.value || '');
        } catch (_error) {
            if (!cached) {
                measuresCatalog = [];
            }
            renderUnitOptions('');
        }
    }

    function renderProductTypeOptions(selectedValue) {
        const selected = selectedValue !== null && selectedValue !== undefined ? String(selectedValue) : '';
        const options = productTypesCatalog.map((item) => {
            const value = String(item.id);
            const label = item.code ? `${item.name} (${item.code})` : item.name;
            return `<option value="${escapeHtml(value)}">${escapeHtml(label)}</option>`;
        }).join('');

        fields.product_type_id.innerHTML = `<option value="">Sin tipo</option>${options}`;
        fields.product_type_id.value = selected;
    }

    async function loadProductTypesCatalog() {
        const cached = getCachedCatalog('product-types');
        if (cached) {
            productTypesCatalog = cached;
            renderProductTypeOptions(fields.product_type_id.value || '');
        }

        try {
            const data = await requestJson('/api/product-types');
            productTypesCatalog = Array.isArray(data) ? data : [];
            setCachedCatalog('product-types', productTypesCatalog);
        } catch (_error) {
            if (!cached) {
                productTypesCatalog = [];
            }
        }

        renderProductTypeOptions(fields.product_type_id.value || '');
    }

    function getSelectedProduct() {
        return products.find((item) => Number(item.id) === Number(selectedProductId)) || null;
    }

    function readCssNumberVar(name, fallback) {
        const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    function isMenuCollapsed() {
        return localStorage.getItem(menuCollapsedStorageKey) === '1';
    }

    function syncMainFieldsLayout(force = false) {
        if (!formMainFields || !editorFrame) return;

        const frameWidth = Math.round(editorFrame.getBoundingClientRect().width || 0);
        const viewportWidth = Math.round(window.innerWidth || 0);
        const menuCollapsed = isMenuCollapsed();
        if (!force && frameWidth === lastMainFieldsLayoutFrameWidth && viewportWidth === lastMainFieldsLayoutViewportWidth) {
            return;
        }
        lastMainFieldsLayoutFrameWidth = frameWidth;
        lastMainFieldsLayoutViewportWidth = viewportWidth;
        lastMenuCollapsedState = menuCollapsed;
        const threeColumnsMinWidth = readCssNumberVar('--fields-layout-3col-min-width', defaultFieldsThreeColumnsMinWidth);
        const layoutHysteresisPx = readCssNumberVar('--fields-layout-hysteresis', defaultFieldsLayoutHysteresisPx);

        if (viewportWidth <= 760) {
            fieldsLayoutIsThreeColumns = false;
            formMainFields.classList.remove('cols-2', 'cols-3');
            formMainFields.classList.add('cols-1');
            return;
        }

        formMainFields.classList.remove('cols-1');

        if (viewportWidth <= 1080) {
            fieldsLayoutIsThreeColumns = false;
            formMainFields.classList.remove('cols-3');
            formMainFields.classList.add('cols-2');
            return;
        }

        if (!menuCollapsed) {
            fieldsLayoutIsThreeColumns = false;
            formMainFields.classList.remove('cols-3');
            formMainFields.classList.add('cols-2');
            return;
        }

        if (fieldsLayoutIsThreeColumns === null) {
            fieldsLayoutIsThreeColumns = frameWidth >= threeColumnsMinWidth;
        } else if (fieldsLayoutIsThreeColumns && frameWidth <= (threeColumnsMinWidth - layoutHysteresisPx)) {
            fieldsLayoutIsThreeColumns = false;
        } else if (!fieldsLayoutIsThreeColumns && frameWidth >= (threeColumnsMinWidth + layoutHysteresisPx)) {
            fieldsLayoutIsThreeColumns = true;
        }

        formMainFields.classList.toggle('cols-3', Boolean(fieldsLayoutIsThreeColumns));
        formMainFields.classList.toggle('cols-2', !Boolean(fieldsLayoutIsThreeColumns));
    }

    function scheduleMainFieldsLayoutSync(force = false) {
        if (force) {
            lastMainFieldsLayoutFrameWidth = null;
            lastMainFieldsLayoutViewportWidth = null;
        }

        if (mainFieldsLayoutRafId) return;

        mainFieldsLayoutRafId = window.requestAnimationFrame(() => {
            mainFieldsLayoutRafId = 0;
            syncMainFieldsLayout(force);
        });
    }

    function scheduleApplyFilter() {
        if (tableFilterDebounceId) {
            window.clearTimeout(tableFilterDebounceId);
        }

        tableFilterDebounceId = window.setTimeout(() => {
            tableFilterDebounceId = 0;
            applyFilter();
        }, 90);
    }

    function observeMainFieldsLayout() {
        if (!editorFrame || fieldsLayoutObserver || typeof ResizeObserver !== 'function') {
            return;
        }

        fieldsLayoutObserver = new ResizeObserver(() => {
            scheduleMainFieldsLayoutSync();
        });

        fieldsLayoutObserver.observe(editorFrame);
    }

    function updateActionButtons() {
        const hasSelection = !!getSelectedProduct();
        btnAdd.disabled = !canManageCatalog;
        btnEdit.disabled = !canManageCatalog || !hasSelection;
        btnDelete.disabled = !canManageCatalog || !hasSelection;
    }

    function openEditor(mode) {
        if (!canManageCatalog) {
            setStatus(UI_TEXT.noManagePermission, 'error');
            return;
        }

        if (mode === 'edit') {
            const product = getSelectedProduct();
            if (!product) {
                setStatus(UI_TEXT.noEditSelection, 'error');
                return;
            }

            fields.id.value = String(product.id);
            fields.sku.value = product.sku || '';
            fields.name.value = product.name || '';
            renderProductTypeOptions(product.product_type_id ?? '');
            fields.unit.value = product.unit || '';
            fields.presentation.value = product.presentation || '';
            fields.image_url.value = product.image_url || '';
            fields.cost.value = product.cost ?? '';
            fields.stock.value = product.stock ?? '';
            fields.daily_consumption.value = product.daily_consumption ?? '';
            fields.coverage_days.value = product.coverage_days ?? '';
            fields.initial_stock.value = product.initial_stock ?? '';
            fields.stock_min.value = product.stock_min ?? '';
            fields.stock_max.value = product.stock_max ?? '';
            fields.reorder_point.value = product.reorder_point ?? product.reorder_level ?? '';
            const productName = String(product.name || '').trim();
            formTitle.textContent = productName
                ? `Editar producto: ${productName}`
                : `Editar producto #${product.id}`;
            btnSubmit.textContent = 'Guardar cambios';
            renderUnitOptions(fields.unit.value);
            updateImagePreview(fields.image_url.value);
            committedImageUrl = imageUrlFromValue(fields.image_url.value);
        } else {
            clearForm();
            formTitle.textContent = 'Nuevo producto';
            btnSubmit.textContent = 'Guardar';
        }

        editorOverlay.classList.add('active');
        editorFrame.classList.add('active');
        editorOverlay.setAttribute('aria-hidden', 'false');
        editorFrame.setAttribute('aria-hidden', 'false');
        scheduleMainFieldsLayoutSync(true);
        fields.name.focus();
    }

    function closeEditor() {
        editorOverlay.classList.remove('active');
        editorFrame.classList.remove('active');
        editorOverlay.setAttribute('aria-hidden', 'true');
        editorFrame.setAttribute('aria-hidden', 'true');
        clearForm();
    }

    function isEditorOpen() {
        return editorFrame.classList.contains('active');
    }

    function isElementVisible(element) {
        return Boolean(element)
            && !element.disabled
            && element.tabIndex !== -1
            && element.getClientRects().length > 0
            && window.getComputedStyle(element).visibility !== 'hidden';
    }

    function getFormFlowControls(formElement) {
        const selectors = [
            'input:not([type="hidden"]):not([type="file"]):not([type="checkbox"]):not([type="radio"])',
            'select',
            'textarea',
            'button[type="button"]'
        ].join(',');

        return Array.from(formElement.querySelectorAll(selectors)).filter(isElementVisible);
    }

    function focusNextControl(formElement, currentElement) {
        const controls = getFormFlowControls(formElement);
        const index = controls.indexOf(currentElement);

        if (index >= 0 && index + 1 < controls.length) {
            controls[index + 1].focus();
            return;
        }

        if (!btnSubmit.disabled) {
            btnSubmit.focus();
        }
    }

    function bindFastFormKeyboardFlow() {
        productForm.addEventListener('keydown', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;

            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                event.preventDefault();
                if (!btnSubmit.disabled) btnSubmit.click();
                return;
            }

            if (event.key !== 'Enter' || event.shiftKey || event.altKey) {
                return;
            }

            if (target.tagName === 'TEXTAREA') {
                return;
            }

            if (target.tagName === 'BUTTON') {
                return;
            }

            if (target instanceof HTMLInputElement && ['checkbox', 'radio', 'file', 'submit', 'button'].includes(target.type)) {
                return;
            }

            event.preventDefault();
            focusNextControl(productForm, target);
        });

        document.addEventListener('keydown', (event) => {
            if (!isEditorOpen()) return;

            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                event.preventDefault();
                if (!btnSubmit.disabled) btnSubmit.click();
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeEditor();
            }
        });
    }

    function setFormEditable(enabled) {
        Object.values(fields).forEach((field) => {
            if (field.id === 'productId') return;
            field.disabled = !enabled;
        });

        btnSubmit.disabled = !enabled;
        btnCancelEdit.disabled = !enabled;
        btnCloseEditor.disabled = !enabled;
        imageFileInput.disabled = !enabled;
        updateActionButtons();
    }

    async function requestJson(url, options = {}) {
        const headers = {
            'Accept': 'application/json',
            'X-USER-ROLE': role,
            ...options.headers,
        };

        const response = await fetch(url, { ...options, headers });

        if (!response.ok) {
            const text = await response.text();
            throw new Error(`HTTP ${response.status} - ${text}`);
        }

        if (response.status === 204) return null;
        return response.json();
    }

    async function loadCapabilities() {
        try {
            const payload = await requestJson('/api/system/capabilities');
            const capabilities = Array.isArray(payload?.capabilities) ? payload.capabilities : [];
            canManageCatalog = capabilities.includes('manage_catalog');

            if (!canManageCatalog) {
                setFormEditable(false);
                setStatus(UI_TEXT.noManageRole, 'error');
            } else {
                setFormEditable(true);
                setStatus(UI_TEXT.ready, null);
            }
        } catch (error) {
            canManageCatalog = false;
            setFormEditable(false);
            setStatus(`No se pudieron cargar permisos: ${String(error.message || error)}`, 'error');
        }
    }

    function formatNumber(value, decimals = 2) {
        if (value === null || value === undefined || value === '') return '-';
        const numeric = Number(value);
        if (!Number.isFinite(numeric)) return String(value);
        return numeric.toFixed(decimals);
    }

    function renderTable(rows) {
        if (!rows.length) {
            tableContainer.innerHTML = '<div class="empty">No hay productos registrados.</div>';
            selectedProductId = null;
            updateActionButtons();
            return;
        }

        const body = rows.map((product) => {
            const selected = Number(selectedProductId) === Number(product.id) ? ' class="selected"' : '';
            const imageUrl = imageUrlFromValue(product.image_url);
            const imageCell = imageUrl
                ? `<img class="table-thumb" src="${escapeHtml(imageUrl)}" alt="${escapeHtml(product.name || 'Producto')}">`
                : '<span class="table-thumb-empty">Sin</span>';
            return `
                <tr data-product-id="${product.id}"${selected}>
                    <td class="img-col">${imageCell}</td>
                    <td>${product.sku || '-'}</td>
                    <td>${product.name || '-'}</td>
                    <td>${product.product_type?.name || '-'}</td>
                    <td>${product.unit || '-'}</td>
                    <td>${product.presentation || '-'}</td>
                    <td>${formatNumber(product.cost, 2)}</td>
                    <td>${formatNumber(product.stock, 3)}</td>
                    <td>${formatNumber(product.daily_consumption, 3)}</td>
                    <td>${formatNumber(product.coverage_days, 2)}</td>
                    <td>${formatNumber(product.initial_stock, 3)}</td>
                    <td>${formatNumber(product.stock_min, 3)}</td>
                    <td>${formatNumber(product.stock_max, 3)}</td>
                    <td>${formatNumber(product.reorder_point ?? product.reorder_level, 3)}</td>
                </tr>
            `;
        }).join('');

        tableContainer.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th class="img-col">Img</th>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Unidad</th>
                        <th>Presentacion</th>
                        <th>Costo</th>
                        <th>Stock actual</th>
                        <th>Consumo diario</th>
                        <th>Dias cobertura</th>
                        <th>Inventario inicial</th>
                        <th>Stock minimo</th>
                        <th>Stock maximo</th>
                        <th>Punto de reorden</th>
                    </tr>
                </thead>
                <tbody>${body}</tbody>
            </table>
        `;

        updateActionButtons();
    }

    function applyFilter() {
        const term = (tableFilter.value || '').trim().toLowerCase();
        if (!term) {
            renderTable(products);
            return;
        }

        const filtered = products.filter((product) => {
            const line = `${product.sku || ''} ${product.name || ''} ${product.product_type?.name || ''} ${product.unit || ''} ${product.presentation || ''}`.toLowerCase();
            return line.includes(term);
        });

        renderTable(filtered);
    }

    async function loadProducts() {
        try {
            const data = await requestJson('/api/products');
            products = Array.isArray(data) ? data : [];

            if (!getSelectedProduct()) {
                selectedProductId = null;
            }

            applyFilter();
        } catch (error) {
            tableContainer.innerHTML = '<div class="empty">No fue posible cargar productos.</div>';
            setStatus(`Error cargando productos: ${String(error.message || error)}`, 'error');
        }
    }

    async function removeSelectedProduct() {
        if (!canManageCatalog) return;

        const product = getSelectedProduct();
        if (!product) {
            setStatus(UI_TEXT.noDeleteSelection, 'error');
            return;
        }

        const productId = Number(product.id);
        const label = product?.name ? `"${product.name}"` : `#${productId}`;
        if (!window.confirm(`¿Deseas eliminar el producto ${label}? Esta accion no se puede deshacer.`)) return;

        try {
            await requestJson(`/api/products/${productId}`, { method: 'DELETE' });
            setStatus(UI_TEXT.deleted, 'ok');
            selectedProductId = null;
            await loadProducts();
        } catch (error) {
            setStatus(`No se pudo eliminar: ${String(error.message || error)}`, 'error');
        }
    }

    productForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canManageCatalog) {
            setStatus(UI_TEXT.noCreateEditPermission, 'error');
            return;
        }

        const payload = collectPayload();
        const editingId = fields.id.value ? Number(fields.id.value) : null;

        try {
            if (editingId) {
                await requestJson(`/api/products/${editingId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus(UI_TEXT.updated, 'ok');
                selectedProductId = editingId;
            } else {
                await requestJson('/api/products', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setStatus(UI_TEXT.created, 'ok');
            }

            closeEditor();
            await loadProducts();
        } catch (error) {
            setStatus(`No se pudo guardar: ${String(error.message || error)}`, 'error');
        }
    });

    btnCancelEdit.addEventListener('click', () => {
        closeEditor();
        setStatus(UI_TEXT.canceled, null);
    });

    btnCloseEditor.addEventListener('click', () => {
        closeEditor();
    });

    editorOverlay.addEventListener('click', () => {
        closeEditor();
    });

    fields.unit.addEventListener('input', () => {
        renderUnitOptions(fields.unit.value);
    });

    fields.name.addEventListener('input', () => {
        updateImagePreview(fields.image_url.value);
    });

    imagePreview.addEventListener('click', () => {
        if (!canManageCatalog || imageFileInput.disabled) return;
        imageFileInput.click();
    });

    imagePreview.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        if (!canManageCatalog || imageFileInput.disabled) return;
        imageFileInput.click();
    });

    imagePreview.addEventListener('dragenter', (event) => {
        event.preventDefault();
        if (!canManageCatalog || imageFileInput.disabled) return;
        imagePreview.classList.add('dragover');
    });

    imagePreview.addEventListener('dragover', (event) => {
        event.preventDefault();
        if (!canManageCatalog || imageFileInput.disabled) return;
        imagePreview.classList.add('dragover');
    });

    imagePreview.addEventListener('dragleave', (event) => {
        event.preventDefault();
        const related = event.relatedTarget;
        if (related && imagePreview.contains(related)) return;
        imagePreview.classList.remove('dragover');
    });

    imagePreview.addEventListener('drop', (event) => {
        event.preventDefault();
        imagePreview.classList.remove('dragover');
        if (!canManageCatalog || imageFileInput.disabled) return;

        const file = event.dataTransfer?.files?.[0] || null;
        const validation = validateImageFile(file);
        if (!validation.valid) {
            setStatus(validation.message, 'error');
            showToast(validation.message, 'error');
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        imageFileInput.files = dt.files;
        imageFileInput.dispatchEvent(new Event('change'));
    });

    imageFileInput.addEventListener('change', async () => {
        if (!canManageCatalog) {
            setStatus(UI_TEXT.noUploadPermission, 'error');
            return;
        }

        const file = imageFileInput.files && imageFileInput.files[0];
        const validation = validateImageFile(file);
        if (!validation.valid) {
            imageFileInput.value = '';
            setStatus(validation.message, 'error');
            showToast(validation.message, 'error');
            return;
        }

        updateImagePreviewFromFile(file);

        imageFileInput.disabled = true;
        setImagePreviewLoading(true);
        setStatus(UI_TEXT.uploadingImage, null);

        try {
            const payload = await uploadProductImage(file);
            const url = String(payload?.url || '').trim();
            if (!url) {
                throw new Error('La respuesta de carga no incluyo una URL valida.');
            }

            fields.image_url.value = url;
            committedImageUrl = url;
            imageFileInput.value = '';
            updateImagePreview(appendPreviewBuster(url));
            syncUploadedImageInPanel(url);
            setStatus(UI_TEXT.uploadImageOk, 'ok');
            showToast('Imagen subida correctamente.', 'ok');
        } catch (error) {
            setStatus(`No fue posible subir la imagen: ${String(error.message || error)}`, 'error');
            showToast('No fue posible subir la imagen.', 'error');
        } finally {
            setImagePreviewLoading(false);
            imageFileInput.disabled = !canManageCatalog;
        }
    });

    document.getElementById('btnRefresh').addEventListener('click', async () => {
        await loadProducts();
        setStatus(UI_TEXT.refreshed, null);
    });

    btnAdd.addEventListener('click', () => {
        openEditor('add');
    });

    btnEdit.addEventListener('click', () => {
        openEditor('edit');
    });

    btnDelete.addEventListener('click', async () => {
        await removeSelectedProduct();
    });

    tableFilter.addEventListener('input', scheduleApplyFilter);

    tableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-product-id]');
        if (!row) return;

        selectedProductId = Number(row.dataset.productId);
        applyFilter();
        const selected = getSelectedProduct();
        if (selected) {
            setStatus(`Registro seleccionado: ${selected.name}`, null);
        }
    });

    async function init() {
        bindFastFormKeyboardFlow();
        observeMainFieldsLayout();
        window.addEventListener('resize', scheduleMainFieldsLayoutSync);
        window.addEventListener('storage', (event) => {
            if (event.key !== menuCollapsedStorageKey) return;
            if (event.newValue === event.oldValue) return;
            fieldsLayoutIsThreeColumns = null;
            scheduleMainFieldsLayoutSync(true);
        });
        window.addEventListener('focus', () => {
            const menuCollapsed = isMenuCollapsed();
            if (menuCollapsed === lastMenuCollapsedState) return;
            fieldsLayoutIsThreeColumns = null;
            scheduleMainFieldsLayoutSync(true);
        });
        scheduleMainFieldsLayoutSync(true);
        clearForm();
        await loadCapabilities();
        await loadProductTypesCatalog();
        await loadMeasuresCatalog();
        await loadProducts();
        updateActionButtons();
    }

    init();
</script>
</body>
</html>
