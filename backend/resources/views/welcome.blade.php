<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - Ordena Facil</title>
    <meta name="theme-color" content="#F2911B">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/assets/branding/comanda-deg.png" type="image/png">
    <link rel="apple-touch-icon" href="/assets/branding/comanda-deg.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/ui-frames-pro.css?v={{ $assetVersion }}">
    <style>
        :root,
        :root[data-theme="clasico"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg-1: #2a1409;
            --bg-2: #4b180a;
            --panel: #5b1d0f;
            --panel-soft: #3f140a;
            --text: #fff4ea;
            --muted: #ffd8c0;
            --accent: var(--c2);
            --accent-soft: rgba(242, 145, 27, 0.24);
            --ok: #10b981;
            --border: rgba(255, 255, 255, 0.16);
            --link: #ffe8b7;
            --badge-bg: rgba(16, 185, 129, 0.15);
            --badge-border: rgba(16, 185, 129, 0.35);
            --badge-text: #d1fae5;
            --frame-bg: #2a1308;
            --loading-bg: rgba(26, 8, 3, 0.84);
            --foldable-bg: rgba(255, 255, 255, 0.04);
            --foldable-active-bg: color-mix(in srgb, var(--accent-soft) 70%, #ffd9a5 30%);
            --foldable-text: #fff4ea;
            --foldable-text-muted: #ffe1cb;
            --foldable-active-text: #2b140a;
            --menu-icon-stroke-width: 1.85;
            --menu-icon-tone-opacity: 1;
            --sidebar-scroll-safe: 7px;
        }

        @media (max-width: 1280px) {
            :root {
                --sidebar-scroll-safe: 6px;
            }
        }

        @media (max-width: 980px) {
            :root {
                --sidebar-scroll-safe: 5px;
            }
        }

        @media (max-width: 640px) {
            :root {
                --sidebar-scroll-safe: 4px;
            }
        }

        :root[data-theme="premium"] {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg-1: #140a07;
            --bg-2: #1b0d09;
            --panel: #2a130e;
            --panel-soft: #200f0a;
            --text: #f8ecdb;
            --muted: #d9c8b3;
            --accent: var(--c1);
            --accent-soft: rgba(242, 194, 48, 0.16);
            --ok: #34d399;
            --border: rgba(242, 194, 48, 0.22);
            --link: #f7d984;
            --badge-bg: rgba(242, 194, 48, 0.14);
            --badge-border: rgba(242, 194, 48, 0.35);
            --badge-text: #fef3c7;
            --frame-bg: #160b08;
            --loading-bg: rgba(20, 9, 5, 0.86);
            --foldable-bg: rgba(255, 255, 255, 0.035);
            --foldable-active-bg: color-mix(in srgb, var(--accent-soft) 65%, #f6d370 35%);
            --foldable-text: #f8ecdb;
            --foldable-text-muted: #e9d4bb;
            --foldable-active-text: #26140d;
            --menu-icon-stroke-width: 1.65;
            --menu-icon-tone-opacity: 0.42;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            min-height: 100vh;
            background:
                radial-gradient(900px 500px at -10% -20%, color-mix(in srgb, var(--c3) 44%, transparent), transparent 55%),
                radial-gradient(900px 500px at 110% -20%, color-mix(in srgb, var(--c1) 34%, transparent), transparent 55%),
                linear-gradient(180deg, var(--bg-1), var(--bg-2));
        }

        .shell {
            max-width: 1300px;
            margin: 0 auto;
            padding: 22px;
            display: grid;
            gap: 16px;
        }

        .hero {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(145deg, color-mix(in srgb, var(--panel) 92%, #000 8%), color-mix(in srgb, var(--panel-soft) 92%, #000 8%));
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.28);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.25);
            padding: 6px;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 32px);
            letter-spacing: 0.3px;
            line-height: 1.12;
            text-wrap: balance;
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
            max-width: 70ch;
        }

        .badge {
            border: 1px solid var(--badge-border);
            background: var(--badge-bg);
            color: var(--badge-text);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .hero-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .theme-select {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border-radius: 10px;
            padding: 6px 10px;
            font: inherit;
            font-size: 13px;
        }

        .control-stack {
            display: grid;
            gap: 4px;
        }

        .control-stack label {
            font-size: 12px;
            color: var(--muted);
            letter-spacing: .25px;
        }

        .layout {
            display: grid;
            grid-template-columns: var(--sidebar-current-width, var(--sidebar-width, 340px)) 1fr;
            gap: 16px;
            transition: grid-template-columns .22s ease;
        }


        .panel {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(165deg, var(--panel), var(--panel-soft));
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22);
        }

        .sidebar {
            padding: 14px 9px 14px 14px;
            display: grid;
            gap: 12px;
            align-content: start;
            height: calc(100vh - 120px);
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-gutter: stable;
            scrollbar-width: thin;
            scrollbar-color: color-mix(in srgb, var(--accent) 66%, #ffffff 34%) transparent;
            transition: padding .2s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 10px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
            margin-block: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 84%, #fff 16%), color-mix(in srgb, var(--accent) 58%, #000 42%));
            border-radius: 999px;
            border: 2px solid transparent;
            background-clip: padding-box;
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 40%, transparent 60%);
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 90%, #fff 10%), color-mix(in srgb, var(--accent) 66%, #000 34%));
        }

        .sidebar.collapsed {
            padding: 12px 6px;
            overflow-x: visible;
        }

        .sidebar-user {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px;
            display: grid;
            gap: 6px;
            background: linear-gradient(155deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.02);
        }

        .sidebar-user-head {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid var(--border);
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 13px;
            color: var(--link);
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-user-title {
            margin: 0;
            font-size: 13px;
        }

        .sidebar-user-subtitle {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
        }

        .sidebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .sidebar h2 {
            margin: 2px 0 6px;
            font-size: 17px;
        }

        .menu-toggle-btn {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            border-radius: 10px;
            padding: 5px 10px;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
            min-height: 34px;
            transition: transform .14s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
        }

        #btnToggleMenu {
            min-width: 40px;
            width: 40px;
            padding: 5px;
            font-size: 16px;
            line-height: 1;
            text-align: center;
        }

        .sidebar.collapsed .menu-toggle-btn {
            width: 100%;
            padding: 8px 6px;
            font-size: 11px;
        }

        .sidebar.collapsed #btnToggleMenu {
            width: auto;
            min-width: 34px;
            inline-size: fit-content;
            font-size: 16px;
            padding: 6px 8px;
            margin-left: 0;
        }

        .menu-toggle-btn:hover {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--accent-soft);
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        #btnToggleAdvanced {
            color: var(--foldable-text, var(--text));
            font-weight: 600;
        }

        #btnToggleAdvanced:hover,
        #btnToggleAdvanced:focus-visible {
            background: var(--foldable-active-bg, var(--accent-soft));
            color: var(--foldable-active-text, var(--text));
        }

        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 100%;
            overflow-y: visible;
            overflow-x: hidden;
            padding-right: var(--sidebar-scroll-safe);
            opacity: 1;
            transition: opacity .2s ease;
        }

        .sidebar-content .action-btn,
        .sidebar-content .menu-section-toggle,
        .sidebar-content .helper-btn,
        .sidebar-content .ops-btn {
            width: 100%;
            margin-right: 0;
            max-width: 100%;
        }

        .sidebar.collapsed .sidebar-content {
            opacity: 1;
            pointer-events: auto;
            overflow-x: visible;
        }

        .sidebar.collapsed .sidebar-head h2,
        .sidebar.collapsed .sidebar-user,
        .sidebar.collapsed .help-box,
        .sidebar.collapsed #menuSearch,
        .sidebar.collapsed #btnToggleAdvanced,
        .sidebar.collapsed #advancedTools {
            display: none;
        }

        .sidebar.collapsed #fullMenu {
            display: grid;
            gap: 6px;
        }

        .sidebar.collapsed .menu-section {
            border-top: 0;
            padding-top: 0;
            margin-top: 0;
        }

        .sidebar.collapsed .menu-section-toggle {
            position: relative;
            justify-content: center;
            padding: 10px 8px;
            border-radius: 12px;
            width: 100%;
            margin-right: 0;
        }

        .sidebar.collapsed .menu-section-toggle::after {
            content: attr(data-section-title);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) scale(.98);
            opacity: 0;
            pointer-events: none;
            white-space: nowrap;
            font-size: 12px;
            letter-spacing: .2px;
            color: var(--text);
            background: linear-gradient(145deg, color-mix(in srgb, var(--panel) 88%, #000 12%), color-mix(in srgb, var(--panel-soft) 88%, #000 12%));
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 6px 9px;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.28);
            transition: opacity .15s ease, transform .15s ease;
            z-index: 30;
        }

        .sidebar.collapsed .menu-section-toggle:hover::after,
        .sidebar.collapsed .menu-section-toggle:focus-visible::after {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .sidebar.collapsed .menu-section-toggle .menu-section-label {
            justify-content: center;
            width: 100%;
        }

        .sidebar.collapsed .menu-section-toggle .menu-section-label > span:not(.menu-section-icon) {
            display: none;
        }

        .sidebar.collapsed .menu-chevron,
        .sidebar.collapsed .menu-list {
            display: none;
        }

        .sidebar.collapsed .menu-section-icon {
            width: 22px;
            height: 22px;
            font-size: 12px;
        }

        .menu-search {
            width: 100%;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border-radius: 10px;
            padding: 8px 10px;
            font: inherit;
            font-size: 13px;
        }

        .menu-search::placeholder {
            color: var(--muted);
        }


        .action-btn {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text);
            border-radius: 12px;
            padding: 11px;
            text-align: left;
            cursor: pointer;
            transition: transform .12s ease, border-color .12s ease, background .12s ease;
            font: inherit;
            min-height: 44px;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(224, 160, 32, 0.75);
            background: var(--accent-soft);
        }

        .action-btn strong {
            display: block;
            font-size: 14px;
        }

        .menu-item-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .menu-item-icon {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border);
            color: var(--link);
            flex: 0 0 18px;
        }

        .menu-item-icon svg,
        .menu-section-icon svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
            fill: none;
            stroke-width: var(--menu-icon-stroke-width, 1.8);
            stroke-linecap: round;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }

        .menu-item-icon svg .icon-tone,
        .menu-section-icon svg .icon-tone {
            stroke-opacity: var(--menu-icon-tone-opacity, 1);
        }

        .menu-item-arrow {
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            width: 12px;
            text-align: center;
        }

        .action-btn.active {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--accent-soft);
        }

        .action-btn span {
            color: var(--muted);
            font-size: 12px;
        }

        .quick-links {
            margin-top: 6px;
            border-top: 1px solid var(--border);
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .menu-section {
            margin-top: 4px;
            border-top: 1px dashed var(--border);
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .menu-section-toggle {
            border: 1px solid var(--border);
            background: var(--foldable-bg, rgba(255, 255, 255, 0.03));
            color: var(--foldable-text, var(--text));
            border-radius: 10px;
            padding: 8px 10px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .menu-section-toggle.active {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--foldable-active-bg, var(--accent-soft));
            color: var(--foldable-active-text, var(--text));
        }

        .menu-section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .menu-section-icon {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            font-size: 11px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid var(--border);
            color: var(--link);
        }

        .menu-section-icon.section-principal {
            background: rgba(16, 185, 129, 0.18);
            border-color: rgba(16, 185, 129, 0.45);
            color: #d1fae5;
        }

        .menu-section-icon.section-inventario {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.45);
            color: #dbeafe;
        }

        .menu-section-icon.section-operacion {
            background: rgba(249, 115, 22, 0.2);
            border-color: rgba(249, 115, 22, 0.45);
            color: #ffedd5;
        }

        .menu-section-icon.section-reportes {
            background: rgba(168, 85, 247, 0.2);
            border-color: rgba(168, 85, 247, 0.45);
            color: #f3e8ff;
        }

        .menu-section-toggle:hover {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--foldable-active-bg, var(--accent-soft));
            color: var(--foldable-active-text, var(--text));
        }

        .menu-chevron {
            font-size: 14px;
            color: var(--foldable-text-muted, var(--muted));
            transition: transform .18s ease;
        }

        .menu-section-toggle.active .menu-chevron,
        .menu-section-toggle:hover .menu-chevron {
            color: currentColor;
            opacity: 0.9;
        }

        .menu-section.collapsed .menu-chevron {
            transform: rotate(-90deg);
        }

        .build-info {
            font-size: 11px;
            color: var(--muted);
            text-align: right;
            white-space: nowrap;
        }

        .menu-section h3 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .menu-list {
            display: grid;
            gap: 8px;
            overflow: hidden;
            max-height: 900px;
            opacity: 1;
            transition: max-height .2s ease, opacity .2s ease;
        }

        .menu-section.collapsed .menu-list {
            max-height: 0;
            opacity: 0;
        }

        .ops-box {
            border-top: 1px dashed var(--border);
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .ops-box h3 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .ops-row {
            display: grid;
            gap: 6px;
        }

        .ops-input {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border-radius: 10px;
            padding: 8px 10px;
            font: inherit;
            font-size: 12px;
        }

        .ops-actions {
            display: grid;
            gap: 8px;
        }

        .ops-actions > * {
            min-width: 0;
        }

        .ops-btn {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            border-radius: 10px;
            padding: 6px 10px;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.2;
            transition: transform .14s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
        }

        .ops-btn:hover {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--accent-soft);
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .ops-result {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.02);
        }

        .quick-links a {
            color: var(--link);
            text-decoration: none;
            font-size: 13px;
        }

        .menu-toggle-btn:focus-visible,
        .menu-section-toggle:focus-visible,
        .action-btn:focus-visible,
        .ops-btn:focus-visible,
        .menu-search:focus-visible,
        .theme-select:focus-visible,
        .ops-input:focus-visible {
            outline: 3px solid color-mix(in srgb, var(--accent) 70%, #fff 30%);
            outline-offset: 1px;
        }

        .help-box {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            gap: 10px;
        }

        .help-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .help-body {
            display: grid;
            gap: 10px;
        }

        .help-box.collapsed .help-body {
            display: none;
        }

        .help-box h3 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .help-box ol {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .helper-note {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        .help-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .about-modal {
            position: fixed;
            inset: 0;
            z-index: 5200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .about-modal.active {
            display: flex;
        }

        .about-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.62);
        }

        .about-card {
            position: relative;
            width: min(780px, calc(100vw - 24px));
            max-height: calc(100vh - 24px);
            overflow: auto;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 22px 44px rgba(0, 0, 0, 0.42);
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .about-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
        }

        .about-head h3 {
            margin: 0;
            font-size: 17px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .about-panel {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.03);
            display: grid;
            gap: 8px;
        }

        .about-panel h4 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .about-list {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            font-size: 12px;
            display: grid;
            gap: 4px;
        }

        .about-kv {
            margin: 0;
            display: grid;
            gap: 6px;
            font-size: 12px;
        }

        .about-kv div {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px dashed var(--border);
            padding-bottom: 4px;
        }

        .about-kv dt {
            color: var(--muted);
        }

        .about-kv dd {
            margin: 0;
            color: var(--text);
            text-align: right;
        }

        .about-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            border-top: 1px solid var(--border);
            padding-top: 10px;
        }

        .helper-btn {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            border-radius: 8px;
            padding: 5px 10px;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.2;
            transition: transform .14s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
        }

        .helper-btn:hover {
            border-color: color-mix(in srgb, var(--accent) 78%, #fff 22%);
            background: var(--accent-soft);
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .advanced-tools {
            display: grid;
            gap: 10px;
            overflow: hidden;
            max-height: 600px;
            opacity: 1;
            transition: max-height .2s ease, opacity .2s ease;
        }

        .advanced-tools.collapsed {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
        }

        .tutorial-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.58);
            z-index: 5000;
            display: none;
        }

        .tutorial-overlay.active {
            display: block;
        }

        .tutorial-focus {
            position: fixed;
            border: 2px solid color-mix(in srgb, var(--accent) 78%, #fff 22%);
            border-radius: 12px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.58);
            z-index: 5001;
            pointer-events: none;
            display: none;
            transition: all .2s ease;
        }

        .tutorial-focus.active {
            display: block;
        }

        .tutorial-card {
            position: relative;
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: linear-gradient(160deg, var(--panel), var(--panel-soft));
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.36);
            z-index: 60;
            padding: 12px;
            display: none;
            gap: 10px;
            margin-top: auto;
            overflow: hidden;
        }

        .tutorial-card.is-final {
            border-color: color-mix(in srgb, var(--c1) 58%, #fff 42%);
            box-shadow: 0 18px 36px rgba(242, 194, 48, 0.2), 0 16px 34px rgba(0, 0, 0, 0.36);
        }

        .tutorial-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--c1), var(--c2), var(--c3));
            opacity: .9;
            pointer-events: none;
        }

        .tutorial-card.active {
            display: grid;
        }

        .tutorial-card h4 {
            margin: 0;
            font-size: 15px;
            letter-spacing: .24px;
        }

        .tutorial-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tutorial-head .tutorial-meta {
            margin-right: auto;
        }

        .tutorial-collapse {
            min-width: 86px;
            text-align: center;
            color: var(--foldable-text, var(--text));
            font-weight: 600;
        }

        .tutorial-collapse:hover,
        .tutorial-collapse:focus-visible {
            background: var(--foldable-active-bg, var(--accent-soft));
            color: var(--foldable-active-text, var(--text));
        }

        @media (prefers-contrast: more) {
            .menu-section-toggle,
            #btnToggleAdvanced,
            .tutorial-collapse {
                border-width: 2px;
                letter-spacing: 0.9px;
            }

            .menu-section-toggle,
            #btnToggleAdvanced,
            .tutorial-collapse,
            .menu-chevron {
                color: var(--text);
            }

            .menu-section-toggle.active,
            .menu-section-toggle:hover,
            #btnToggleAdvanced:hover,
            #btnToggleAdvanced:focus-visible,
            .tutorial-collapse:hover,
            .tutorial-collapse:focus-visible {
                background: color-mix(in srgb, var(--accent) 58%, #fff 42%);
                color: #1c110a;
            }
        }

        @media (forced-colors: active) {
            .menu-section-toggle,
            #btnToggleAdvanced,
            .tutorial-collapse {
                forced-color-adjust: auto;
                border-color: ButtonText;
                background: ButtonFace;
                color: ButtonText;
            }

            .menu-section-toggle.active,
            .menu-section-toggle:hover,
            #btnToggleAdvanced:hover,
            #btnToggleAdvanced:focus-visible,
            .tutorial-collapse:hover,
            .tutorial-collapse:focus-visible {
                background: Highlight;
                color: HighlightText;
                border-color: Highlight;
            }

            .menu-chevron {
                color: currentColor;
            }
        }

        .tutorial-body {
            display: grid;
            gap: 6px;
        }

        .tutorial-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .tutorial-meta {
            width: fit-content;
            font-size: 11px;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 3px 8px;
            background: rgba(255, 255, 255, 0.06);
            letter-spacing: .2px;
        }

        .tutorial-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            border-top: 1px solid var(--border);
            padding-top: 8px;
        }

        .tutorial-card.is-final #btnTutorialNext {
            border-color: rgba(242, 194, 48, 0.75);
            background: linear-gradient(120deg, rgba(242, 194, 48, 0.28), rgba(242, 145, 27, 0.2));
            color: #2f180c;
            font-weight: 700;
        }

        .tutorial-card.collapsed {
            gap: 8px;
        }

        .tutorial-card.collapsed .tutorial-body,
        .tutorial-card.collapsed .tutorial-actions {
            display: none;
        }

        .viewer {
            padding: 10px;
            display: grid;
            gap: 12px;
        }

        .viewer-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .viewer-head h3 {
            margin: 0;
            font-size: 15px;
            letter-spacing: .2px;
        }

        .viewer-head a {
            color: #fef3c7;
            text-decoration: none;
            font-size: 13px;
            line-height: 1.4;
        }

        .frame-wrap {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--frame-bg);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.24);
        }

        iframe {
            width: 100%;
            min-height: 420px;
            height: var(--viewer-frame-height, 76vh);
            border: 0;
            background: var(--frame-bg);
        }

        .loading {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background: var(--loading-bg);
            color: var(--link);
            font-size: 14px;
            letter-spacing: 0.2px;
            line-height: 1.5;
            text-align: center;
            padding: 12px;
            transition: opacity .2s ease;
        }

        .loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .action-btn.is-disabled {
            opacity: 0.62;
            cursor: not-allowed;
            border-style: dashed;
        }

        .action-btn.is-disabled:hover {
            transform: none;
            background: rgba(255, 255, 255, 0.03);
            border-color: var(--border);
        }

        @media (max-width: 980px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                height: auto;
                max-height: none;
            }
            iframe {
                min-height: 360px;
                height: var(--viewer-frame-height, 68vh);
            }

            .about-grid {
                grid-template-columns: 1fr;
            }

        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div>
                <div class="brand">
                    <img src="/assets/branding/comanda-deg.png" alt="Logo Ordena Facil">
                    <div>
                        <h1>Ordena Facil - Centro de Operaciones</h1>
                        <p>Portada principal con accesos utiles y dashboard embebido para una operacion continua.</p>
                    </div>
                </div>
            </div>
            <div class="hero-controls">
                <div class="control-stack">
                    <label for="themeSelect">Tema</label>
                    <select id="themeSelect" class="theme-select" aria-label="Selector de tema">
                        <option value="clasico">Clasico</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="control-stack">
                    <label for="roleSelect">Rol operativo</label>
                    <select id="roleSelect" class="theme-select" aria-label="Selector de rol">
                        <option value="guest">guest</option>
                        <option value="mesero">mesero</option>
                        <option value="cocina">cocina</option>
                        <option value="caja">caja</option>
                        <option value="gerente">gerente</option>
                        <option value="admin">admin</option>
                    </select>
                </div>
                <div class="badge" id="roleBadge">Rol activo: guest</div>
                <div class="badge" id="capBadge">Sistema listo</div>
                <div class="badge" id="buildBadge">Build: {{ env('APP_VERSION', 'v1.0.0') }}</div>
            </div>
        </section>

        <section class="layout">
            <aside class="panel sidebar">
                <div class="sidebar-head">
                    <h2>Panel de Inicio</h2>
                    <button id="btnToggleMenu" class="menu-toggle-btn" type="button" aria-expanded="true" aria-label="Contraer menu lateral" title="Contraer menu lateral">◀</button>
                </div>

                <div id="sidebarContent" class="sidebar-content">
                    <section class="sidebar-user" aria-label="Usuario activo">
                        <div class="sidebar-user-head">
                            <div class="sidebar-avatar" id="sidebarAvatar">OF</div>
                            <div>
                                <p class="sidebar-user-title">Ordena Facil</p>
                                <p class="sidebar-user-subtitle" id="sidebarRoleName">Rol: guest</p>
                            </div>
                        </div>
                        <div class="build-info">Version: {{ env('APP_VERSION', 'v1.0.0') }}</div>
                    </section>

                    <input id="menuSearch" class="menu-search" type="search" placeholder="Buscar opcion..." aria-label="Buscar opcion del menu">
                    <button class="helper-btn" id="btnReload" type="button">
                        Recargar vista actual
                    </button>

                    <div id="fullMenu"></div>

                    <button id="btnToggleAdvanced" class="menu-toggle-btn" type="button" aria-expanded="true">Ocultar herramientas avanzadas</button>

                    <div id="advancedTools" class="advanced-tools">
                        <section class="ops-box">
                            <h3>Acciones del Sistema</h3>
                            <div class="ops-row">
                                <input id="apiKeyInput" class="ops-input" type="password" placeholder="API Key dashboard (si aplica)">
                            </div>
                            <div class="ops-actions">
                                <button id="btnQueueDaily" class="ops-btn" type="button">Encolar reporte diario</button>
                                <button id="btnClearDashboardCache" class="ops-btn" type="button">Limpiar cache dashboard</button>
                            </div>
                            <div id="opsResult" class="ops-result" aria-live="polite">Listo para ejecutar acciones del sistema.</div>
                        </section>

                        <div class="quick-links">
                            <a href="/up" target="_blank" rel="noopener noreferrer">Estado de salud /up</a>
                            <a href="/dashboard" target="_blank" rel="noopener noreferrer">Abrir dashboard en nueva pestana</a>
                            <a href="/install">Instalar Ordena Facil en este dispositivo</a>
                            <a href="/docs/GUIA_USO_RAPIDO_ORDENA_FACIL.md" target="_blank" rel="noopener noreferrer">Abrir guia rapida de uso</a>
                        </div>
                    </div>

                    <section id="helpBox" class="help-box" aria-label="Guia de uso rapido">
                        <div class="help-head">
                            <h3>Guia Rapida</h3>
                            <button id="btnHelpCollapse" class="helper-btn tutorial-collapse" type="button" aria-expanded="true">Plegar</button>
                        </div>
                        <div class="help-body">
                            <ol>
                                <li>Selecciona un rol operativo.</li>
                                <li>Elige una opcion del menu izquierdo.</li>
                                <li>Trabaja en la vista de la derecha.</li>
                            </ol>
                            <div class="helper-note">Atajo: pulsa <strong>/</strong> para buscar opciones.</div>
                            <div class="help-actions">
                                <button id="btnStartTutorial" class="helper-btn" type="button">Iniciar tutorial</button>
                                <button id="btnExpandMenu" class="helper-btn" type="button">Mostrar todo el menu</button>
                                <button id="btnRefreshUi" class="helper-btn" type="button">Actualizar interfaz</button>
                                <button id="btnAbout" class="helper-btn" type="button">Acerca de</button>
                            </div>
                        </div>
                    </section>

                    <section id="tutorialCard" class="tutorial-card" aria-live="polite" aria-label="Tutorial guiado">
                        <div class="tutorial-head">
                            <div class="tutorial-meta" id="tutorialMeta">Paso 1 de 1</div>
                            <button id="btnTutorialCollapse" class="helper-btn tutorial-collapse" type="button" aria-expanded="true">Plegar</button>
                        </div>
                        <div class="tutorial-body" id="tutorialBody">
                            <h4 id="tutorialTitle">Tutorial</h4>
                            <p id="tutorialText"></p>
                        </div>
                        <div class="tutorial-actions">
                            <button id="btnTutorialPrev" class="helper-btn" type="button">Anterior</button>
                            <button id="btnTutorialNext" class="helper-btn" type="button">Siguiente</button>
                            <button id="btnTutorialClose" class="helper-btn" type="button">Cerrar</button>
                        </div>
                    </section>
                </div>
            </aside>

            <section class="panel viewer">
                <div class="viewer-head">
                    <h3 id="viewerTitle">Dashboard Operativo</h3>
                    <a id="openTab" href="/dashboard" target="_blank" rel="noopener noreferrer">Abrir vista actual</a>
                </div>
                <div class="frame-wrap">
                    <div id="frameLoading" class="loading">Cargando vista...</div>
                    <iframe id="appFrame" src="about:blank" title="Vista integrada"></iframe>
                </div>
            </section>
        </section>
    </main>

    <div id="tutorialOverlay" class="tutorial-overlay" aria-hidden="true"></div>
    <div id="tutorialFocus" class="tutorial-focus" aria-hidden="true"></div>

    <section id="aboutModal" class="about-modal" aria-hidden="true" aria-label="Acerca de Ordena Facil">
        <div id="aboutBackdrop" class="about-backdrop"></div>
        <article class="about-card" role="dialog" aria-modal="true" aria-labelledby="aboutTitle">
            <div class="about-head">
                <h3 id="aboutTitle">Acerca de Ordena Facil</h3>
                <button id="btnAboutCloseTop" class="helper-btn" type="button">Cerrar</button>
            </div>

            <div class="about-grid">
                <section class="about-panel">
                    <h4>Version instalada</h4>
                    <dl class="about-kv" id="aboutVersionInfo"></dl>
                </section>

                <section class="about-panel">
                    <h4>Estado actual</h4>
                    <dl class="about-kv" id="aboutRuntimeInfo"></dl>
                </section>

                <section class="about-panel">
                    <h4>Herramientas del sistema</h4>
                    <ul class="about-list">
                        <li>Backend: Laravel + PHP + MySQL.</li>
                        <li>Frontend: Blade + JavaScript (menu lateral y visor embebido).</li>
                        <li>PWA: manifest + service worker para instalacion en dispositivos.</li>
                        <li>Seguridad: cabeceras, API key y roles/capacidades por perfil.</li>
                        <li>Operacion: reportes (diario/semanal/mensual/anual), exportacion y colas.</li>
                    </ul>
                </section>

                <section class="about-panel">
                    <h4>Modulos principales</h4>
                    <ul class="about-list">
                        <li>Dashboard operativo y salud del sistema.</li>
                        <li>Inventario: productos, menu items, mesas.</li>
                        <li>Operacion: ordenes, clientes, comisiones, gastos.</li>
                        <li>Reportes y exportacion de datos.</li>
                        <li>Instalacion multiplataforma y actualizacion guiada.</li>
                    </ul>
                </section>
            </div>

            <div class="about-actions">
                <button id="btnAboutRefresh" class="helper-btn" type="button">Actualizar datos</button>
                <button id="btnAboutClose" class="helper-btn" type="button">Cerrar</button>
            </div>
        </article>
    </section>

    <div
        id="systemMeta"
        hidden
        data-app-name="{{ config('app.name', 'Ordena Facil') }}"
        data-app-version="{{ env('APP_VERSION', 'v1.0.0') }}"
        data-laravel-version="{{ app()->version() }}"
        data-php-version="{{ PHP_VERSION }}"
        data-app-env="{{ app()->environment() }}"
        data-generated-at="{{ now()->format('Y-m-d H:i:s') }}"
    ></div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js?v={{ $assetVersion }}', { updateViaCache: 'none' }).catch(() => {});
            });
        }

        const frame = document.getElementById('appFrame');
        const title = document.getElementById('viewerTitle');
        const openTab = document.getElementById('openTab');
        const loading = document.getElementById('frameLoading');
        const themeSelect = document.getElementById('themeSelect');
        const roleSelect = document.getElementById('roleSelect');
        const roleBadge = document.getElementById('roleBadge');
        const capBadge = document.getElementById('capBadge');
        const buildBadge = document.getElementById('buildBadge');
        const sidebarRoleName = document.getElementById('sidebarRoleName');
        const fullMenu = document.getElementById('fullMenu');
        const btnStartTutorial = document.getElementById('btnStartTutorial');
        const btnExpandMenu = document.getElementById('btnExpandMenu');
        const btnRefreshUi = document.getElementById('btnRefreshUi');
        const btnAbout = document.getElementById('btnAbout');
        const btnToggleMenu = document.getElementById('btnToggleMenu');
        const btnToggleAdvanced = document.getElementById('btnToggleAdvanced');
        const sidebar = document.querySelector('.sidebar');
        const advancedTools = document.getElementById('advancedTools');
        const menuSearch = document.getElementById('menuSearch');
        const apiKeyInput = document.getElementById('apiKeyInput');
        const opsResult = document.getElementById('opsResult');
        const tutorialOverlay = document.getElementById('tutorialOverlay');
        const tutorialFocus = document.getElementById('tutorialFocus');
        const helpBox = document.getElementById('helpBox');
        const btnHelpCollapse = document.getElementById('btnHelpCollapse');
        const tutorialCard = document.getElementById('tutorialCard');
        const tutorialMeta = document.getElementById('tutorialMeta');
        const tutorialTitle = document.getElementById('tutorialTitle');
        const tutorialText = document.getElementById('tutorialText');
        const btnTutorialCollapse = document.getElementById('btnTutorialCollapse');
        const btnTutorialPrev = document.getElementById('btnTutorialPrev');
        const btnTutorialNext = document.getElementById('btnTutorialNext');
        const btnTutorialClose = document.getElementById('btnTutorialClose');
        const aboutModal = document.getElementById('aboutModal');
        const aboutBackdrop = document.getElementById('aboutBackdrop');
        const btnAboutCloseTop = document.getElementById('btnAboutCloseTop');
        const btnAboutClose = document.getElementById('btnAboutClose');
        const btnAboutRefresh = document.getElementById('btnAboutRefresh');
        const aboutVersionInfo = document.getElementById('aboutVersionInfo');
        const aboutRuntimeInfo = document.getElementById('aboutRuntimeInfo');
        const systemMeta = document.getElementById('systemMeta');
        const sidebarContent = document.getElementById('sidebarContent');
        let currentPath = '/dashboard';
        let capabilities = [];
        let sectionCollapseState = {};
        let frameLoadTimeout = null;
        let tutorialActive = false;
        let tutorialStep = 0;
        let aboutOpen = false;

        const systemBuildInfo = {
            appName: systemMeta?.dataset.appName || 'Ordena Facil',
            appVersion: systemMeta?.dataset.appVersion || 'v1.0.0',
            laravelVersion: systemMeta?.dataset.laravelVersion || 'n/d',
            phpVersion: systemMeta?.dataset.phpVersion || 'n/d',
            appEnv: systemMeta?.dataset.appEnv || 'n/d',
            generatedAt: systemMeta?.dataset.generatedAt || 'n/d',
        };

        const tutorialSteps = [
            {
                title: 'Selecciona un rol',
                text: 'Empieza por elegir el rol operativo. Esto muestra solo opciones permitidas para ese perfil.',
                selector: '#roleSelect',
            },
            {
                title: 'Busca rapidamente',
                text: 'Usa este campo para encontrar opciones por nombre. Tambien puedes pulsar / para enfocar la busqueda.',
                selector: '#menuSearch',
            },
            {
                title: 'Abre una opcion del menu',
                text: 'Haz clic en cualquier opcion para cargar su vista en el panel derecho.',
                selector: '[data-menu-src="/dashboard"]',
            },
            {
                title: 'Trabaja en esta vista',
                text: 'Aqui se mostraran reportes, dashboards y listados para operar el sistema.',
                selector: '#appFrame',
            },
            {
                title: 'Herramientas avanzadas',
                text: 'Cuando tengas mas experiencia, abre aqui funciones tecnicas como cola y cache.',
                selector: '#advancedTools',
            },
        ];

        function getRole() {
            const stored = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
            const allowed = ['guest', 'user', 'mesero', 'cocina', 'caja', 'gerente', 'admin'];
            return allowed.includes(stored) ? stored : 'guest';
        }

        function setRole(role) {
            localStorage.setItem('ordena-facil-role', role);
            localStorage.removeItem('barandrest-role');
            roleBadge.textContent = `Rol activo: ${role}`;
            sidebarRoleName.textContent = `Rol: ${role}`;
        }

        function hasCapability(capability) {
            if (!capability) return true;
            return capabilities.includes(capability);
        }

        function getMenuCollapsed() {
            return localStorage.getItem('ordena-facil-menu-collapsed') === '1';
        }

        function setMenuCollapsed(collapsed) {
            localStorage.setItem('ordena-facil-menu-collapsed', collapsed ? '1' : '0');
            sidebar.classList.toggle('collapsed', collapsed);
            document.documentElement.style.setProperty('--sidebar-current-width', collapsed ? '86px' : '340px');
            const actionLabel = collapsed ? 'Expandir menu lateral' : 'Contraer menu lateral';
            btnToggleMenu.textContent = collapsed ? '▶' : '◀';
            btnToggleMenu.title = actionLabel;
            btnToggleMenu.setAttribute('aria-label', actionLabel);
            btnToggleMenu.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            requestAnimationFrame(syncSidebarScrollSafe);
        }

        function getSectionStates() {
            try {
                const raw = localStorage.getItem('ordena-facil-menu-sections');
                const parsed = raw ? JSON.parse(raw) : {};
                return (parsed && typeof parsed === 'object') ? parsed : {};
            } catch (_error) {
                return {};
            }
        }

        function getAdvancedToolsVisible() {
            return localStorage.getItem('ordena-facil-advanced-tools') === '1';
        }

        function getTutorialCollapsed() {
            return localStorage.getItem('ordena-facil-tutorial-collapsed') === '1';
        }

        function getHelpCollapsed() {
            return localStorage.getItem('ordena-facil-help-collapsed') === '1';
        }

        function setTutorialCollapsed(collapsed, options = {}) {
            const persist = options.persist !== false;

            tutorialCard.classList.toggle('collapsed', collapsed);
            btnTutorialCollapse.textContent = collapsed ? 'Expandir' : 'Plegar';
            btnTutorialCollapse.title = collapsed ? 'Expandir guia tutorial' : 'Plegar guia tutorial';
            btnTutorialCollapse.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

            if (persist) {
                localStorage.setItem('ordena-facil-tutorial-collapsed', collapsed ? '1' : '0');
            }

            if (tutorialActive && collapsed) {
                tutorialFocus.classList.remove('active');
            }
        }

        function setHelpCollapsed(collapsed, options = {}) {
            const persist = options.persist !== false;

            helpBox.classList.toggle('collapsed', collapsed);
            btnHelpCollapse.textContent = collapsed ? 'Expandir' : 'Plegar';
            btnHelpCollapse.title = collapsed ? 'Expandir guia rapida' : 'Plegar guia rapida';
            btnHelpCollapse.setAttribute('aria-expanded', collapsed ? 'false' : 'true');

            if (persist) {
                localStorage.setItem('ordena-facil-help-collapsed', collapsed ? '1' : '0');
            }
        }

        function setAdvancedToolsVisible(visible) {
            localStorage.setItem('ordena-facil-advanced-tools', visible ? '1' : '0');
            advancedTools.classList.toggle('collapsed', !visible);
            btnToggleAdvanced.textContent = visible ? 'Ocultar herramientas avanzadas' : 'Mostrar herramientas avanzadas';
            btnToggleAdvanced.setAttribute('aria-expanded', visible ? 'true' : 'false');
        }

        function saveSectionStates() {
            localStorage.setItem('ordena-facil-menu-sections', JSON.stringify(sectionCollapseState));
        }

        function slugify(input) {
            return String(input || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function bindSectionToggles() {
            document.querySelectorAll('[data-section-key]').forEach((section) => {
                const key = section.dataset.sectionKey;
                const toggle = section.querySelector('.menu-section-toggle');
                if (!toggle) return;

                toggle.addEventListener('click', () => {
                    if (sidebar.classList.contains('collapsed')) {
                        setMenuCollapsed(false);
                    }

                    const collapsed = !section.classList.contains('collapsed');

                    // Exclusive accordion: only one section expanded at a time.
                    if (!collapsed) {
                        document.querySelectorAll('[data-section-key]').forEach((other) => {
                            if (other === section) return;
                            other.classList.add('collapsed');
                            const otherToggle = other.querySelector('.menu-section-toggle');
                            if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                            sectionCollapseState[other.dataset.sectionKey] = true;
                        });
                    }

                    section.classList.toggle('collapsed', collapsed);
                    toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    sectionCollapseState[key] = collapsed;
                    saveSectionStates();
                    highlightActiveSection();
                });
            });
        }

        function expandAllSections() {
            document.querySelectorAll('[data-section-key]').forEach((section) => {
                section.classList.remove('collapsed');
                sectionCollapseState[section.dataset.sectionKey] = false;
                const toggle = section.querySelector('.menu-section-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            });

            saveSectionStates();
            menuSearch.value = '';
            filterMenu('');
        }

        function highlightActiveSection() {
            document.querySelectorAll('[data-section-key]').forEach((section) => {
                const hasActive = section.querySelector('[data-menu-src].active') !== null;
                const toggle = section.querySelector('.menu-section-toggle');
                if (toggle) toggle.classList.toggle('active', hasActive);
            });
        }

        function placeTutorialFocus(selector) {
            const element = document.querySelector(selector);
            if (!element) {
                tutorialFocus.classList.remove('active');
                return;
            }

            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const rect = element.getBoundingClientRect();
            const pad = 6;
            tutorialFocus.style.left = `${Math.max(0, rect.left - pad)}px`;
            tutorialFocus.style.top = `${Math.max(0, rect.top - pad)}px`;
            tutorialFocus.style.width = `${Math.max(20, rect.width + (pad * 2))}px`;
            tutorialFocus.style.height = `${Math.max(20, rect.height + (pad * 2))}px`;
            tutorialFocus.classList.add('active');
        }

        function renderTutorialStep() {
            const step = tutorialSteps[tutorialStep];
            if (!step) return;

            if (typeof step.before === 'function') {
                step.before();
            }

            tutorialMeta.textContent = `Paso ${tutorialStep + 1} de ${tutorialSteps.length}`;
            tutorialTitle.textContent = step.title;
            tutorialText.textContent = step.text;

            btnTutorialPrev.disabled = tutorialStep === 0;
            btnTutorialNext.textContent = tutorialStep === tutorialSteps.length - 1 ? 'Finalizar' : 'Siguiente';
            tutorialCard.classList.toggle('is-final', tutorialStep === tutorialSteps.length - 1);

            if (tutorialCard.classList.contains('collapsed')) {
                tutorialFocus.classList.remove('active');
                return;
            }

            // Delay helps after animated menu expansions.
            setTimeout(() => placeTutorialFocus(step.selector), 120);
        }

        function stopTutorial() {
            tutorialActive = false;
            tutorialOverlay.classList.remove('active');
            tutorialFocus.classList.remove('active');
            tutorialCard.classList.remove('active');
            tutorialCard.classList.remove('is-final');
        }

        async function refreshInterfaceNow() {
            try {
                if ('caches' in window) {
                    const keys = await caches.keys();
                    await Promise.all(keys.map((key) => caches.delete(key)));
                }

                if ('serviceWorker' in navigator) {
                    const regs = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(regs.map((reg) => reg.unregister()));
                }

                setOpsResult('Interfaz actualizada. Recargando...', false);
            } catch (_error) {
                setOpsResult('No se pudo limpiar completamente el cache. Recargando...', true);
            }

            window.setTimeout(() => {
                window.location.reload();
            }, 250);
        }

        function startTutorial() {
            setMenuCollapsed(false);
            setTutorialCollapsed(false, { persist: false });
            tutorialActive = true;
            tutorialStep = 0;
            tutorialOverlay.classList.add('active');
            tutorialCard.classList.add('active');
            renderTutorialStep();
        }

        function nextTutorialStep() {
            if (tutorialStep >= tutorialSteps.length - 1) {
                stopTutorial();
                return;
            }
            tutorialStep += 1;
            renderTutorialStep();
        }

        function prevTutorialStep() {
            if (tutorialStep <= 0) return;
            tutorialStep -= 1;
            renderTutorialStep();
        }

        function getTheme() {
            const selected = themeSelect.value || localStorage.getItem('ordena-facil-theme') || localStorage.getItem('barandrest-theme') || 'clasico';
            return selected === 'premium' ? 'premium' : 'clasico';
        }

        function syncLayoutHeights() {
            const viewerFrameHeight = Math.max(360, window.innerHeight - 220);

            document.documentElement.style.setProperty('--viewer-frame-height', `${viewerFrameHeight}px`);
            syncSidebarScrollSafe();
        }

        function syncSidebarScrollSafe() {
            if (!sidebar) return;

            const viewportBase = window.innerWidth <= 640
                ? 4
                : window.innerWidth <= 980
                    ? 5
                    : window.innerWidth <= 1280
                        ? 6
                        : 7;

            const gutter = Math.max(0, sidebar.offsetWidth - sidebar.clientWidth);
            const safe = Math.max(viewportBase, gutter + 2);

            document.documentElement.style.setProperty('--sidebar-scroll-safe', `${safe}px`);
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('ordena-facil-theme', theme);
            localStorage.removeItem('barandrest-theme');
        }

        function withTheme(src) {
            const url = new URL(src, window.location.origin);
            url.searchParams.set('theme', getTheme());
            return url.pathname + url.search;
        }

        function mapToViewer(src, text) {
            if (!src.startsWith('/api/')) return src;
            const url = new URL('/viewer/api', window.location.origin);
            url.searchParams.set('endpoint', src);
            url.searchParams.set('title', text || 'Vista de datos');
            return url.pathname + url.search;
        }

        function loadView(src, text) {
            loading.classList.remove('hidden');
            loading.textContent = 'Cargando vista...';
            title.textContent = text;
            const target = mapToViewer(src, text);
            const themed = withTheme(target);
            openTab.href = themed;
            frame.src = themed;
            currentPath = src;
            highlightActiveMenu(src);

            if (frameLoadTimeout) clearTimeout(frameLoadTimeout);
            frameLoadTimeout = setTimeout(() => {
                loading.textContent = 'La vista esta tardando o el frame esta bloqueado. Usa "Abrir vista actual" o "Actualizar interfaz".';
            }, 8000);
        }

        function highlightActiveMenu(src) {
            document.querySelectorAll('[data-menu-src]').forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.menuSrc === src);
            });
            highlightActiveSection();
        }

        function getMenuIconSvg(iconKey, type = 'item') {
            const icons = {
                home: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5.5 9.5V21h13V9.5"></path></svg>',
                heart: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 7.6a5 5 0 0 0-7.1 0L12 9.3l-1.7-1.7a5 5 0 1 0-7.1 7.1l1.7 1.7L12 23l7.1-6.6 1.7-1.7a5 5 0 0 0 0-7.1z"></path></svg>',
                grid: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8"></rect><rect x="13" y="3" width="8" height="8"></rect><rect x="3" y="13" width="8" height="8"></rect><rect x="13" y="13" width="8" height="8"></rect></svg>',
                ruler: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18"></path><path d="M6 7v4"></path><path d="M10 7v3"></path><path d="M14 7v4"></path><path d="M18 7v3"></path><path d="M3 17h18"></path></svg>',
                tag: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10 12 2H4v8l8 8 8-8z"></path><circle cx="7.5" cy="6.5" r="1"></circle></svg>',
                list: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6h12"></path><path d="M9 12h12"></path><path d="M9 18h12"></path><circle cx="4" cy="6" r="1"></circle><circle cx="4" cy="12" r="1"></circle><circle cx="4" cy="18" r="1"></circle></svg>',
                box: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5z"></path><path d="M3 7.5V16.5L12 21l9-4.5V7.5"></path><path d="M12 12v9"></path></svg>',
                table: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 10h18"></path><path d="M9 4v16"></path><path d="M15 4v16"></path></svg>',
                cog: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.2"></circle><path d="M19.4 15a7.8 7.8 0 0 0 .1-2l2-1.2-2-3.5-2.3.6a7.6 7.6 0 0 0-1.7-1l-.4-2.3h-4l-.4 2.3a7.6 7.6 0 0 0-1.7 1l-2.3-.6-2 3.5 2 1.2a7.8 7.8 0 0 0 .1 2l-2 1.2 2 3.5 2.3-.6a7.6 7.6 0 0 0 1.7 1l.4 2.3h4l.4-2.3a7.6 7.6 0 0 0 1.7-1l2.3.6 2-3.5-2-1.2z"></path></svg>',
                refresh: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.6-6.4"></path><path d="M21 3v6h-6"></path></svg>',
                user: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>',
                percent: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 5 5 19"></path><circle cx="7" cy="7" r="2.5"></circle><circle cx="17" cy="17" r="2.5"></circle></svg>',
                wallet: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M15 12h6"></path><circle cx="16" cy="12" r=".8"></circle></svg>',
                reports: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h16"></path><path d="M7 16V10"></path><path d="M12 16V6"></path><path d="M17 16v-3"></path></svg>',
                day: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M8 3v4"></path><path d="M16 3v4"></path><path d="M3 10h18"></path><path d="M8 14h4"></path></svg>',
                week: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M3 10h18"></path><path d="M9 10v11"></path><path d="M15 10v11"></path></svg>',
                month: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M8 3v4"></path><path d="M16 3v4"></path><path d="M3 10h18"></path><path d="M7 14h10"></path><path d="M7 17h7"></path></svg>',
                year: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M3 10h18"></path><path d="M8 14h8"></path><path d="M8 17h8"></path><path d="M8 3v4"></path><path d="M16 3v4"></path></svg>',
                sales: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h16"></path><path d="M6 16 10 12l3 3 5-6"></path><path d="M18 9h-4"></path></svg>',
                excel: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M8 8 12 16"></path><path d="M12 8 8 16"></path><path d="M14 8h4"></path></svg>',
                pdf: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5"></path><path d="M9 15h6"></path></svg>',
            };

            const fallback = type === 'section' ? icons.grid : icons.list;
            const base = icons[iconKey] || fallback;
            if (getTheme() !== 'premium') return base;

            return base.replace(/<(path|rect|circle|line|polyline|polygon|ellipse)\b/, '<$1 class="icon-tone"');
        }

        function renderMenu() {
            sectionCollapseState = getSectionStates();

            const sections = [
                {
                    title: 'Principal',
                    icon: 'home',
                    color: 'principal',
                    items: [
                        { label: 'Dashboard', icon: 'grid', hint: 'Vista general del negocio', src: '/dashboard', capability: 'view_dashboard' },
                        { label: 'Salud del sistema', icon: 'heart', hint: 'Endpoint /up', src: '/up' }
                    ]
                },
                {
                    title: 'Inventario y Catalogo',
                    icon: 'box',
                    color: 'inventario',
                    items: [
                        { label: 'Medidas', icon: 'ruler', hint: 'Gestion de catalogo de medidas', src: '/catalog/measures', capability: 'manage_catalog' },
                        { label: 'Tipos de Producto', icon: 'tag', hint: 'Clasificacion para inventario y menu', src: '/catalog/product-types', capability: 'manage_catalog' },
                        { label: 'Categorias de Menu', icon: 'list', hint: 'Clasifica cocteles, entradas, postres y mas', src: '/catalog/menu-categories', capability: 'manage_catalog' },
                        { label: 'Productos', icon: 'box', hint: 'Gestion de catalogo de productos', src: '/catalog/products', capability: 'manage_catalog' },
                        { label: 'Menu Items', icon: 'list', hint: 'Listado del menu', src: '/catalog/menu-items', capability: 'manage_catalog' },
                        { label: 'Mesas', icon: 'table', hint: 'Listado de mesas', src: '/api/tables', capability: 'manage_tables' }
                    ]
                },
                {
                    title: 'Operacion',
                    icon: 'cog',
                    color: 'operacion',
                    items: [
                        { label: 'Ordenes', icon: 'refresh', hint: 'Flujo de ordenes', src: '/api/orders', capability: 'manage_orders' },
                        { label: 'Clientes', icon: 'user', hint: 'Base de clientes', src: '/api/customers', capability: 'manage_customers' },
                        { label: 'Comisiones', icon: 'percent', hint: 'Comisiones registradas', src: '/api/commissions', capability: 'manage_commissions' },
                        { label: 'Gastos', icon: 'wallet', hint: 'Control de gastos', src: '/api/expenses', capability: 'manage_expenses' }
                    ]
                },
                {
                    title: 'Reportes',
                    icon: 'reports',
                    color: 'reportes',
                    items: [
                        { label: 'Reporte Diario', icon: 'day', hint: 'Resumen diario', src: '/api/reports/daily', capability: 'manage_reports' },
                        { label: 'Reporte Semanal', icon: 'week', hint: 'Resumen semanal', src: '/api/reports/weekly', capability: 'manage_reports' },
                        { label: 'Reporte Mensual', icon: 'month', hint: 'Resumen mensual', src: '/api/reports/monthly', capability: 'manage_reports' },
                        { label: 'Reporte Anual', icon: 'year', hint: 'Resumen anual', src: '/api/reports/yearly', capability: 'manage_reports' },
                        { label: 'Reporte de Ventas', icon: 'sales', hint: 'Analitica de ventas', src: '/api/reports/sales', capability: 'manage_reports' },
                        { label: 'Exportar Excel', icon: 'excel', hint: 'Generar exportacion', src: '/api/reports/export/excel', capability: 'manage_reports' },
                        { label: 'Exportar PDF', icon: 'pdf', hint: 'Generar exportacion', src: '/api/reports/export/pdf', capability: 'manage_reports' }
                    ]
                }
            ];

            fullMenu.innerHTML = sections.map((section) => {
                const key = slugify(section.title);
                const collapsed = sectionCollapseState[key] === true;
                const buttons = section.items.map((item) => {
                    const available = hasCapability(item.capability);
                    const disabled = available ? '' : ' disabled';
                    const className = available ? 'action-btn' : 'action-btn is-disabled';
                    const hint = available ? item.hint : `${item.hint} - sin permiso para este rol`;

                    return `<button class="${className}" data-menu-src="${item.src}" data-available="${available ? '1' : '0'}" ${disabled}><strong><span class="menu-item-row"><span class="menu-item-icon">${getMenuIconSvg(item.icon, 'item')}</span><span>${item.label}</span></span></strong><span>${hint}</span></button>`;
                }).join('');
                return `
                    <section class="menu-section ${collapsed ? 'collapsed' : ''}" data-section-key="${key}">
                        <button class="menu-section-toggle" type="button" aria-expanded="${collapsed ? 'false' : 'true'}" data-section-title="${section.title}" aria-label="${section.title}" title="${section.title}">
                            <span class="menu-section-label"><span class="menu-section-icon section-${section.color || 'default'}">${getMenuIconSvg(section.icon, 'section')}</span><span>${section.title}</span></span>
                            <span class="menu-chevron">▾</span>
                        </button>
                        <div class="menu-list">${buttons}</div>
                    </section>
                `;
            }).join('');

            document.querySelectorAll('[data-menu-src]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    if (btn.dataset.available !== '1') {
                        setOpsResult('Tu rol actual no tiene permiso para esta opcion. Cambia el rol para habilitarla.', true);
                        return;
                    }
                    loadView(btn.dataset.menuSrc, btn.querySelector('strong').textContent || 'Vista');
                });
            });

            bindSectionToggles();

            const anyExpanded = document.querySelector('[data-section-key]:not(.collapsed)') !== null;
            if (!anyExpanded) {
                const firstSection = document.querySelector('[data-section-key]');
                if (firstSection) {
                    firstSection.classList.remove('collapsed');
                    const firstToggle = firstSection.querySelector('.menu-section-toggle');
                    if (firstToggle) firstToggle.setAttribute('aria-expanded', 'true');
                    sectionCollapseState[firstSection.dataset.sectionKey] = false;
                    saveSectionStates();
                }
            }

            highlightActiveMenu(currentPath);
        }

        function filterMenu(term) {
            const query = (term || '').trim().toLowerCase();
            document.querySelectorAll('.menu-section').forEach((section) => {
                let visibleCount = 0;
                section.querySelectorAll('[data-menu-src]').forEach((btn) => {
                    const text = (btn.textContent || '').toLowerCase();
                    const visible = !query || text.includes(query);
                    btn.style.display = visible ? '' : 'none';
                    if (visible) visibleCount += 1;
                });
                section.style.display = visibleCount > 0 ? '' : 'none';

                if (visibleCount > 0 && query) {
                    section.classList.remove('collapsed');
                    const toggle = section.querySelector('.menu-section-toggle');
                    if (toggle) toggle.setAttribute('aria-expanded', 'true');
                }
            });
        }

        function setOpsResult(message, isError) {
            opsResult.textContent = message;
            opsResult.style.color = isError ? '#fecaca' : 'var(--muted)';
            opsResult.style.borderColor = isError ? 'rgba(191, 19, 4, 0.55)' : 'var(--border)';
            opsResult.style.background = isError ? 'rgba(191, 19, 4, 0.16)' : 'rgba(255, 255, 255, 0.02)';
        }

        function rowHtml(label, value) {
            return `<div><dt>${label}</dt><dd>${value}</dd></div>`;
        }

        async function resolveServiceWorkerStatus() {
            if (!('serviceWorker' in navigator)) return 'No disponible';

            try {
                const regs = await navigator.serviceWorker.getRegistrations();
                return regs.length > 0 ? `Activo (${regs.length})` : 'Disponible sin registrar';
            } catch (_error) {
                return 'No se pudo verificar';
            }
        }

        async function fillAboutData() {
            const swStatus = await resolveServiceWorkerStatus();
            const cacheApi = ('caches' in window) ? 'Disponible' : 'No disponible';
            const pwaInstall = ('BeforeInstallPromptEvent' in window) ? 'Compatible' : 'Depende del navegador';
            const currentRole = getRole();
            const capCount = Array.isArray(capabilities) ? capabilities.length : 0;
            const currentTheme = getTheme();
            const nowStamp = new Date().toLocaleString();

            aboutVersionInfo.innerHTML = [
                rowHtml('Sistema', systemBuildInfo.appName),
                rowHtml('Version', systemBuildInfo.appVersion),
                rowHtml('Framework', `Laravel ${systemBuildInfo.laravelVersion}`),
                rowHtml('PHP', systemBuildInfo.phpVersion),
                rowHtml('Ambiente', systemBuildInfo.appEnv),
                rowHtml('Compilado', systemBuildInfo.generatedAt),
            ].join('');

            aboutRuntimeInfo.innerHTML = [
                rowHtml('URL', window.location.origin),
                rowHtml('Rol activo', currentRole),
                rowHtml('Permisos cargados', String(capCount)),
                rowHtml('Tema', currentTheme),
                rowHtml('Service Worker', swStatus),
                rowHtml('Cache del navegador', cacheApi),
                rowHtml('Instalacion PWA', pwaInstall),
                rowHtml('Actualizado', nowStamp),
            ].join('');
        }

        async function openAboutModal() {
            aboutOpen = true;
            aboutModal.classList.add('active');
            aboutModal.setAttribute('aria-hidden', 'false');
            await fillAboutData();
        }

        function closeAboutModal() {
            aboutOpen = false;
            aboutModal.classList.remove('active');
            aboutModal.setAttribute('aria-hidden', 'true');
        }

        async function runAction(url, method, requiresApiKey) {
            try {
                const headers = { 'Accept': 'application/json' };
                headers['X-USER-ROLE'] = getRole();
                if (requiresApiKey) {
                    const key = apiKeyInput.value.trim();
                    if (!key) {
                        setOpsResult('Debes indicar la API Key para esta accion.', true);
                        return;
                    }
                    headers['X-API-KEY'] = key;
                }

                setOpsResult('Ejecutando accion...', false);
                const res = await fetch(url, { method, headers });
                if (!res.ok) {
                    let detail = `HTTP ${res.status}`;
                    try {
                        const payload = await res.json();
                        if (payload && payload.message) detail = `${detail} - ${payload.message}`;
                    } catch (_ignored) {}
                    setOpsResult(`Accion fallo: ${detail}`, true);
                    return;
                }

                setOpsResult('Accion ejecutada correctamente.', false);
            } catch (error) {
                setOpsResult(`Error: ${String(error.message || error)}`, true);
            }
        }

        async function loadCapabilities() {
            const role = getRole();
            try {
                const res = await fetch('/api/system/capabilities', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-USER-ROLE': role,
                    },
                });

                if (!res.ok) {
                    // Fallback keeps core navigation available if capabilities endpoint is temporarily unavailable.
                    capabilities = ['view_dashboard'];
                    capBadge.textContent = `Permisos no disponibles (${res.status})`;
                } else {
                    const payload = await res.json();
                    capabilities = Array.isArray(payload.capabilities) ? payload.capabilities : ['view_dashboard'];
                    const roleName = String(payload.role || role);
                    const capCount = capabilities.length;
                    setRole(roleName);
                    roleSelect.value = roleName;
                    capBadge.textContent = `Permisos activos: ${capCount}`;
                }
            } catch (_error) {
                capabilities = ['view_dashboard'];
                capBadge.textContent = 'Permisos no disponibles (offline)';
            }

            renderMenu();
            filterMenu(menuSearch.value || '');
            requestAnimationFrame(syncSidebarScrollSafe);

            const canQueue = hasCapability('manage_reports');
            const canClear = hasCapability('manage_system');
            document.getElementById('btnQueueDaily').disabled = !canQueue;
            document.getElementById('btnClearDashboardCache').disabled = !canClear;
        }

        frame.addEventListener('load', () => {
            if (frameLoadTimeout) clearTimeout(frameLoadTimeout);
            loading.classList.add('hidden');
        });

        document.getElementById('btnReload').addEventListener('click', () => {
            loading.classList.remove('hidden');
            frame.contentWindow.location.reload();
        });

        menuSearch.addEventListener('input', () => {
            filterMenu(menuSearch.value);
        });

        document.getElementById('btnQueueDaily').addEventListener('click', () => {
            runAction('/api/reports/daily/queue', 'POST', false);
        });

        document.getElementById('btnClearDashboardCache').addEventListener('click', () => {
            runAction('/api/dashboard/clear-cache', 'POST', true);
        });

        themeSelect.addEventListener('change', () => {
            applyTheme(getTheme());
            const current = openTab.getAttribute('href') || '/dashboard';
            const path = current.split('?')[0] || '/dashboard';
            loadView(path, title.textContent || 'Dashboard Operativo');
        });

        roleSelect.addEventListener('change', async () => {
            setRole(roleSelect.value);
            await loadCapabilities();
        });

        btnToggleMenu.addEventListener('click', () => {
            setMenuCollapsed(!sidebar.classList.contains('collapsed'));
        });

        btnToggleAdvanced.addEventListener('click', () => {
            setAdvancedToolsVisible(!advancedTools.classList.contains('collapsed'));
        });

        btnStartTutorial.addEventListener('click', () => {
            startTutorial();
        });

        btnExpandMenu.addEventListener('click', () => {
            expandAllSections();
        });

        btnRefreshUi.addEventListener('click', async () => {
            await refreshInterfaceNow();
        });

        btnAbout.addEventListener('click', async () => {
            await openAboutModal();
        });

        btnTutorialNext.addEventListener('click', () => {
            nextTutorialStep();
        });

        btnTutorialCollapse.addEventListener('click', () => {
            const collapsed = !tutorialCard.classList.contains('collapsed');
            setTutorialCollapsed(collapsed);

            if (tutorialActive && !collapsed) {
                renderTutorialStep();
            }
        });

        btnHelpCollapse.addEventListener('click', () => {
            const collapsed = !helpBox.classList.contains('collapsed');
            setHelpCollapsed(collapsed);
        });

        btnTutorialPrev.addEventListener('click', () => {
            prevTutorialStep();
        });

        btnTutorialClose.addEventListener('click', () => {
            stopTutorial();
        });

        tutorialOverlay.addEventListener('click', () => {
            stopTutorial();
        });

        aboutBackdrop.addEventListener('click', () => {
            closeAboutModal();
        });

        btnAboutCloseTop.addEventListener('click', () => {
            closeAboutModal();
        });

        btnAboutClose.addEventListener('click', () => {
            closeAboutModal();
        });

        btnAboutRefresh.addEventListener('click', async () => {
            await fillAboutData();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === '/' && document.activeElement !== menuSearch) {
                event.preventDefault();
                menuSearch.focus();
                menuSearch.select();
            }

            if (tutorialActive && event.key === 'Escape') {
                event.preventDefault();
                stopTutorial();
            }

            if (aboutOpen && event.key === 'Escape') {
                event.preventDefault();
                closeAboutModal();
            }
        });

        window.addEventListener('resize', () => {
            syncLayoutHeights();
        });

        const initialTheme = ((localStorage.getItem('ordena-facil-theme') || localStorage.getItem('barandrest-theme')) === 'premium') ? 'premium' : 'clasico';
        const initialRole = getRole();
        themeSelect.value = initialTheme;
        roleSelect.value = initialRole;
        buildBadge.textContent = `Build: {{ env('APP_VERSION', 'v1.0.0') }}`;
        applyTheme(initialTheme);
        syncLayoutHeights();
        setRole(initialRole);
        loadView('/dashboard', 'Dashboard Operativo');
        setMenuCollapsed(getMenuCollapsed());
        setAdvancedToolsVisible(getAdvancedToolsVisible());
        setTutorialCollapsed(getTutorialCollapsed());
        setHelpCollapsed(getHelpCollapsed());
        loadCapabilities();
    </script>
</body>
</html>
