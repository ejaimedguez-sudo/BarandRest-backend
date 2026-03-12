<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - BarandRest</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
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
            letter-spacing: 0.2px;
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
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
            max-height: 78vh;
            overflow: auto;
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
            border-color: rgba(224, 160, 32, 0.75);
            background: var(--accent-soft);
        }

        .action-btn strong {
            display: block;
            font-size: 14px;
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
        }

        .quick-links a {
            color: var(--link);
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
            background: var(--frame-bg);
        }

        iframe {
            width: 100%;
            min-height: 76vh;
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
                <div class="brand">
                    <img src="/assets/branding/comanda-deg.png" alt="Logo BarandRest">
                    <div>
                        <h1>BarandRest - Centro de Operaciones</h1>
                        <p>Portada principal con accesos utiles y dashboard embebido para una operacion continua.</p>
                    </div>
                </div>
            </div>
            <div class="hero-controls">
                <label for="themeSelect" style="font-size:12px;color:var(--muted)">Tema</label>
                <select id="themeSelect" class="theme-select" aria-label="Selector de tema">
                    <option value="clasico">Clasico</option>
                    <option value="premium">Premium</option>
                </select>
                <div class="badge">Sistema listo</div>
            </div>
        </section>

        <section class="layout">
            <aside class="panel sidebar">
                <h2>Panel de Inicio</h2>
                <button class="action-btn" id="btnReload">
                    <strong>Recargar Dashboard</strong>
                    <span>Actualiza la vista embebida</span>
                </button>

                <div id="fullMenu"></div>

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
        const themeSelect = document.getElementById('themeSelect');
        const fullMenu = document.getElementById('fullMenu');
        let currentPath = '/dashboard';

        function getTheme() {
            const selected = themeSelect.value || localStorage.getItem('barandrest-theme') || 'clasico';
            return selected === 'premium' ? 'premium' : 'clasico';
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('barandrest-theme', theme);
        }

        function withTheme(src) {
            const url = new URL(src, window.location.origin);
            url.searchParams.set('theme', getTheme());
            return url.pathname + url.search;
        }

        function loadView(src, text) {
            loading.classList.remove('hidden');
            title.textContent = text;
            const themed = withTheme(src);
            openTab.href = themed;
            frame.src = themed;
            currentPath = src;
            highlightActiveMenu(src);
        }

        function highlightActiveMenu(src) {
            document.querySelectorAll('[data-menu-src]').forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.menuSrc === src);
            });
        }

        function renderMenu() {
            const sections = [
                {
                    title: 'Principal',
                    items: [
                        { label: 'Dashboard', hint: 'Vista general del negocio', src: '/dashboard' },
                        { label: 'Salud del sistema', hint: 'Endpoint /up', src: '/up' }
                    ]
                },
                {
                    title: 'Inventario y Catalogo',
                    items: [
                        { label: 'Productos', hint: 'Listado de productos', src: '/api/products' },
                        { label: 'Menu Items', hint: 'Listado del menu', src: '/api/menu-items' },
                        { label: 'Mesas', hint: 'Listado de mesas', src: '/api/tables' }
                    ]
                },
                {
                    title: 'Operacion',
                    items: [
                        { label: 'Ordenes', hint: 'Flujo de ordenes', src: '/api/orders' },
                        { label: 'Clientes', hint: 'Base de clientes', src: '/api/customers' },
                        { label: 'Comisiones', hint: 'Comisiones registradas', src: '/api/commissions' },
                        { label: 'Gastos', hint: 'Control de gastos', src: '/api/expenses' }
                    ]
                },
                {
                    title: 'Reportes',
                    items: [
                        { label: 'Reporte Diario', hint: 'Resumen diario', src: '/api/reports/daily' },
                        { label: 'Reporte Semanal', hint: 'Resumen semanal', src: '/api/reports/weekly' },
                        { label: 'Reporte Mensual', hint: 'Resumen mensual', src: '/api/reports/monthly' },
                        { label: 'Reporte Anual', hint: 'Resumen anual', src: '/api/reports/yearly' },
                        { label: 'Reporte de Ventas', hint: 'Analitica de ventas', src: '/api/reports/sales' },
                        { label: 'Exportar Excel', hint: 'Generar exportacion', src: '/api/reports/export/excel' },
                        { label: 'Exportar PDF', hint: 'Generar exportacion', src: '/api/reports/export/pdf' }
                    ]
                }
            ];

            fullMenu.innerHTML = sections.map((section) => {
                const buttons = section.items.map((item) => {
                    return `<button class="action-btn" data-menu-src="${item.src}"><strong>${item.label}</strong><span>${item.hint}</span></button>`;
                }).join('');
                return `<section class="menu-section"><h3>${section.title}</h3><div class="menu-list">${buttons}</div></section>`;
            }).join('');

            document.querySelectorAll('[data-menu-src]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    loadView(btn.dataset.menuSrc, btn.querySelector('strong').textContent || 'Vista');
                });
            });

            highlightActiveMenu(currentPath);
        }

        frame.addEventListener('load', () => {
            loading.classList.add('hidden');
        });

        document.getElementById('btnReload').addEventListener('click', () => {
            loading.classList.remove('hidden');
            frame.contentWindow.location.reload();
        });

        themeSelect.addEventListener('change', () => {
            applyTheme(getTheme());
            const current = openTab.getAttribute('href') || '/dashboard';
            const path = current.split('?')[0] || '/dashboard';
            loadView(path, title.textContent || 'Dashboard Operativo');
        });

        const initialTheme = (localStorage.getItem('barandrest-theme') === 'premium') ? 'premium' : 'clasico';
        themeSelect.value = initialTheme;
        applyTheme(initialTheme);
        loadView('/dashboard', 'Dashboard Operativo');

        renderMenu();
    </script>
</body>
</html>
