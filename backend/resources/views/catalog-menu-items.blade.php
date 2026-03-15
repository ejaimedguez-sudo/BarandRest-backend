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
    <title>Catalogo de Menu Items - Ordena Facil</title>
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
            --fields-layout-3col-min-width: {{ (int) ($layoutClasico['fields_three_columns_min_width'] ?? 1030) }};
            --fields-layout-hysteresis: {{ (int) ($layoutClasico['fields_hysteresis'] ?? 34) }};
            --media-layout-3col-min-width: {{ (int) ($layoutClasico['three_columns_min_width'] ?? 1140) }};
            --media-layout-hysteresis: {{ (int) ($layoutClasico['hysteresis'] ?? 32) }};
        }

        :root[data-theme="premium"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
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
            --fields-layout-3col-min-width: {{ (int) ($layoutPremium['fields_three_columns_min_width'] ?? 1060) }};
            --fields-layout-hysteresis: {{ (int) ($layoutPremium['fields_hysteresis'] ?? 38) }};
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
            max-width: 1240px;
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

        .toolbar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .toolbar input,
        .toolbar select {
            flex: 1;
            min-width: 220px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12px;
            min-height: 32px;
        }

        .toolbar .sort-select {
            flex: 0 0 210px;
            min-width: 180px;
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: scroll;
            height: min(60vh, 560px);
            background: rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 12px;
            vertical-align: top;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: color-mix(in srgb, var(--panel) 88%, #000 12%);
            font-size: 11px;
            color: var(--muted);
            letter-spacing: .2px;
            white-space: nowrap;
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

        .description-cell {
            min-width: 260px;
            max-width: 420px;
            white-space: normal;
            line-height: 1.45;
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

        .bool-badge {
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 3px 9px;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
        }

        .bool-badge.yes {
            border-color: rgba(16, 185, 129, 0.45);
            background: rgba(16, 185, 129, 0.12);
            color: var(--ok-text);
        }

        .bool-badge.no {
            border-color: rgba(148, 163, 184, 0.35);
            background: rgba(148, 163, 184, 0.08);
            color: var(--muted);
        }

        .margin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid transparent;
            padding: 3px 9px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .margin-good {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.45);
            color: var(--ok-text);
        }

        .margin-mid {
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.45);
            color: #b45309;
        }

        :root[data-theme="premium"] .margin-mid {
            color: #fcd34d;
        }

        .margin-low {
            background: var(--warn-bg);
            border-color: var(--warn-border);
            color: var(--warn-text);
        }

        .frame-footer {
            margin-top: 8px;
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .panel .head .btn,
        .frame-footer .btn,
        .editor-head .btn,
        .form-actions .btn,
        .recipe-actions .btn,
        .recipe-footer .btn,
        .profit-foot .btn,
        .history-filters .btn,
        .editor-footer .btn {
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

        .empty {
            padding: 14px;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        .editor-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.58);
            z-index: 3090;
            display: none;
        }

        .editor-overlay.active {
            display: block;
        }

        .editor-frame {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(.98);
            width: min(1180px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(155deg, var(--panel), var(--panel-soft));
            box-shadow: 0 28px 50px rgba(0, 0, 0, 0.35);
            z-index: 3100;
            padding: 10px;
            display: none;
            overflow: auto;
        }

        .editor-frame.active {
            display: grid;
            gap: 8px;
            animation: popIn .2s ease-out;
        }

        .editor-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 6px;
        }

        .editor-head h2 {
            margin: 0;
            font-size: 16px;
            letter-spacing: .2px;
        }

        .editor-body {
            overflow: auto;
            max-height: calc(100vh - 150px);
            padding-right: 2px;
            container-type: inline-size;
            display: grid;
            gap: 8px;
        }

        .editor-columns {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(360px, 0.95fr);
            gap: 10px;
            align-items: start;
        }

        .editor-main {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.03);
            min-width: 0;
            container-type: inline-size;
            display: grid;
            gap: 6px;
        }

        .editor-main-layout {
            display: grid;
            grid-template-columns: minmax(300px, .9fr) minmax(0, 1.3fr);
            gap: 10px 12px;
            align-items: start;
        }

        .editor-main-layout > .media-field {
            grid-column: 1;
            margin: 0;
        }

        .editor-main-layout > .form-grid {
            grid-column: 2;
            min-width: 0;
        }

        .editor-main h3 {
            margin: 0;
            font-size: 14px;
        }

        .recipe-panel {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            gap: 6px;
            min-width: 0;
            container-type: inline-size;
        }

        .recipe-panel.hidden {
            display: none;
        }

        .recipe-panel h3 {
            margin: 0;
            font-size: 14px;
        }

        .recipe-hint {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.4;
        }

        .recipe-table-wrap {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: auto;
            max-height: 260px;
            background: rgba(0, 0, 0, 0.05);
        }

        .recipe-table {
            width: 100%;
            min-width: 420px;
            border-collapse: collapse;
        }

        .recipe-table th,
        .recipe-table td {
            padding: 8px;
            font-size: 12px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .recipe-table th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .recipe-table tr {
            cursor: pointer;
        }

        .recipe-table tr.selected {
            background: rgba(242, 145, 27, 0.22);
        }

        .recipe-controls {
            border-top: 1px dashed var(--border);
            padding-top: 8px;
            display: grid;
            gap: 8px;
        }

        .recipe-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .recipe-controls-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(120px, 0.7fr) minmax(120px, 0.8fr);
            gap: 8px;
        }

        .recipe-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .profit-panel {
            border: 1px dashed var(--border);
            border-radius: 10px;
            padding: 8px;
            display: grid;
            gap: 8px;
            background: rgba(255, 255, 255, 0.03);
        }

        .profit-panel h4 {
            margin: 0;
            font-size: 13px;
        }

        .profit-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .profit-foot {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        .profit-meta {
            font-size: 12px;
            color: var(--muted);
        }

        .recipe-status {
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 8px;
            font-size: 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.03);
            min-height: 36px;
            white-space: pre-wrap;
        }

        .recipe-status.error {
            background: var(--warn-bg);
            border-color: var(--warn-border);
            color: var(--warn-text);
        }

        .recipe-status.ok {
            background: var(--ok-bg);
            border-color: var(--ok-border);
            color: var(--ok-text);
        }

        .history-panel {
            border: 1px dashed var(--border);
            border-radius: 10px;
            padding: 8px;
            display: grid;
            gap: 8px;
            background: rgba(255, 255, 255, 0.03);
        }

        .history-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .history-head h4 {
            margin: 0;
            font-size: 13px;
        }

        .history-meta {
            font-size: 12px;
            color: var(--muted);
        }

        .history-filters {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 8px;
            align-items: end;
        }

        .history-filters .field {
            gap: 2px;
        }

        .history-filters .field label {
            font-size: 11px;
        }

        .history-list {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: auto;
            max-height: 160px;
            background: rgba(0, 0, 0, 0.05);
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px;
        }

        .history-table th,
        .history-table td {
            padding: 8px;
            font-size: 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        .history-table th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: color-mix(in srgb, var(--panel) 88%, #000 12%);
            color: var(--muted);
        }

        .history-empty {
            padding: 10px;
            font-size: 12px;
            color: var(--muted);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--form-field-row-gap) var(--form-field-col-gap);
            align-items: start;
        }

        .form-grid.cols-1 {
            grid-template-columns: 1fr;
        }

        .form-grid.cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-grid.cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .media-field {
            border: 1px dashed var(--border);
            border-radius: 10px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
            gap: 10px 12px;
            align-items: start;
        }

        .media-preview {
            grid-column: 1;
            grid-row: auto;
        }

        .media-controls {
            grid-column: 2;
            display: grid;
            gap: 8px;
            min-width: 0;
        }

        .media-field.layout-3 {
            grid-template-columns: minmax(300px, 390px) minmax(0, 1fr) minmax(0, 1fr);
        }

        .media-field.layout-3 .media-controls {
            grid-column: 2 / -1;
        }

        .media-input-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            align-items: center;
        }

        .media-upload-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            align-items: center;
        }

        .media-fit-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .media-fit-switch {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 2px;
            background: rgba(255, 255, 255, 0.04);
        }

        .media-fit-btn {
            border: 0;
            background: transparent;
            color: var(--muted);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            line-height: 1.2;
            cursor: pointer;
        }

        .media-fit-btn.active {
            background: rgba(242, 145, 27, 0.24);
            color: var(--text);
        }

        .media-fit-btn:focus-visible {
            outline: 2px solid rgba(240, 184, 3, 0.5);
            outline-offset: 1px;
        }

        .media-input-row input {
            width: 100%;
        }

        .media-upload-row input {
            width: 100%;
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
            min-height: 230px;
            height: clamp(230px, 34vh, 360px);
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
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .media-preview.fit-contain img {
            object-fit: contain;
            background: rgba(0, 0, 0, 0.18);
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

        .field-wide,
        .form-actions {
            grid-column: 1 / -1;
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
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--text);
            padding: 6px 8px;
            font: inherit;
            font-size: 12.5px;
            min-height: var(--form-input-min-height);
        }

        .field textarea {
            min-height: 86px;
            resize: vertical;
        }

        .field-check {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding-top: 2px;
        }

        .field-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--c2);
        }

        .form-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 2px;
        }

        .editor-footer {
            border-top: 1px solid var(--border);
            padding-top: 10px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translate(-50%, -49%) scale(.98);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @media (max-width: 860px) {
            .table-wrap {
                height: min(52vh, 460px);
            }

            .editor-columns {
                grid-template-columns: 1fr;
            }

            .form-grid,
            .form-grid.cols-2,
            .form-grid.cols-3 {
                grid-template-columns: 1fr;
            }

            .editor-main-layout {
                grid-template-columns: 1fr;
            }

            .editor-main-layout > .media-field,
            .editor-main-layout > .form-grid {
                grid-column: 1;
            }

            .field label {
                min-height: 0;
            }

            .recipe-controls-grid {
                grid-template-columns: 1fr;
            }

            .history-filters {
                grid-template-columns: 1fr;
            }

            .profit-grid {
                grid-template-columns: 1fr;
            }

            .field-wide,
            .form-actions {
                grid-column: auto;
            }

            .frame-footer {
                justify-content: stretch;
            }
        }

        @media (max-width: 1200px) {
            .editor-columns {
                grid-template-columns: 1fr;
            }

            .editor-main-layout {
                grid-template-columns: 1fr;
            }

            .editor-main-layout > .media-field,
            .editor-main-layout > .form-grid {
                grid-column: 1;
            }

            .form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .recipe-table {
                min-width: 100%;
            }
        }

        @media (max-width: 760px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .media-input-row,
            .media-upload-row {
                grid-template-columns: 1fr;
            }

            .media-fit-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .media-field {
                grid-template-columns: 1fr;
            }

            .media-field.layout-3 {
                grid-template-columns: 1fr;
            }

            .media-preview,
            .media-controls {
                grid-column: 1;
            }

            .media-field.layout-3 .media-controls {
                grid-column: 1;
            }

            .media-preview {
                grid-row: auto;
            }

            .field-wide,
            .form-actions {
                grid-column: auto;
            }

            .recipe-controls-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .recipe-footer {
                justify-content: stretch;
            }

            .recipe-footer .btn {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body class="catalog-standard">
<main class="wrap">
    <section class="panel">
        <div class="head">
            <span class="badge">Vista operativa</span>
        </div>
        <div class="body">
            <div class="toolbar">
                <input id="tableFilter" type="search" placeholder="Buscar por ID, codigo, nombre o descripcion...">
                <select id="sortField" class="sort-select" aria-label="Ordenar por campo">
                    <option value="id">Ordenar por ID</option>
                    <option value="code">Ordenar por Codigo</option>
                    <option value="description">Ordenar por Descripcion</option>
                </select>
                <select id="sortDir" class="sort-select" aria-label="Direccion de orden">
                    <option value="asc">Ascendente</option>
                    <option value="desc">Descendente</option>
                </select>
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
    </section>
</main>

<div id="editorOverlay" class="editor-overlay" aria-hidden="true"></div>

<section id="editorFrame" class="editor-frame" aria-hidden="true">
    <div class="editor-head">
        <h2 id="formTitle">Nuevo item de menu</h2>
        <button id="btnCloseEditor" class="btn btn-compact" type="button">Cerrar</button>
    </div>
    <div class="editor-body">
        <div class="editor-columns">
            <div class="editor-main">
                <h3>Datos generales del item</h3>
                <div class="editor-main-layout">
                    <div class="field media-field">
                        <div id="itemImagePreview" class="media-preview" role="button" tabindex="0" title="Haz clic para seleccionar una imagen" aria-label="Seleccionar imagen del menu item">
                            <span class="media-empty">Sin imagen seleccionada</span>
                        </div>
                        <div class="media-controls">
                            <label for="image_url">Imagen del menu item</label>
                            <div class="media-input-row">
                                <input id="image_url" name="image_url" type="text" maxlength="2048" placeholder="https://.../item.jpg">
                                <button id="btnSearchItemImage" class="btn btn-compact" type="button">Buscar imagen</button>
                            </div>
                            <div class="media-upload-row">
                                <input id="item_image_file" name="item_image_file" class="media-file-input-hidden" type="file" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="media-fit-row">
                                <span class="field-hint">Ajuste de imagen en marco</span>
                                <div class="media-fit-switch" role="group" aria-label="Ajuste de imagen">
                                    <button id="btnItemImageFitCover" class="media-fit-btn active" type="button" aria-pressed="true">Llenar</button>
                                    <button id="btnItemImageFitContain" class="media-fit-btn" type="button" aria-pressed="false">Completa</button>
                                </div>
                            </div>
                            <p class="field-hint">Haz clic en el marco para seleccionar imagen, o pega una URL. Formatos JPG/PNG/WEBP (max 5 MB).</p>
                        </div>
                    </div>

                    <form id="menuItemForm" class="form-grid">
                        <input id="menuItemId" type="hidden">

                        <div class="field">
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
                        <label for="menu_category_id">Categoria de menu</label>
                        <select id="menu_category_id" name="menu_category_id">
                            <option value="">Sin categoria</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="code">Codigo</label>
                        <input id="code" name="code" type="text" maxlength="100" pattern="[A-Za-z0-9\-_.]+" title="Solo letras, numeros, guion, guion bajo y punto" placeholder="MI-001">
                    </div>

                    <div class="field">
                        <label for="price" class="required">Precio</label>
                        <input id="price" name="price" type="number" step="0.01" min="0" required>
                    </div>

                    <div class="field">
                        <label for="manual_cost">Costo manual</label>
                        <input id="manual_cost" name="manual_cost" type="number" step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div class="field">
                        <label for="cost">Costo calculado</label>
                        <input id="cost" name="cost" type="number" step="0.01" min="0" placeholder="0.00" readonly>
                    </div>

                    <div class="field field-check">
                        <input id="is_recipe" name="is_recipe" type="checkbox">
                        <label for="is_recipe">Manejar como receta</label>
                    </div>

                    <div class="field">
                        <label for="prep_time_minutes">Tiempo de preparacion (min)</label>
                        <input id="prep_time_minutes" name="prep_time_minutes" type="number" min="0" max="1440" step="1" placeholder="0">
                    </div>

                    <div class="field">
                        <label for="dish">Plato</label>
                        <input id="dish" name="dish" type="text" maxlength="120" placeholder="Entrada, fuerte, postre...">
                    </div>

                    <div class="field">
                        <label for="kitchen">Cocina</label>
                        <input id="kitchen" name="kitchen" type="text" maxlength="120" placeholder="Fria, caliente, bar...">
                    </div>

                    <div class="field">
                        <label for="servings">Raciones</label>
                        <input id="servings" name="servings" type="number" min="0" max="1000" step="1" placeholder="0">
                    </div>

                    <div class="field">
                        <label for="calories">Calorias</label>
                        <input id="calories" name="calories" type="number" min="0" max="100000" step="1" placeholder="0">
                    </div>

                    <div class="field">
                        <label for="equipment">Equipo</label>
                        <input id="equipment" name="equipment" type="text" maxlength="255" placeholder="Shaker, horno, parrilla...">
                    </div>

                        <div class="field field-wide">
                            <label for="description">Descripcion</label>
                            <textarea id="description" name="description" maxlength="1000" placeholder="Detalle breve del item"></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <aside id="recipePanel" class="recipe-panel hidden">
                <h3>Materiales de receta</h3>
                <p class="recipe-hint" id="recipeHint">Activa "Manejar como receta" para administrar materiales.</p>

                <div id="recipeTableContainer" class="recipe-table-wrap">
                    <div class="empty">Sin materiales agregados.</div>
                </div>

                <div class="recipe-controls">
                    <input id="ingredientId" type="hidden">
                    <div class="recipe-controls-grid">
                        <div class="field">
                            <label for="ingredient_product_id" class="required">Material de inventario</label>
                            <select id="ingredient_product_id" name="ingredient_product_id">
                                <option value="">Selecciona material</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="ingredient_quantity" class="required">Cantidad</label>
                            <input id="ingredient_quantity" name="ingredient_quantity" type="number" min="0.001" step="0.001" placeholder="0.000">
                        </div>
                        <div class="field">
                            <label for="ingredient_yield">Rendimiento (cocteles/L)</label>
                            <input id="ingredient_yield" name="ingredient_yield" type="number" min="0.001" step="0.001" placeholder="Ej. 20">
                        </div>
                        <div class="field">
                            <label for="ingredient_consumption_ml">Consumo (ml por coctel)</label>
                            <input id="ingredient_consumption_ml" name="ingredient_consumption_ml" type="number" min="0.001" step="0.001" readonly>
                        </div>
                        <div class="field">
                            <label for="ingredient_unit">Unidad</label>
                            <input id="ingredient_unit" name="ingredient_unit" type="text" maxlength="50" placeholder="ml, g, pza">
                        </div>
                    </div>

                    <div class="recipe-actions">
                        <button id="btnIngredientSave" class="btn btn-compact btn-add" type="button">Agregar material</button>
                        <button id="btnIngredientEdit" class="btn btn-compact btn-edit" type="button">Editar seleccionado</button>
                        <button id="btnIngredientDelete" class="btn btn-compact btn-delete" type="button">Eliminar seleccionado</button>
                    </div>

                    <div class="recipe-footer">
                        <button id="btnIngredientCancel" class="btn btn-compact" type="button">Cancelar</button>
                    </div>
                </div>

                <section class="profit-panel">
                    <h4>Rentabilidad</h4>
                    <div class="profit-grid">
                        <div class="field">
                            <label for="recipe_cost_total">Costo receta</label>
                            <input id="recipe_cost_total" type="number" step="0.01" min="0" readonly>
                        </div>
                        <div class="field">
                            <label for="profit_margin_percent">Margen utilidad (%)</label>
                            <input id="profit_margin_percent" type="number" step="0.01" min="0" max="99.99" placeholder="30.00">
                        </div>
                        <div class="field">
                            <label for="retail_suggested">Retail sugerido</label>
                            <input id="retail_suggested" type="number" step="0.01" min="0" readonly>
                        </div>
                    </div>
                    <div class="profit-foot">
                        <span id="profitMeta" class="profit-meta">Utilidad estimada: -</span>
                        <button id="btnApplyRetail" class="btn btn-compact" type="button">Aplicar retail a precio</button>
                    </div>
                </section>

                <section class="history-panel">
                    <div class="history-head">
                        <h4>Historial de costo receta</h4>
                        <span id="recipeHistoryMeta" class="history-meta">Sin cambios registrados.</span>
                    </div>
                    <div class="history-filters">
                        <div class="field">
                            <label for="historyActionFilter">Accion</label>
                            <select id="historyActionFilter" name="historyActionFilter">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="historyDateFromFilter">Desde</label>
                            <input id="historyDateFromFilter" name="historyDateFromFilter" type="date">
                        </div>
                        <div class="field">
                            <label for="historyDateToFilter">Hasta</label>
                            <input id="historyDateToFilter" name="historyDateToFilter" type="date">
                        </div>
                        <button id="btnHistoryClearFilters" class="btn btn-compact" type="button">Limpiar filtros</button>
                    </div>
                    <div id="recipeHistoryContainer" class="history-list">
                        <div class="history-empty">No hay historial disponible.</div>
                    </div>
                </section>

                <div id="recipeStatus" class="recipe-status">Guarda un item como receta para administrar sus materiales.</div>
            </aside>
        </div>

        <div class="editor-footer">
            <button id="btnCancelEdit" class="btn btn-compact" type="button">Cancelar</button>
            <button id="btnSubmit" class="btn btn-compact btn-add" type="submit" form="menuItemForm">Guardar item</button>
        </div>
    </div>
</section>

<script>
    const marginTrafficLightConfig = @json(config('profitability.menu_item_margin_traffic_light', ['good' => 25, 'mid' => 15]));
    const urlParams = new URLSearchParams(window.location.search);
    const deviationMinParam = Number(urlParams.get('deviationMin') || 0);
    const deviationFilterMin = Number.isFinite(deviationMinParam) && deviationMinParam > 0 ? deviationMinParam / 100 : 0;
    const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
    const roleBadge = document.getElementById('roleBadge');
    const tableContainer = document.getElementById('tableContainer');
    const tableFilter = document.getElementById('tableFilter');
    const sortField = document.getElementById('sortField');
    const sortDir = document.getElementById('sortDir');
    const statusBox = document.getElementById('status');
    const btnRefresh = document.getElementById('btnRefresh');
    const btnAdd = document.getElementById('btnAdd');
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');

    const editorOverlay = document.getElementById('editorOverlay');
    const editorFrame = document.getElementById('editorFrame');
    const formTitle = document.getElementById('formTitle');
    const menuItemForm = document.getElementById('menuItemForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const btnCloseEditor = document.getElementById('btnCloseEditor');
    const btnSearchItemImage = document.getElementById('btnSearchItemImage');
    const itemImageFileInput = document.getElementById('item_image_file');
    const itemImagePreview = document.getElementById('itemImagePreview');
    const btnItemImageFitCover = document.getElementById('btnItemImageFitCover');
    const btnItemImageFitContain = document.getElementById('btnItemImageFitContain');
    const recipePanel = document.getElementById('recipePanel');
    const recipeHint = document.getElementById('recipeHint');
    const recipeTableContainer = document.getElementById('recipeTableContainer');
    const recipeStatus = document.getElementById('recipeStatus');
    const ingredientId = document.getElementById('ingredientId');
    const ingredientProductId = document.getElementById('ingredient_product_id');
    const ingredientQuantity = document.getElementById('ingredient_quantity');
    const ingredientYield = document.getElementById('ingredient_yield');
    const ingredientConsumptionMl = document.getElementById('ingredient_consumption_ml');
    const ingredientUnit = document.getElementById('ingredient_unit');
    const btnIngredientSave = document.getElementById('btnIngredientSave');
    const btnIngredientEdit = document.getElementById('btnIngredientEdit');
    const btnIngredientCancel = document.getElementById('btnIngredientCancel');
    const btnIngredientDelete = document.getElementById('btnIngredientDelete');
    const recipeCostTotal = document.getElementById('recipe_cost_total');
    const profitMarginPercent = document.getElementById('profit_margin_percent');
    const retailSuggested = document.getElementById('retail_suggested');
    const profitMeta = document.getElementById('profitMeta');
    const btnApplyRetail = document.getElementById('btnApplyRetail');
    const recipeHistoryContainer = document.getElementById('recipeHistoryContainer');
    const recipeHistoryMeta = document.getElementById('recipeHistoryMeta');
    const historyActionFilter = document.getElementById('historyActionFilter');
    const historyDateFromFilter = document.getElementById('historyDateFromFilter');
    const historyDateToFilter = document.getElementById('historyDateToFilter');
    const btnHistoryClearFilters = document.getElementById('btnHistoryClearFilters');
    const UI_TEXT = {
        noManagePermission: 'No tienes permisos para crear o editar registros del catalogo.',
        noEditSelection: 'Selecciona un registro para editar.',
        noDeleteSelection: 'Selecciona un registro para eliminar.',
        noUploadPermission: 'No tienes permisos para subir imagenes.',
        noManageRole: 'Tu rol actual no tiene permisos para administrar el catalogo.',
        ready: 'Selecciona un registro y usa Agregar, Editar o Eliminar.',
        deleted: 'Registro eliminado correctamente.',
        updated: 'Registro actualizado correctamente.',
        created: 'Registro creado correctamente.',
        createdWithRecipe: 'Registro y receta guardados correctamente.',
        createdWithMaterials: 'Registro creado con materiales; receta lista para ajustes.',
        createdNeedsRecipe: 'Registro creado. Ahora agrega materiales de receta.',
        canceled: 'Edicion cancelada.',
        refreshed: 'Listado actualizado.',
        uploadingImage: 'Subiendo imagen del menu item...',
        imageUploaded: 'Imagen subida correctamente. Guarda el item para persistir el enlace.',
    };

    const fields = {
        id: document.getElementById('menuItemId'),
        name: document.getElementById('name'),
        product_type_id: document.getElementById('product_type_id'),
        menu_category_id: document.getElementById('menu_category_id'),
        code: document.getElementById('code'),
        image_url: document.getElementById('image_url'),
        price: document.getElementById('price'),
        manual_cost: document.getElementById('manual_cost'),
        cost: document.getElementById('cost'),
        is_recipe: document.getElementById('is_recipe'),
        prep_time_minutes: document.getElementById('prep_time_minutes'),
        dish: document.getElementById('dish'),
        kitchen: document.getElementById('kitchen'),
        servings: document.getElementById('servings'),
        calories: document.getElementById('calories'),
        equipment: document.getElementById('equipment'),
        description: document.getElementById('description'),
    };

    let items = [];
    let productsCatalog = [];
    let productTypesCatalog = [];
    let menuCategoriesCatalog = [];
    let ingredients = [];
    let costHistory = [];
    let historyActionsCatalog = [];
    let draftIngredientSeq = 0;
    let selectedItemId = null;
    let selectedIngredientId = null;
    let canManageCatalog = false;
    let currentSortField = 'id';
    let currentSortDir = 'asc';
    let committedItemImageUrl = '';
    let menuItemPreviewToken = 0;
    let itemImageFitMode = 'cover';
    let mediaLayoutObserver = null;
    let mediaLayoutIsThreeColumns = null;
    let fieldsLayoutIsThreeColumns = null;
    let mediaLayoutRafId = 0;
    let lastMediaLayoutViewportWidth = null;
    let lastMediaLayoutFrameWidth = null;
    let tableFilterDebounceId = 0;
    let appliedMediaLayoutIsThreeColumns = null;
    const defaultFieldsLayoutThreeColumnsMinWidth = 1030;
    const defaultFieldsLayoutHysteresisPx = 34;
    const defaultMediaLayoutThreeColumnsMinWidth = 1160;
    const defaultMediaLayoutHysteresisPx = 36;
    const catalogCachePrefix = `barandrest:catalog-cache:${role}:`;
    const catalogCacheTtlMs = 5 * 60 * 1000;
    const itemImageFitStorageLegacyKey = 'barandrest:catalog-menu-items:image-fit';
    const itemImageFitStorageKey = `${itemImageFitStorageLegacyKey}:${role}`;
    const maxUploadSizeBytes = 5 * 1024 * 1024;
    const allowedUploadTypes = new Set(['image/jpeg', 'image/png', 'image/webp']);
    const toastRoot = document.createElement('div');
    toastRoot.className = 'toast-stack';
    document.body.appendChild(toastRoot);
    const sortPreferenceKey = `ordena-facil-menu-items-sort-${role}`;

    if (roleBadge) {
        roleBadge.textContent = `Rol activo: ${role}`;
    }

    function setStatus(message, type = null) {
        statusBox.textContent = message;
        statusBox.classList.remove('ok', 'error');
        if (type) statusBox.classList.add(type);
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

    function formatMoney(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '-';
        return number.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatQuantity(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '-';
        return number.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 3 });
    }

    function formatPercent(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '-';
        return `${number.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`;
    }

    function getMarginTone(margin) {
        const goodThreshold = Number(marginTrafficLightConfig?.good);
        const midThreshold = Number(marginTrafficLightConfig?.mid);
        const safeGood = Number.isFinite(goodThreshold) ? goodThreshold : 25;
        const safeMid = Number.isFinite(midThreshold) ? midThreshold : 15;
        const value = Number(margin);
        if (!Number.isFinite(value)) return { cls: 'margin-low', label: 'Sin margen' };
        if (value >= Math.max(safeGood, safeMid)) return { cls: 'margin-good', label: 'Saludable' };
        if (value >= Math.min(safeGood, safeMid)) return { cls: 'margin-mid', label: 'Vigilancia' };
        return { cls: 'margin-low', label: 'Riesgo' };
    }

    function formatDateTime(value) {
        if (!value) return '-';
        const dt = new Date(value);
        if (Number.isNaN(dt.getTime())) return '-';
        return dt.toLocaleString();
    }

    function formatDifference(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return '-';
        const prefix = number > 0 ? '+' : '';
        return `${prefix}${formatMoney(number)}`;
    }

    function normalizeHistoryDateInput(value) {
        if (!value || typeof value !== 'string') return null;
        const match = value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) return null;

        const year = Number(match[1]);
        const month = Number(match[2]);
        const day = Number(match[3]);
        if (!Number.isFinite(year) || !Number.isFinite(month) || !Number.isFinite(day)) return null;
        return new Date(year, month - 1, day);
    }

    function getHistoryActionLabel(action) {
        const key = String(action || '').trim();
        const labels = {
            ingredient_created: 'Material agregado',
            ingredient_updated: 'Material editado',
            ingredient_deleted: 'Material eliminado',
        };
        return labels[key] || (key || 'Sin accion');
    }

    function buildHistoryActionsCatalog() {
        const set = new Set();
        costHistory.forEach((entry) => {
            const action = String(entry?.action || '').trim();
            if (action) set.add(action);
        });
        historyActionsCatalog = [...set].sort((a, b) => a.localeCompare(b));
    }

    function renderHistoryActionFilterOptions() {
        const currentValue = String(historyActionFilter.value || '');
        const options = historyActionsCatalog
            .map((action) => `<option value="${escapeHtml(action)}">${escapeHtml(getHistoryActionLabel(action))}</option>`)
            .join('');
        historyActionFilter.innerHTML = `<option value="">Todas</option>${options}`;

        if (currentValue && historyActionsCatalog.includes(currentValue)) {
            historyActionFilter.value = currentValue;
            return;
        }

        historyActionFilter.value = '';
    }

    function getFilteredCostHistory() {
        const action = String(historyActionFilter.value || '').trim();
        const fromDate = normalizeHistoryDateInput(historyDateFromFilter.value);
        const toDate = normalizeHistoryDateInput(historyDateToFilter.value);

        return costHistory.filter((entry) => {
            if (action && String(entry?.action || '').trim() !== action) {
                return false;
            }

            if (!fromDate && !toDate) {
                return true;
            }

            const createdAt = new Date(entry?.created_at || '');
            if (Number.isNaN(createdAt.getTime())) {
                return false;
            }

            const day = new Date(createdAt.getFullYear(), createdAt.getMonth(), createdAt.getDate());
            if (fromDate && day < fromDate) {
                return false;
            }

            if (toDate && day > toDate) {
                return false;
            }

            return true;
        });
    }

    function renderCostHistory() {
        if (!costHistory.length) {
            recipeHistoryContainer.innerHTML = '<div class="history-empty">No hay historial disponible.</div>';
            recipeHistoryMeta.textContent = 'Sin cambios registrados.';
            historyActionsCatalog = [];
            renderHistoryActionFilterOptions();
            return;
        }

        buildHistoryActionsCatalog();
        renderHistoryActionFilterOptions();

        const filteredHistory = getFilteredCostHistory();
        if (!filteredHistory.length) {
            recipeHistoryContainer.innerHTML = '<div class="history-empty">No hay registros para los filtros aplicados.</div>';
            recipeHistoryMeta.textContent = `0 de ${costHistory.length} movimientos`;
            return;
        }

        recipeHistoryMeta.textContent = `${filteredHistory.length} de ${costHistory.length} movimientos`;
        const rows = filteredHistory.map((entry) => `
            <tr>
                <td>${formatDateTime(entry.created_at)}</td>
                <td>${escapeHtml(getHistoryActionLabel(entry.action))}</td>
                <td>${escapeHtml(asText(entry.actor_role))}</td>
                <td>${formatMoney(entry.previous_cost)}</td>
                <td>${formatMoney(entry.new_cost)}</td>
                <td>${formatDifference(entry.difference)}</td>
            </tr>
        `).join('');

        recipeHistoryContainer.innerHTML = `
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Accion</th>
                        <th>Rol</th>
                        <th>Costo anterior</th>
                        <th>Costo nuevo</th>
                        <th>Diferencia</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `;
    }

    function getDeviationRatio(item) {
        const manual = Number(item?.manual_cost ?? 0);
        const calculated = Number(item?.cost ?? 0);
        if (!Number.isFinite(manual) || !Number.isFinite(calculated) || calculated <= 0) {
            return 0;
        }
        return Math.abs(manual - calculated) / calculated;
    }

    function renderMarginCell(item) {
        const tone = getMarginTone(item?.profit_margin_percent);
        const value = formatPercent(item?.profit_margin_percent);
        return `<span class="margin-badge ${tone.cls}" title="${tone.label}">${value}</span>`;
    }

    function asText(value) {
        if (value === null || value === undefined || value === '') return '-';
        return String(value);
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function normalizeNumber(value) {
        if (value === null || value === undefined || value === '') return null;
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : null;
    }

    function toMoney(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return null;
        return Math.round(number * 100) / 100;
    }

    function toMargin(value) {
        const number = Number(value);
        if (!Number.isFinite(number)) return null;
        return Math.round(number * 100) / 100;
    }

    function requestJson(url, options = {}) {
        const headers = {
            Accept: 'application/json',
            'X-USER-ROLE': role,
            ...options.headers,
        };

        return fetch(url, { ...options, headers }).then(async (response) => {
            if (!response.ok) {
                let message = `HTTP ${response.status}`;

                try {
                    const payload = await response.json();
                    const errors = payload?.errors && typeof payload.errors === 'object' ? payload.errors : null;
                    if (errors) {
                        const first = Object.values(errors).flat().find(Boolean);
                        if (first) message = String(first);
                    } else if (payload?.message) {
                        message = String(payload.message);
                    }
                } catch (_error) {
                    const text = await response.text();
                    if (text) message = `${message} - ${text}`;
                }

                throw new Error(message);
            }

            if (response.status === 204) return null;
            return response.json();
        });
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

    function getSelectedItem() {
        return items.find((item) => Number(item.id) === Number(selectedItemId)) || null;
    }

    function updateActionButtons() {
        const hasSelection = !!getSelectedItem();

        btnAdd.disabled = !canManageCatalog;
        btnEdit.disabled = !canManageCatalog || !hasSelection;
        btnDelete.disabled = !canManageCatalog || !hasSelection;
        updateRecipeControlsState();
    }

    function readCssNumberVar(name, fallback) {
        const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    function syncMediaFieldLayout(force = false) {
        const frameWidth = Math.round(editorFrame.getBoundingClientRect().width || 0);
        const viewportWidth = Math.round(window.innerWidth || 0);
        if (!force && frameWidth === lastMediaLayoutFrameWidth && viewportWidth === lastMediaLayoutViewportWidth) {
            return;
        }
        lastMediaLayoutFrameWidth = frameWidth;
        lastMediaLayoutViewportWidth = viewportWidth;
        const fieldsLayoutThreeColumnsMinWidth = readCssNumberVar('--fields-layout-3col-min-width', defaultFieldsLayoutThreeColumnsMinWidth);
        const fieldsLayoutHysteresisPx = readCssNumberVar('--fields-layout-hysteresis', defaultFieldsLayoutHysteresisPx);
        const mediaLayoutThreeColumnsMinWidth = readCssNumberVar('--media-layout-3col-min-width', defaultMediaLayoutThreeColumnsMinWidth);
        const mediaLayoutHysteresisPx = readCssNumberVar('--media-layout-hysteresis', defaultMediaLayoutHysteresisPx);

        if (viewportWidth <= 760) {
            fieldsLayoutIsThreeColumns = false;
            mediaLayoutIsThreeColumns = false;
            menuItemForm.classList.remove('cols-2', 'cols-3');
            menuItemForm.classList.add('cols-1');
        } else if (viewportWidth <= 1200) {
            fieldsLayoutIsThreeColumns = false;
            mediaLayoutIsThreeColumns = false;
            menuItemForm.classList.remove('cols-1', 'cols-3');
            menuItemForm.classList.add('cols-2');
        } else {
            menuItemForm.classList.remove('cols-1');

            if (fieldsLayoutIsThreeColumns === null) {
                fieldsLayoutIsThreeColumns = frameWidth >= fieldsLayoutThreeColumnsMinWidth;
            } else if (fieldsLayoutIsThreeColumns && frameWidth <= (fieldsLayoutThreeColumnsMinWidth - fieldsLayoutHysteresisPx)) {
                fieldsLayoutIsThreeColumns = false;
            } else if (!fieldsLayoutIsThreeColumns && frameWidth >= (fieldsLayoutThreeColumnsMinWidth + fieldsLayoutHysteresisPx)) {
                fieldsLayoutIsThreeColumns = true;
            }

            menuItemForm.classList.toggle('cols-3', Boolean(fieldsLayoutIsThreeColumns));
            menuItemForm.classList.toggle('cols-2', !Boolean(fieldsLayoutIsThreeColumns));

            if (mediaLayoutIsThreeColumns === null) {
                mediaLayoutIsThreeColumns = frameWidth >= mediaLayoutThreeColumnsMinWidth;
            } else if (mediaLayoutIsThreeColumns && frameWidth <= (mediaLayoutThreeColumnsMinWidth - mediaLayoutHysteresisPx)) {
                mediaLayoutIsThreeColumns = false;
            } else if (!mediaLayoutIsThreeColumns && frameWidth >= (mediaLayoutThreeColumnsMinWidth + mediaLayoutHysteresisPx)) {
                mediaLayoutIsThreeColumns = true;
            }
        }

        const mediaLayoutChanged = appliedMediaLayoutIsThreeColumns !== Boolean(mediaLayoutIsThreeColumns);
        if (mediaLayoutChanged || force) {
            document.querySelectorAll('.media-field').forEach((field) => {
                field.classList.toggle('layout-3', Boolean(mediaLayoutIsThreeColumns));
            });
            appliedMediaLayoutIsThreeColumns = Boolean(mediaLayoutIsThreeColumns);
        }
    }

    function scheduleMediaFieldLayoutSync(force = false) {
        if (force) {
            lastMediaLayoutViewportWidth = null;
            lastMediaLayoutFrameWidth = null;
            appliedMediaLayoutIsThreeColumns = null;
        }

        if (mediaLayoutRafId) return;

        mediaLayoutRafId = window.requestAnimationFrame(() => {
            mediaLayoutRafId = 0;
            syncMediaFieldLayout(force);
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

    function observeEditorResize() {
        if (typeof ResizeObserver !== 'function' || mediaLayoutObserver) {
            return;
        }

        mediaLayoutObserver = new ResizeObserver(() => {
            scheduleMediaFieldLayoutSync();
        });

        mediaLayoutObserver.observe(editorFrame);
    }

    function saveSortPreferences() {
        const payload = {
            field: currentSortField,
            dir: currentSortDir,
        };

        try {
            localStorage.setItem(sortPreferenceKey, JSON.stringify(payload));
        } catch (_error) {
            // No action required if storage is unavailable.
        }
    }

    function loadSortPreferences() {
        try {
            const raw = localStorage.getItem(sortPreferenceKey);
            if (!raw) return;

            const parsed = JSON.parse(raw);
            const validField = ['id', 'code', 'description'].includes(parsed?.field) ? parsed.field : null;
            const validDir = ['asc', 'desc'].includes(parsed?.dir) ? parsed.dir : null;

            if (validField) currentSortField = validField;
            if (validDir) currentSortDir = validDir;
        } catch (_error) {
            // No action required if stored value is invalid.
        }
    }

    function setRecipeStatus(message, type = null) {
        recipeStatus.textContent = message;
        recipeStatus.classList.remove('ok', 'error');
        if (type) recipeStatus.classList.add(type);
    }

    function currentEditingMenuItemId() {
        const id = Number(fields.id.value);
        return Number.isFinite(id) && id > 0 ? id : null;
    }

    function isDraftRecipeMode() {
        return fields.is_recipe.checked && !currentEditingMenuItemId();
    }

    function clearIngredientForm() {
        ingredientId.value = '';
        ingredientProductId.value = '';
        ingredientQuantity.value = '';
        ingredientYield.value = '';
        ingredientConsumptionMl.value = '';
        ingredientUnit.value = '';
        selectedIngredientId = null;
        btnIngredientSave.textContent = 'Agregar material';
    }

    function updateIngredientConsumptionFromYield() {
        const yieldValue = normalizeNumber(ingredientYield.value);
        if (yieldValue !== null && yieldValue > 0) {
            const consumption = Math.round((1000 / Number(yieldValue)) * 1000) / 1000;
            ingredientConsumptionMl.value = consumption.toFixed(3);
            ingredientQuantity.value = consumption.toFixed(3);
            if (!ingredientUnit.value.trim()) {
                ingredientUnit.value = 'ml';
            }
            return;
        }

        ingredientConsumptionMl.value = '';
    }

    function computeIngredientCost(item) {
        const unitCost = Number(item.product?.cost || 0);
        const consumptionMl = Number(item.consumption_ml || 0);

        if (Number.isFinite(consumptionMl) && consumptionMl > 0) {
            return (unitCost / 1000) * consumptionMl;
        }

        const quantity = Number(item.quantity || 0);
        return Number.isFinite(quantity) ? quantity * unitCost : 0;
    }

    function clearProfitabilityForm() {
        recipeCostTotal.value = '';
        profitMarginPercent.value = '';
        retailSuggested.value = '';
        profitMeta.textContent = 'Utilidad estimada: -';
    }

    function resetHistoryFilters() {
        historyActionFilter.value = '';
        historyDateFromFilter.value = '';
        historyDateToFilter.value = '';
    }

    async function loadCostHistoryForCurrentItem() {
        const menuItemId = currentEditingMenuItemId();
        if (!menuItemId || !fields.is_recipe.checked) {
            costHistory = [];
            resetHistoryFilters();
            renderCostHistory();
            return;
        }

        try {
            const data = await requestJson(`/api/menu-item-cost-histories?menu_item_id=${menuItemId}&limit=20`);
            costHistory = Array.isArray(data) ? data : [];
            resetHistoryFilters();
            renderCostHistory();
        } catch (_error) {
            costHistory = [];
            resetHistoryFilters();
            renderCostHistory();
            recipeHistoryMeta.textContent = 'No se pudo cargar historial.';
        }
    }

    function calculateRecipeCostTotal() {
        return ingredients.reduce((sum, item) => {
            const subtotal = computeIngredientCost(item);
            return sum + subtotal;
        }, 0);
    }

    function calculateRetailFromMargin(costValue, marginValue) {
        const cost = Number(costValue);
        const margin = Number(marginValue);
        if (!Number.isFinite(cost) || !Number.isFinite(margin) || cost < 0 || margin < 0 || margin >= 100) {
            return null;
        }

        const denominator = 1 - (margin / 100);
        if (denominator <= 0) return null;
        return toMoney(cost / denominator);
    }

    function calculateMarginFromCostAndRetail(costValue, retailValue) {
        const cost = Number(costValue);
        const retail = Number(retailValue);
        if (!Number.isFinite(cost) || !Number.isFinite(retail) || retail <= 0) {
            return null;
        }

        const margin = ((retail - cost) / retail) * 100;
        return toMargin(margin);
    }

    function refreshProfitabilityFromState(syncMarginFromPrice = true) {
        const isRecipeMode = fields.is_recipe.checked;
        const hasRecipeMaterials = ingredients.length > 0;
        const recipeCost = toMoney(calculateRecipeCostTotal());
        const manualCost = toMoney(normalizeNumber(fields.manual_cost.value) || 0);
        const fallbackCost = toMoney(normalizeNumber(fields.cost.value) || manualCost || 0);
        const effectiveCost = isRecipeMode ? (recipeCost ?? fallbackCost) : fallbackCost;

        recipeCostTotal.value = effectiveCost !== null ? String(effectiveCost.toFixed(2)) : '';

        if (isRecipeMode && recipeCost !== null && recipeCost > 0) {
            fields.cost.value = recipeCost.toFixed(2);
        }

        const price = toMoney(normalizeNumber(fields.price.value));
        if (syncMarginFromPrice) {
            const computedMargin = calculateMarginFromCostAndRetail(effectiveCost ?? 0, price ?? 0);
            if (computedMargin !== null) {
                const bounded = Math.max(-999.99, Math.min(99.99, computedMargin));
                profitMarginPercent.value = bounded.toFixed(2);
            }
        }

        const marginInput = toMargin(normalizeNumber(profitMarginPercent.value));
        const suggested = calculateRetailFromMargin(effectiveCost ?? 0, marginInput ?? 0);
        retailSuggested.value = suggested !== null ? suggested.toFixed(2) : '';

        if (isRecipeMode && !hasRecipeMaterials) {
            retailSuggested.value = '';
            profitMeta.textContent = 'Agrega materiales para calcular y aplicar retail.';
            btnApplyRetail.disabled = true;
            return;
        }

        if (suggested !== null && effectiveCost !== null) {
            const utility = toMoney(suggested - effectiveCost);
            profitMeta.textContent = `Utilidad estimada: ${formatMoney(utility)} (${(marginInput ?? 0).toFixed(2)}%)`;
        } else {
            profitMeta.textContent = 'Utilidad estimada: -';
        }

        btnApplyRetail.disabled = !(suggested !== null && suggested >= 0);
    }

    function updateRecipeControlsState() {
        const editorActive = editorFrame.classList.contains('active');
        const recipeEnabled = fields.is_recipe.checked;
        const hasRecipeMaterials = ingredients.length > 0;
        const enabled = canManageCatalog && editorActive && recipeEnabled;

        ingredientProductId.disabled = !enabled;
        ingredientQuantity.disabled = !enabled;
        ingredientUnit.disabled = !enabled;
        btnIngredientSave.disabled = !enabled;
        btnIngredientEdit.disabled = !enabled || !selectedIngredientId;
        btnIngredientCancel.disabled = !enabled;
        btnIngredientDelete.disabled = !enabled || !selectedIngredientId;
        profitMarginPercent.disabled = !canManageCatalog || !editorActive || !recipeEnabled || !hasRecipeMaterials;
        btnApplyRetail.disabled = !canManageCatalog || !editorActive || !recipeEnabled || !hasRecipeMaterials;
    }

    function renderIngredientOptions(selectedValue = '') {
        const selected = selectedValue ? String(selectedValue) : '';
        const options = productsCatalog.map((item) => {
            const value = String(item.id);
            const label = `${item.name}${item.unit ? ` (${item.unit})` : ''}`;
            return `<option value="${escapeHtml(value)}">${escapeHtml(label)}</option>`;
        }).join('');

        ingredientProductId.innerHTML = `<option value="">Selecciona material</option>${options}`;
        ingredientProductId.value = selected;
    }

    function renderIngredientsTable() {
        if (!ingredients.length) {
            recipeTableContainer.innerHTML = '<div class="empty">Sin materiales agregados.</div>';
            refreshProfitabilityFromState(true);
            updateRecipeControlsState();
            return;
        }

        const body = ingredients.map((item) => {
            const selected = Number(selectedIngredientId) === Number(item.id) ? ' class="selected"' : '';
            const unit = item.unit || item.product?.unit || '-';
            const subtotal = computeIngredientCost(item);
            return `
                <tr data-ingredient-id="${item.id}"${selected}>
                    <td>${escapeHtml(asText(item.product?.name))}</td>
                    <td>${formatQuantity(item.quantity)}</td>
                    <td>${formatQuantity(item.cocktail_yield)}</td>
                    <td>${formatQuantity(item.consumption_ml)}</td>
                    <td>${escapeHtml(asText(unit))}</td>
                    <td>${formatMoney(item.product?.cost)}</td>
                    <td>${formatMoney(subtotal)}</td>
                </tr>
            `;
        }).join('');

        recipeTableContainer.innerHTML = `
            <table class="recipe-table">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <th>Rend. cocteles/L</th>
                        <th>Consumo ml/coctel</th>
                        <th>Unidad</th>
                        <th>Costo producto</th>
                        <th>Costo proporcional</th>
                    </tr>
                </thead>
                <tbody>${body}</tbody>
            </table>
        `;

        refreshProfitabilityFromState(true);
        updateRecipeControlsState();
    }

    function toggleRecipePanel() {
        const recipeEnabled = fields.is_recipe.checked;
        const menuItemId = currentEditingMenuItemId();

        if (!recipeEnabled) {
            recipePanel.classList.add('hidden');
            ingredients = [];
            costHistory = [];
            resetHistoryFilters();
            selectedIngredientId = null;
            clearIngredientForm();
            clearProfitabilityForm();
            renderIngredientsTable();
            renderCostHistory();
            setRecipeStatus('Activa "Manejar como receta" para administrar materiales.', null);
            return;
        }

        recipePanel.classList.remove('hidden');
        if (!menuItemId) {
            costHistory = [];
            resetHistoryFilters();
            selectedIngredientId = null;
            clearIngredientForm();
            refreshProfitabilityFromState(true);
            renderIngredientsTable();
            renderCostHistory();
            recipeHint.textContent = 'Modo borrador: agrega materiales ahora y guarda el item para persistir receta + costos.';
            setRecipeStatus('Puedes capturar materiales antes de guardar el item.', null);
            return;
        }

        recipeHint.textContent = 'Administra los materiales que componen este item del menu.';
        refreshProfitabilityFromState(true);
        setRecipeStatus('Carga de materiales lista.', null);
    }

    async function loadProductsCatalog() {
        const cached = getCachedCatalog('products');
        if (cached) {
            productsCatalog = cached;
            renderIngredientOptions(ingredientProductId.value || '');
        }

        try {
            const data = await requestJson('/api/products');
            productsCatalog = Array.isArray(data) ? data : [];
            setCachedCatalog('products', productsCatalog);
        } catch (_error) {
            if (!cached) {
                productsCatalog = [];
            }
        }

        renderIngredientOptions(ingredientProductId.value || '');
    }

    async function loadIngredientsForCurrentItem() {
        const menuItemId = currentEditingMenuItemId();
        if (!fields.is_recipe.checked) {
            ingredients = [];
            costHistory = [];
            resetHistoryFilters();
            selectedIngredientId = null;
            clearIngredientForm();
            renderIngredientsTable();
            renderCostHistory();
            updateRecipeControlsState();
            return;
        }

        if (!menuItemId) {
            costHistory = [];
            resetHistoryFilters();
            selectedIngredientId = null;
            clearIngredientForm();
            renderIngredientsTable();
            renderCostHistory();
            setRecipeStatus('Modo borrador activo: al guardar se registran item y materiales.', null);
            updateRecipeControlsState();
            return;
        }

        try {
            const data = await requestJson(`/api/menu-item-ingredients?menu_item_id=${menuItemId}`);
            ingredients = Array.isArray(data) ? data : [];
            selectedIngredientId = null;
            clearIngredientForm();
            renderIngredientsTable();
            await loadCostHistoryForCurrentItem();
            setRecipeStatus('Materiales cargados correctamente.', 'ok');
        } catch (error) {
            ingredients = [];
            costHistory = [];
            resetHistoryFilters();
            selectedIngredientId = null;
            clearIngredientForm();
            renderIngredientsTable();
            renderCostHistory();
            setRecipeStatus(`No se pudieron cargar materiales: ${String(error.message || error)}`, 'error');
        }

        updateRecipeControlsState();
    }

    function selectIngredientForEdit(item) {
        if (!item) {
            clearIngredientForm();
            updateRecipeControlsState();
            return;
        }

        selectedIngredientId = Number(item.id);
        ingredientId.value = String(item.id);
        renderIngredientOptions(item.product_id);
        ingredientQuantity.value = item.quantity ?? '';
        ingredientYield.value = item.cocktail_yield ?? '';
        ingredientConsumptionMl.value = item.consumption_ml ?? '';
        ingredientUnit.value = item.unit || item.product?.unit || '';
        btnIngredientSave.textContent = 'Guardar material';
        setRecipeStatus(`Material seleccionado: ${item.product?.name || `#${item.id}`}`, null);
        renderIngredientsTable();
    }

    function collectIngredientPayload() {
        const menuItemId = currentEditingMenuItemId();
        return {
            menu_item_id: menuItemId,
            product_id: normalizeNumber(ingredientProductId.value),
            quantity: normalizeNumber(ingredientQuantity.value),
            cocktail_yield: normalizeNumber(ingredientYield.value),
            consumption_ml: normalizeNumber(ingredientConsumptionMl.value),
            unit: ingredientUnit.value.trim() || null,
        };
    }

    function setFormEditable(enabled) {
        Object.values(fields).forEach((field) => {
            if (field.id === 'menuItemId') return;
            field.disabled = !enabled;
        });

        btnSubmit.disabled = !enabled;
        btnCancelEdit.disabled = !enabled;
        btnCloseEditor.disabled = !enabled;
        btnSearchItemImage.disabled = !enabled;
        itemImageFileInput.disabled = !enabled;
        btnItemImageFitCover.disabled = !enabled;
        btnItemImageFitContain.disabled = !enabled;
    }

    function clearForm() {
        fields.id.value = '';
        fields.name.value = '';
        fields.product_type_id.value = '';
        fields.menu_category_id.value = '';
        fields.code.value = '';
        fields.image_url.value = '';
        itemImageFileInput.value = '';
        fields.price.value = '';
        fields.manual_cost.value = '';
        fields.cost.value = '';
        fields.is_recipe.checked = false;
        fields.prep_time_minutes.value = '';
        fields.dish.value = '';
        fields.kitchen.value = '';
        fields.servings.value = '';
        fields.calories.value = '';
        fields.equipment.value = '';
        fields.description.value = '';
        formTitle.textContent = 'Nuevo item de menu';
        btnSubmit.textContent = 'Guardar';
        clearIngredientForm();
        clearProfitabilityForm();
        ingredients = [];
        costHistory = [];
        resetHistoryFilters();
        draftIngredientSeq = 0;
        renderIngredientsTable();
        renderCostHistory();
        setRecipeStatus('Guarda un item como receta para administrar sus materiales.', null);
        updateItemImagePreview('');
        committedItemImageUrl = '';
        toggleRecipePanel();
    }

    function imageUrlFromValue(value) {
        return String(value || '').trim();
    }

    function applyItemImageFitMode(mode, persist = true) {
        itemImageFitMode = mode === 'contain' ? 'contain' : 'cover';
        itemImagePreview.classList.toggle('fit-contain', itemImageFitMode === 'contain');
        const isContain = itemImageFitMode === 'contain';
        btnItemImageFitCover.classList.toggle('active', !isContain);
        btnItemImageFitContain.classList.toggle('active', isContain);
        btnItemImageFitCover.setAttribute('aria-pressed', isContain ? 'false' : 'true');
        btnItemImageFitContain.setAttribute('aria-pressed', isContain ? 'true' : 'false');

        if (persist) {
            localStorage.setItem(itemImageFitStorageKey, itemImageFitMode);
        }
    }

    function restoreItemImageFitMode() {
        const stored = localStorage.getItem(itemImageFitStorageKey) || localStorage.getItem(itemImageFitStorageLegacyKey);
        if (stored === 'cover' || stored === 'contain') {
            localStorage.setItem(itemImageFitStorageKey, stored);
        }
        applyItemImageFitMode(stored === 'contain' ? 'contain' : 'cover', false);
    }

    function appendPreviewBuster(urlValue) {
        const url = imageUrlFromValue(urlValue);
        if (!url) return '';
        const separator = url.includes('?') ? '&' : '?';
        return `${url}${separator}preview_ts=${Date.now()}`;
    }

    function renderItemImagePreviewEmpty(message = 'Haz clic en este marco para seleccionar una imagen') {
        itemImagePreview.innerHTML = `<span class="media-empty"><span class="media-empty-title">Sin imagen</span><span class="media-empty-sub">${escapeHtml(message)}</span></span>`;
    }

    function renderItemImagePreview(urlValue, fallbackUrl = '') {
        const url = imageUrlFromValue(urlValue);
        if (!url) {
            renderItemImagePreviewEmpty();
            return;
        }

        const token = ++menuItemPreviewToken;
        const safeLabel = fields.name.value.trim() || 'Menu item';
        const img = document.createElement('img');
        img.alt = `Imagen de ${safeLabel}`;
        img.decoding = 'async';
        img.loading = 'eager';

        const fallback = imageUrlFromValue(fallbackUrl);
        let usedFallback = false;

        img.addEventListener('error', () => {
            if (token !== menuItemPreviewToken) return;
            if (!usedFallback && fallback && fallback !== url) {
                usedFallback = true;
                img.src = appendPreviewBuster(fallback);
                return;
            }
            renderItemImagePreviewEmpty('No se pudo cargar la imagen. Verifica la URL o vuelve a subirla.');
        });

        img.src = url;
        itemImagePreview.replaceChildren(img);
    }

    function updateItemImagePreview(urlValue) {
        renderItemImagePreview(urlValue, committedItemImageUrl);
    }

    function setItemImagePreviewLoading(isLoading) {
        itemImagePreview.classList.toggle('loading', Boolean(isLoading));
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

    function updateItemImagePreviewFromFile(file) {
        const reader = new FileReader();
        reader.onload = () => {
            const safeLabel = escapeHtml(fields.name.value.trim() || 'Menu item');
            const safeUrl = escapeHtml(String(reader.result || ''));
            if (!safeUrl) return;
            itemImagePreview.innerHTML = `<img src="${safeUrl}" alt="Imagen de ${safeLabel}">`;
        };
        reader.readAsDataURL(file);
    }

    function openItemImageSearch() {
        const queryBase = [fields.name.value, fields.dish.value, fields.kitchen.value].filter(Boolean).join(' ').trim();
        const query = encodeURIComponent(queryBase || 'menu item restaurante');
        window.open(`https://www.bing.com/images/search?q=${query}`, '_blank', 'noopener');
    }

    function syncUploadedItemImageInPanel(url) {
        const editingId = Number(fields.id.value || 0);
        if (!editingId) return;

        const item = items.find((row) => Number(row.id) === editingId);
        if (!item) return;

        item.image_url = url;
        applyFilter();
    }

    async function uploadMenuItemImage(file) {
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

    function collectPayload() {
        const normalizedCode = fields.code.value.trim().toUpperCase();
        return {
            name: fields.name.value.trim(),
            product_type_id: normalizeNumber(fields.product_type_id.value),
            menu_category_id: normalizeNumber(fields.menu_category_id.value),
            code: normalizedCode || null,
            image_url: fields.image_url.value.trim() || null,
            price: normalizeNumber(fields.price.value),
            manual_cost: normalizeNumber(fields.manual_cost.value),
            cost: normalizeNumber(fields.cost.value),
            profit_margin_percent: normalizeNumber(profitMarginPercent.value),
            is_recipe: fields.is_recipe.checked,
            prep_time_minutes: normalizeNumber(fields.prep_time_minutes.value),
            dish: fields.dish.value.trim() || null,
            kitchen: fields.kitchen.value.trim() || null,
            servings: normalizeNumber(fields.servings.value),
            calories: normalizeNumber(fields.calories.value),
            equipment: fields.equipment.value.trim() || null,
            description: fields.description.value.trim() || null,
        };
    }

    function closeEditor() {
        editorOverlay.classList.remove('active');
        editorFrame.classList.remove('active');
        editorOverlay.setAttribute('aria-hidden', 'true');
        editorFrame.setAttribute('aria-hidden', 'true');
        clearForm();
        updateRecipeControlsState();
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
        menuItemForm.addEventListener('keydown', (event) => {
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
            focusNextControl(menuItemForm, target);
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

    function openEditor(mode) {
        if (!canManageCatalog) {
            setStatus(UI_TEXT.noManagePermission, 'error');
            return;
        }

        clearForm();

        if (mode === 'edit') {
            const item = getSelectedItem();
            if (!item) {
                setStatus(UI_TEXT.noEditSelection, 'error');
                return;
            }

            fields.id.value = String(item.id);
            fields.name.value = item.name || '';
            renderProductTypeOptions(item.product_type_id ?? '');
            renderMenuCategoryOptions(item.menu_category_id ?? '');
            fields.code.value = item.code || '';
            fields.image_url.value = item.image_url || '';
            fields.price.value = item.price ?? '';
            fields.manual_cost.value = item.manual_cost ?? '';
            fields.cost.value = item.cost ?? '';
            fields.is_recipe.checked = Boolean(item.is_recipe);
            fields.prep_time_minutes.value = item.prep_time_minutes ?? '';
            fields.dish.value = item.dish ?? '';
            fields.kitchen.value = item.kitchen ?? '';
            fields.servings.value = item.servings ?? '';
            fields.calories.value = item.calories ?? '';
            fields.equipment.value = item.equipment ?? '';
            fields.description.value = item.description || '';
            const itemCost = toMoney(Number(item.cost ?? 0));
            const itemPrice = toMoney(Number(item.price ?? 0));
            const storedMargin = normalizeNumber(item.profit_margin_percent);
            const itemMargin = storedMargin !== null
                ? storedMargin
                : calculateMarginFromCostAndRetail(itemCost ?? 0, itemPrice ?? 0);
            profitMarginPercent.value = itemMargin !== null ? itemMargin.toFixed(2) : '';
            const itemName = String(item.name || '').trim();
            formTitle.textContent = itemName
                ? `Editar item de menu: ${itemName}`
                : `Editar item de menu #${item.id}`;
            btnSubmit.textContent = 'Guardar cambios';
            updateItemImagePreview(fields.image_url.value);
            committedItemImageUrl = imageUrlFromValue(fields.image_url.value);
        } else {
            formTitle.textContent = 'Nuevo item de menu';
            btnSubmit.textContent = 'Guardar';
            refreshProfitabilityFromState(true);
        }

        editorOverlay.classList.add('active');
        editorFrame.classList.add('active');
        editorOverlay.setAttribute('aria-hidden', 'false');
        editorFrame.setAttribute('aria-hidden', 'false');
        scheduleMediaFieldLayoutSync(true);
        toggleRecipePanel();
        loadIngredientsForCurrentItem();
        refreshProfitabilityFromState(true);
        updateRecipeControlsState();
        fields.name.focus();
    }

    fields.code.addEventListener('blur', () => {
        fields.code.value = fields.code.value.trim().toUpperCase();
    });

    fields.image_url.addEventListener('input', () => {
        const currentUrl = imageUrlFromValue(fields.image_url.value);
        updateItemImagePreview(currentUrl);
    });

    fields.name.addEventListener('input', () => {
        updateItemImagePreview(fields.image_url.value);
    });

    btnSearchItemImage.addEventListener('click', () => {
        openItemImageSearch();
    });

    btnItemImageFitCover.addEventListener('click', () => {
        applyItemImageFitMode('cover');
    });

    btnItemImageFitContain.addEventListener('click', () => {
        applyItemImageFitMode('contain');
    });

    itemImagePreview.addEventListener('click', () => {
        if (!canManageCatalog || itemImageFileInput.disabled) return;
        itemImageFileInput.click();
    });

    itemImagePreview.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        if (!canManageCatalog || itemImageFileInput.disabled) return;
        itemImageFileInput.click();
    });

    itemImagePreview.addEventListener('dragenter', (event) => {
        event.preventDefault();
        if (!canManageCatalog || itemImageFileInput.disabled) return;
        itemImagePreview.classList.add('dragover');
    });

    itemImagePreview.addEventListener('dragover', (event) => {
        event.preventDefault();
        if (!canManageCatalog || itemImageFileInput.disabled) return;
        itemImagePreview.classList.add('dragover');
    });

    itemImagePreview.addEventListener('dragleave', (event) => {
        event.preventDefault();
        const related = event.relatedTarget;
        if (related && itemImagePreview.contains(related)) return;
        itemImagePreview.classList.remove('dragover');
    });

    itemImagePreview.addEventListener('drop', (event) => {
        event.preventDefault();
        itemImagePreview.classList.remove('dragover');
        if (!canManageCatalog || itemImageFileInput.disabled) return;

        const file = event.dataTransfer?.files?.[0] || null;
        const validation = validateImageFile(file);
        if (!validation.valid) {
            setStatus(validation.message, 'error');
            showToast(validation.message, 'error');
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        itemImageFileInput.files = dt.files;
        itemImageFileInput.dispatchEvent(new Event('change'));
    });

    itemImageFileInput.addEventListener('change', async () => {
        if (!canManageCatalog) {
            setStatus(UI_TEXT.noUploadPermission, 'error');
            return;
        }

        const file = itemImageFileInput.files && itemImageFileInput.files[0];
        const validation = validateImageFile(file);
        if (!validation.valid) {
            itemImageFileInput.value = '';
            setStatus(validation.message, 'error');
            showToast(validation.message, 'error');
            return;
        }

        updateItemImagePreviewFromFile(file);

        itemImageFileInput.disabled = true;
        setItemImagePreviewLoading(true);
        setStatus(UI_TEXT.uploadingImage, null);

        try {
            const payload = await uploadMenuItemImage(file);
            const url = String(payload?.url || '').trim();
            if (!url) {
                throw new Error('La respuesta de carga no incluyo una URL valida.');
            }

            fields.image_url.value = url;
            committedItemImageUrl = url;
            itemImageFileInput.value = '';
            updateItemImagePreview(appendPreviewBuster(url));
            syncUploadedItemImageInPanel(url);
            setStatus(UI_TEXT.imageUploaded, 'ok');
            showToast('Imagen subida correctamente.', 'ok');
        } catch (error) {
            setStatus(`No fue posible subir la imagen: ${String(error.message || error)}`, 'error');
            showToast('No fue posible subir la imagen.', 'error');
        } finally {
            setItemImagePreviewLoading(false);
            itemImageFileInput.disabled = !canManageCatalog;
        }
    });

    ingredientYield.addEventListener('input', () => {
        updateIngredientConsumptionFromYield();
    });

    fields.is_recipe.addEventListener('change', async () => {
        toggleRecipePanel();
        refreshProfitabilityFromState(true);
        await loadIngredientsForCurrentItem();
    });

    fields.price.addEventListener('input', () => {
        refreshProfitabilityFromState(true);
    });

    fields.cost.addEventListener('input', () => {
        refreshProfitabilityFromState(true);
    });

    fields.manual_cost.addEventListener('input', () => {
        if (!fields.is_recipe.checked) {
            const manual = normalizeNumber(fields.manual_cost.value);
            fields.cost.value = manual !== null ? Number(manual).toFixed(2) : '';
        }
        refreshProfitabilityFromState(true);
    });

    profitMarginPercent.addEventListener('input', () => {
        refreshProfitabilityFromState(false);
    });

    btnApplyRetail.addEventListener('click', () => {
        if (ingredients.length === 0) {
            setRecipeStatus('Primero agrega materiales para aplicar retail.', 'error');
            return;
        }

        const suggested = normalizeNumber(retailSuggested.value);
        if (suggested === null || suggested < 0) {
            setRecipeStatus('No hay retail sugerido para aplicar.', 'error');
            return;
        }

        fields.price.value = Number(suggested).toFixed(2);
        refreshProfitabilityFromState(true);
        setRecipeStatus('Retail aplicado al precio del item.', 'ok');
    });

    function renderTable(rows) {
        if (!rows.length) {
            tableContainer.innerHTML = '<div class="empty">No hay menu items registrados.</div>';
            selectedItemId = null;
            updateActionButtons();
            return;
        }

        const body = rows.map((item) => {
            const selected = Number(selectedItemId) === Number(item.id) ? ' class="selected"' : '';
            const recipe = item.is_recipe ? '<span class="bool-badge yes">Si</span>' : '<span class="bool-badge no">No</span>';
            const imageUrl = imageUrlFromValue(item.image_url);
            const imageCell = imageUrl
                ? `<img class="table-thumb" src="${escapeHtml(imageUrl)}" alt="${escapeHtml(asText(item.name))}">`
                : '<span class="table-thumb-empty">Sin</span>';
            return `
                <tr data-item-id="${item.id}"${selected}>
                    <td>${item.id}</td>
                    <td class="img-col">${imageCell}</td>
                    <td>${escapeHtml(asText(item.code))}</td>
                    <td>${escapeHtml(asText(item.name))}</td>
                    <td>${escapeHtml(asText(item.product_type?.name))}</td>
                    <td>${escapeHtml(asText(item.menu_category?.name || item.category))}</td>
                    <td>${formatMoney(item.price)}</td>
                    <td>${formatMoney(item.manual_cost)}</td>
                    <td>${formatMoney(item.cost)}</td>
                    <td>${renderMarginCell(item)}</td>
                    <td>${recipe}</td>
                    <td>${item.prep_time_minutes ?? '-'}</td>
                    <td>${escapeHtml(asText(item.dish))}</td>
                    <td>${escapeHtml(asText(item.kitchen))}</td>
                    <td>${item.servings ?? '-'}</td>
                    <td>${item.calories ?? '-'}</td>
                    <td>${escapeHtml(asText(item.equipment))}</td>
                    <td class="description-cell">${escapeHtml(asText(item.description))}</td>
                    <td>${item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'}</td>
                </tr>
            `;
        }).join('');

        tableContainer.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="img-col">Img</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Costo Manual</th>
                        <th>Costo Calculado</th>
                        <th>Margen</th>
                        <th>Receta</th>
                        <th>Prep (min)</th>
                        <th>Plato</th>
                        <th>Cocina</th>
                        <th>Raciones</th>
                        <th>Calorias</th>
                        <th>Equipo</th>
                        <th>Descripcion</th>
                        <th>Creacion</th>
                    </tr>
                </thead>
                <tbody>${body}</tbody>
            </table>
        `;

        updateActionButtons();
    }

    function applyFilter() {
        const term = (tableFilter.value || '').trim().toLowerCase();
        const sorted = [...items].sort((a, b) => {
            const direction = currentSortDir === 'desc' ? -1 : 1;

            if (currentSortField === 'id') {
                return (Number(a.id || 0) - Number(b.id || 0)) * direction;
            }

            if (currentSortField === 'code') {
                return String(a.code || '').localeCompare(String(b.code || '')) * direction;
            }

            if (currentSortField === 'description') {
                return String(a.description || '').localeCompare(String(b.description || '')) * direction;
            }

            return 0;
        });

        let filtered = sorted;
        if (deviationFilterMin > 0) {
            filtered = filtered.filter((item) => getDeviationRatio(item) >= deviationFilterMin);
        }

        if (term) {
            filtered = filtered.filter((item) => {
            const line = `${item.id || ''} ${item.code || ''} ${item.name || ''} ${item.image_url || ''} ${item.product_type?.name || ''} ${item.menu_category?.name || item.category || ''} ${item.manual_cost ?? ''} ${item.cost ?? ''} ${item.profit_margin_percent ?? ''} ${item.prep_time_minutes ?? ''} ${item.dish || ''} ${item.kitchen || ''} ${item.servings ?? ''} ${item.calories ?? ''} ${item.equipment || ''} ${item.description || ''}`.toLowerCase();
            return line.includes(term);
            });
        }

        renderTable(filtered);
    }

    async function loadMenuItems() {
        try {
            const data = await requestJson('/api/menu-items');
            const list = Array.isArray(data) ? data : [];
            items = list.sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));

            if (!getSelectedItem()) {
                selectedItemId = null;
            }

            applyFilter();
        } catch (error) {
            tableContainer.innerHTML = '<div class="empty">No fue posible cargar menu items.</div>';
            setStatus(`Error cargando menu items: ${String(error.message || error)}`, 'error');
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

    function renderMenuCategoryOptions(selectedValue) {
        const selected = selectedValue !== null && selectedValue !== undefined ? String(selectedValue) : '';
        const options = menuCategoriesCatalog.map((item) => {
            const value = String(item.id);
            const label = item.code ? `${item.name} (${item.code})` : item.name;
            return `<option value="${escapeHtml(value)}">${escapeHtml(label)}</option>`;
        }).join('');

        fields.menu_category_id.innerHTML = `<option value="">Sin categoria</option>${options}`;
        fields.menu_category_id.value = selected;
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

    async function loadMenuCategoriesCatalog() {
        const cached = getCachedCatalog('menu-categories');
        if (cached) {
            menuCategoriesCatalog = cached;
            renderMenuCategoryOptions(fields.menu_category_id.value || '');
        }

        try {
            const data = await requestJson('/api/menu-categories');
            menuCategoriesCatalog = Array.isArray(data) ? data : [];
            setCachedCatalog('menu-categories', menuCategoriesCatalog);
        } catch (_error) {
            if (!cached) {
                menuCategoriesCatalog = [];
            }
        }

        renderMenuCategoryOptions(fields.menu_category_id.value || '');
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

            updateActionButtons();
        } catch (error) {
            canManageCatalog = false;
            setFormEditable(false);
            updateActionButtons();
            setStatus(`No se pudieron cargar permisos: ${String(error.message || error)}`, 'error');
        }
    }

    async function removeSelectedItem() {
        if (!canManageCatalog) return;

        const item = getSelectedItem();
        if (!item) {
            setStatus(UI_TEXT.noDeleteSelection, 'error');
            return;
        }

        const label = item?.name ? `"${item.name}"` : `#${item.id}`;
        if (!window.confirm(`¿Deseas eliminar el item de menu ${label}? Esta accion no se puede deshacer.`)) return;

        try {
            await requestJson(`/api/menu-items/${item.id}`, { method: 'DELETE' });
            selectedItemId = null;
            setStatus(UI_TEXT.deleted, 'ok');
            await loadMenuItems();
        } catch (error) {
            setStatus(`No se pudo eliminar: ${String(error.message || error)}`, 'error');
        }
    }

    menuItemForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canManageCatalog) {
            setStatus(UI_TEXT.noManagePermission, 'error');
            return;
        }

        const payload = collectPayload();
        const editingId = fields.id.value ? Number(fields.id.value) : null;
        const draftIngredients = (!editingId && payload.is_recipe)
            ? ingredients
                .filter((entry) => Number(entry.id) < 0)
                .map((entry) => ({
                    product_id: normalizeNumber(entry.product_id),
                    quantity: normalizeNumber(entry.quantity),
                    cocktail_yield: normalizeNumber(entry.cocktail_yield),
                    consumption_ml: normalizeNumber(entry.consumption_ml),
                    unit: (entry.unit || '').trim() || null,
                }))
            : [];

        try {
            if (editingId) {
                await requestJson(`/api/menu-items/${editingId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                selectedItemId = editingId;
                setStatus(UI_TEXT.updated, 'ok');
                await loadMenuItems();
                await loadIngredientsForCurrentItem();
            } else {
                const created = await requestJson('/api/menu-items', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });

                const createdId = Number(created?.id || 0);
                if (createdId > 0) {
                    selectedItemId = createdId;
                }

                if (createdId > 0 && draftIngredients.length > 0) {
                    for (const ingredient of draftIngredients) {
                        await requestJson('/api/menu-item-ingredients', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                menu_item_id: createdId,
                                product_id: ingredient.product_id,
                                quantity: ingredient.quantity,
                                cocktail_yield: ingredient.cocktail_yield,
                                consumption_ml: ingredient.consumption_ml,
                                unit: ingredient.unit,
                            }),
                        });
                    }
                }

                if (createdId > 0 && draftIngredients.length > 0) {
                    setStatus(UI_TEXT.createdWithRecipe, 'ok');
                } else {
                    setStatus(UI_TEXT.created, 'ok');
                }

                if (payload.is_recipe && createdId > 0) {
                    await loadMenuItems();
                    openEditor('edit');
                    if (draftIngredients.length > 0) {
                        setStatus(UI_TEXT.createdWithMaterials, 'ok');
                    } else {
                        setStatus(UI_TEXT.createdNeedsRecipe, 'ok');
                    }
                    return;
                }
            }

            closeEditor();
            await loadMenuItems();
        } catch (error) {
            setStatus(`No se pudo guardar: ${String(error.message || error)}`, 'error');
        }
    });

    recipeTableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-ingredient-id]');
        if (!row) return;

        const id = Number(row.dataset.ingredientId);
        const item = ingredients.find((entry) => Number(entry.id) === id) || null;
        selectIngredientForEdit(item);
    });

    btnIngredientCancel.addEventListener('click', () => {
        clearIngredientForm();
        renderIngredientOptions('');
        renderIngredientsTable();
        setRecipeStatus('Edicion de material cancelada.', null);
    });

    btnIngredientEdit.addEventListener('click', () => {
        if (!canManageCatalog) {
            setRecipeStatus('No tienes permisos para administrar recetas.', 'error');
            return;
        }

        const ingredient = ingredients.find((entry) => Number(entry.id) === Number(selectedIngredientId)) || null;
        if (!ingredient) {
            setRecipeStatus('Selecciona un material para editar.', 'error');
            return;
        }

        selectIngredientForEdit(ingredient);
        ingredientProductId.focus();
        setRecipeStatus('Modo edicion habilitado para el material seleccionado.', null);
    });

    btnIngredientSave.addEventListener('click', async () => {
        if (!canManageCatalog) {
            setRecipeStatus('No tienes permisos para administrar recetas.', 'error');
            return;
        }

        if (!fields.is_recipe.checked) {
            setRecipeStatus('Activa "Manejar como receta" para administrar materiales.', 'error');
            return;
        }

        const menuItemId = currentEditingMenuItemId();
        const payload = collectIngredientPayload();

        if (!payload.product_id) {
            setRecipeStatus('Selecciona un material de inventario.', 'error');
            return;
        }

        if (!payload.quantity || payload.quantity <= 0) {
            setRecipeStatus('La cantidad debe ser mayor a 0.', 'error');
            return;
        }

        const editingIngredientId = ingredientId.value ? Number(ingredientId.value) : null;

        if (!menuItemId) {
            const product = productsCatalog.find((entry) => Number(entry.id) === Number(payload.product_id));
            if (!product) {
                setRecipeStatus('El material seleccionado ya no existe en catalogo.', 'error');
                return;
            }

            const duplicate = ingredients.find((entry) => (
                Number(entry.product_id) === Number(payload.product_id)
                && Number(entry.id) !== Number(editingIngredientId)
            ));
            if (duplicate) {
                setRecipeStatus('El material ya esta agregado a esta receta.', 'error');
                return;
            }

            const draftEntry = {
                id: editingIngredientId && editingIngredientId < 0 ? editingIngredientId : -(++draftIngredientSeq),
                menu_item_id: null,
                product_id: Number(payload.product_id),
                quantity: Number(payload.quantity),
                cocktail_yield: payload.cocktail_yield,
                consumption_ml: payload.consumption_ml,
                unit: payload.unit,
                product,
            };

            if (editingIngredientId && editingIngredientId < 0) {
                const index = ingredients.findIndex((entry) => Number(entry.id) === editingIngredientId);
                if (index >= 0) {
                    ingredients[index] = draftEntry;
                }
                setRecipeStatus('Material borrador actualizado.', 'ok');
            } else {
                ingredients.push(draftEntry);
                setRecipeStatus('Material borrador agregado.', 'ok');
            }

            clearIngredientForm();
            renderIngredientsTable();
            return;
        }

        try {
            if (editingIngredientId) {
                await requestJson(`/api/menu-item-ingredients/${editingIngredientId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setRecipeStatus('Material actualizado correctamente.', 'ok');
            } else {
                await requestJson('/api/menu-item-ingredients', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                setRecipeStatus('Material agregado correctamente.', 'ok');
            }

            await loadIngredientsForCurrentItem();
        } catch (error) {
            setRecipeStatus(`No se pudo guardar el material: ${String(error.message || error)}`, 'error');
        }
    });

    btnIngredientDelete.addEventListener('click', async () => {
        if (!canManageCatalog) {
            setRecipeStatus('No tienes permisos para administrar recetas.', 'error');
            return;
        }

        const ingredient = ingredients.find((entry) => Number(entry.id) === Number(selectedIngredientId));
        if (!ingredient) {
            setRecipeStatus('Selecciona un material para eliminar.', 'error');
            return;
        }

        const label = ingredient.product?.name ? `"${ingredient.product.name}"` : `#${ingredient.id}`;
        if (!window.confirm(`¿Deseas eliminar el material ${label}?`)) return;

        if (!currentEditingMenuItemId()) {
            ingredients = ingredients.filter((entry) => Number(entry.id) !== Number(ingredient.id));
            selectedIngredientId = null;
            clearIngredientForm();
            renderIngredientsTable();
            setRecipeStatus('Material borrador eliminado.', 'ok');
            return;
        }

        try {
            await requestJson(`/api/menu-item-ingredients/${ingredient.id}`, { method: 'DELETE' });
            setRecipeStatus('Material eliminado correctamente.', 'ok');
            await loadIngredientsForCurrentItem();
        } catch (error) {
            setRecipeStatus(`No se pudo eliminar el material: ${String(error.message || error)}`, 'error');
        }
    });

    btnRefresh.addEventListener('click', async () => {
        await loadMenuItems();
        if (canManageCatalog) {
            setStatus(UI_TEXT.refreshed, null);
        }
    });

    btnAdd.addEventListener('click', () => {
        openEditor('add');
    });

    btnEdit.addEventListener('click', () => {
        openEditor('edit');
    });

    btnDelete.addEventListener('click', async () => {
        await removeSelectedItem();
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

    tableFilter.addEventListener('input', scheduleApplyFilter);

    historyActionFilter.addEventListener('change', () => {
        renderCostHistory();
    });

    historyDateFromFilter.addEventListener('change', () => {
        renderCostHistory();
    });

    historyDateToFilter.addEventListener('change', () => {
        renderCostHistory();
    });

    btnHistoryClearFilters.addEventListener('click', () => {
        resetHistoryFilters();
        renderCostHistory();
    });

    sortField.addEventListener('change', () => {
        currentSortField = sortField.value;
        saveSortPreferences();
        applyFilter();
    });

    sortDir.addEventListener('change', () => {
        currentSortDir = sortDir.value;
        saveSortPreferences();
        applyFilter();
    });

    tableContainer.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-item-id]');
        if (!row) return;

        selectedItemId = Number(row.dataset.itemId);
        applyFilter();

        const selected = getSelectedItem();
        if (selected) {
            setStatus(`Registro seleccionado: ${selected.name || `#${selected.id}`}`, null);
        }
    });

    async function init() {
        restoreItemImageFitMode();
        bindFastFormKeyboardFlow();
        observeEditorResize();
        window.addEventListener('resize', scheduleMediaFieldLayoutSync);
        scheduleMediaFieldLayoutSync(true);
        clearForm();
        if (deviationFilterMin > 0) {
            tableFilter.placeholder = `Filtro activo: desviacion >= ${Math.round(deviationFilterMin * 100)}%`; 
            setStatus(`Filtro inicial aplicado: desviacion >= ${Math.round(deviationFilterMin * 100)}%.`, null);
        }
        loadSortPreferences();
        sortField.value = currentSortField;
        sortDir.value = currentSortDir;
        await loadCapabilities();
        await loadProductsCatalog();
        await loadProductTypesCatalog();
        await loadMenuCategoriesCatalog();
        await loadMenuItems();
        updateActionButtons();
    }

    init();
</script>
</body>
</html>
