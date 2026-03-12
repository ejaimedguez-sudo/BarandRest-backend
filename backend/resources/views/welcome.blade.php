<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - BarandRest</title>
    <style>
        :root {
            --bg: #10141f;
            --panel: #172033;
            --panel-2: #1d2a42;
            --text: #f6f7fb;
            --muted: #b4bfd6;
            --border: rgba(255, 255, 255, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", "Trebuchet MS", "Verdana", sans-serif;
            background:
                radial-gradient(1200px 500px at -10% -30%, rgba(249, 115, 22, 0.25), transparent 60%),
                radial-gradient(900px 500px at 110% -20%, rgba(245, 158, 11, 0.25), transparent 55%),
                linear-gradient(180deg, #0b1020 0%, var(--bg) 55%, #0b1224 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            gap: 18px;
        }

        .hero {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            background: linear-gradient(145deg, rgba(23, 32, 51, 0.95), rgba(13, 19, 32, 0.95));
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.3);
        }

        .hero h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 0.3px;
        }

        .hero p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 15px;
        }

        .layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 18px;
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: linear-gradient(160deg, var(--panel), var(--panel-2));
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
        }

        .menu {
            padding: 16px;
        }

        .menu h2 {
            margin: 0 0 14px;
            font-size: 18px;
        }

        .menu-grid {
            display: grid;
            gap: 10px;
        }

        .menu-btn {
            width: 100%;
            text-align: left;
            color: var(--text);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: transform .12s ease, border-color .12s ease, background .12s ease;
        }

        .menu-btn strong {
            display: block;
            font-size: 14px;
        }

        .menu-btn span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .menu-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(245, 158, 11, 0.7);
            background: rgba(245, 158, 11, 0.08);
        }

        .menu-btn.active {
            border-color: rgba(245, 158, 11, 0.9);
            background: rgba(245, 158, 11, 0.14);
        }

        .status {
            margin-top: 14px;
            padding: 10px;
            border-radius: 10px;
            font-size: 12px;
            border: 1px solid rgba(16, 185, 129, 0.35);
            background: rgba(16, 185, 129, 0.12);
            color: #d1fae5;
        }

        .viewer {
            padding: 12px;
            display: grid;
            gap: 10px;
        }

        .viewer-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 6px 8px;
        }

        .viewer-head h3 {
            margin: 0;
            font-size: 16px;
        }

        .viewer-head a {
            color: #fde68a;
            text-decoration: none;
            font-size: 13px;
        }

        iframe {
            width: 100%;
            min-height: 72vh;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #0e1628;
        }

        @media (max-width: 980px) {
            .layout { grid-template-columns: 1fr; }
            iframe { min-height: 62vh; }
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="hero">
            <h1>Centro de Control BarandRest</h1>
            <p>Elige una vista de trabajo y manten el Dashboard integrado dentro de esta pantalla de inicio.</p>
        </section>

        <section class="layout">
            <aside class="panel menu">
                <h2>Accesos rapidos</h2>
                <div class="menu-grid" id="menuGrid">
                    <button class="menu-btn active" data-src="/dashboard" data-title="Dashboard Operativo">
                        <strong>Dashboard</strong>
                        <span>Indicadores y resumen de ventas</span>
                    </button>
                    <button class="menu-btn" data-src="/api/products" data-title="API Productos">
                        <strong>Productos (API)</strong>
                        <span>Vista JSON para validar inventario</span>
                    </button>
                    <button class="menu-btn" data-src="/api/reports/monthly" data-title="Reporte Mensual (API)">
                        <strong>Reporte mensual</strong>
                        <span>Datos agregados por mes</span>
                    </button>
                    <button class="menu-btn" data-src="/api/reports/yearly" data-title="Reporte Anual (API)">
                        <strong>Reporte anual</strong>
                        <span>Datos agregados por ano</span>
                    </button>
                </div>
                <div class="status">Estado: sistema listo. Puedes cambiar de vista sin salir de la portada.</div>
            </aside>

            <section class="panel viewer">
                <div class="viewer-head">
                    <h3 id="viewerTitle">Dashboard Operativo</h3>
                    <a id="openTab" href="/dashboard" target="_blank" rel="noopener noreferrer">Abrir en nueva pestana</a>
                </div>
                <iframe id="appFrame" src="/dashboard" title="BarandRest Dashboard"></iframe>
            </section>
        </section>
    </div>

    <script>
        const buttons = Array.from(document.querySelectorAll('.menu-btn'));
        const frame = document.getElementById('appFrame');
        const title = document.getElementById('viewerTitle');
        const openTab = document.getElementById('openTab');

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                buttons.forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
                const src = btn.dataset.src;
                const text = btn.dataset.title;
                frame.src = src;
                title.textContent = text;
                openTab.href = src;
            });
        });
    </script>
</body>
</html>
