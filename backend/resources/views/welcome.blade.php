<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - BarandRest</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #111827;
            --panel: #1f2937;
            --panel-soft: #111c31;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #f59e0b;
            --accent-soft: rgba(245, 158, 11, 0.2);
            --ok: #10b981;
            --border: rgba(255, 255, 255, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            min-height: 100vh;
            background:
                radial-gradient(900px 500px at -10% -20%, rgba(249, 115, 22, 0.2), transparent 55%),
                radial-gradient(900px 500px at 110% -20%, rgba(16, 185, 129, 0.15), transparent 55%),
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
            background: linear-gradient(145deg, rgba(31, 41, 55, 0.95), rgba(17, 24, 39, 0.95));
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.28);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 32px);
            letter-spacing: 0.2px;
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .badge {
            border: 1px solid rgba(16, 185, 129, 0.35);
            background: rgba(16, 185, 129, 0.15);
            color: #d1fae5;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 16px;
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(165deg, var(--panel), var(--panel-soft));
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22);
        }

        .sidebar {
            padding: 14px;
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .sidebar h2 {
            margin: 2px 0 6px;
            font-size: 17px;
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
        }

        .action-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(245, 158, 11, 0.65);
            background: rgba(245, 158, 11, 0.08);
        }

        .action-btn strong {
            display: block;
            font-size: 14px;
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

        .quick-links a {
            color: #fde68a;
            text-decoration: none;
            font-size: 13px;
        }

        .viewer {
            padding: 10px;
            display: grid;
            gap: 10px;
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
        }

        .viewer-head h3 {
            margin: 0;
            font-size: 15px;
        }

        .viewer-head a {
            color: #fef3c7;
            text-decoration: none;
            font-size: 13px;
        }

        .frame-wrap {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: #0b1220;
        }

        iframe {
            width: 100%;
            min-height: 76vh;
            border: 0;
            background: #0b1220;
        }

        .loading {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background: rgba(11, 18, 32, 0.88);
            color: #fef3c7;
            font-size: 14px;
            letter-spacing: 0.2px;
            transition: opacity .2s ease;
        }

        .loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        @media (max-width: 980px) {
            .layout { grid-template-columns: 1fr; }
            iframe { min-height: 68vh; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div>
                <h1>BarandRest - Centro de Operaciones</h1>
                <p>Portada principal con accesos utiles y dashboard embebido para una operacion continua.</p>
            </div>
            <div class="badge">Sistema listo</div>
        </section>

        <section class="layout">
            <aside class="panel sidebar">
                <h2>Panel de Inicio</h2>
                <button class="action-btn" id="btnReload">
                    <strong>Recargar Dashboard</strong>
                    <span>Actualiza la vista embebida</span>
                </button>
                <button class="action-btn" id="btnDashboard">
                    <strong>Ir a Dashboard</strong>
                    <span>Vuelve al tablero principal</span>
                </button>
                <button class="action-btn" id="btnProducts">
                    <strong>Ver Productos (API)</strong>
                    <span>Revision rapida de inventario JSON</span>
                </button>
                <button class="action-btn" id="btnMonthly">
                    <strong>Reporte Mensual (API)</strong>
                    <span>Resumen consolidado por mes</span>
                </button>
                <button class="action-btn" id="btnYearly">
                    <strong>Reporte Anual (API)</strong>
                    <span>Resumen consolidado por ano</span>
                </button>

                <div class="quick-links">
                    <a href="/up" target="_blank" rel="noopener noreferrer">Estado de salud /up</a>
                    <a href="/dashboard" target="_blank" rel="noopener noreferrer">Abrir dashboard en nueva pestana</a>
                </div>
            </aside>

            <section class="panel viewer">
                <div class="viewer-head">
                    <h3 id="viewerTitle">Dashboard Operativo</h3>
                    <a id="openTab" href="/dashboard" target="_blank" rel="noopener noreferrer">Abrir vista actual</a>
                </div>
                <div class="frame-wrap">
                    <div id="frameLoading" class="loading">Cargando vista...</div>
                    <iframe id="appFrame" src="/dashboard" title="Vista integrada"></iframe>
                </div>
            </section>
        </section>
    </main>

    <script>
        const frame = document.getElementById('appFrame');
        const title = document.getElementById('viewerTitle');
        const openTab = document.getElementById('openTab');
        const loading = document.getElementById('frameLoading');

        function loadView(src, text) {
            loading.classList.remove('hidden');
            title.textContent = text;
            openTab.href = src;
            frame.src = src;
        }

        frame.addEventListener('load', () => {
            loading.classList.add('hidden');
        });

        document.getElementById('btnReload').addEventListener('click', () => {
            loading.classList.remove('hidden');
            frame.contentWindow.location.reload();
        });

        document.getElementById('btnDashboard').addEventListener('click', () => loadView('/dashboard', 'Dashboard Operativo'));
        document.getElementById('btnProducts').addEventListener('click', () => loadView('/api/products', 'Productos (API)'));
        document.getElementById('btnMonthly').addEventListener('click', () => loadView('/api/reports/monthly', 'Reporte Mensual (API)'));
        document.getElementById('btnYearly').addEventListener('click', () => loadView('/api/reports/yearly', 'Reporte Anual (API)'));
    </script>
</body>
</html>
