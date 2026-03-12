<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vista de Datos - Ordena Facil</title>
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
            --text: #3a1a0e;
            --muted: #8f5f45;
            --border: #f3d9c8;
            --head: #ffedd5;
        }

        :root[data-theme="premium"] {
            --bg: #1b0f0b;
            --panel: #26140f;
            --text: #f8e9d6;
            --muted: #d8bca4;
            --border: rgba(242, 194, 48, 0.22);
            --head: rgba(242, 194, 48, 0.14);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            background: var(--bg);
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px;
            display: grid;
            gap: 12px;
        }

        .head {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--panel);
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .head h1 {
            margin: 0;
            font-size: 18px;
        }

        .head small {
            color: var(--muted);
            word-break: break-all;
        }

        .btn {
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text);
            border-radius: 8px;
            padding: 8px 10px;
            text-decoration: none;
            font-size: 13px;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--panel);
            padding: 12px;
        }

        .helper {
            margin: 0 0 10px;
            color: var(--muted);
            font-size: 12px;
        }

        .status {
            color: var(--muted);
            font-size: 13px;
        }

        .status.error {
            color: #b91c1c;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .tools {
            margin: 10px 0;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
            align-items: center;
        }

        .filter-input {
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text);
            border-radius: 8px;
            padding: 8px 10px;
            font: inherit;
            font-size: 13px;
            min-height: 40px;
        }

        .filter-input:focus-visible {
            outline: 3px solid color-mix(in srgb, var(--c2) 70%, #fff 30%);
            outline-offset: 1px;
        }

        .count {
            color: var(--muted);
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            background: var(--head);
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
        }

        tbody td {
            padding: 8px;
            border-top: 1px solid var(--border);
            vertical-align: top;
        }

        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 12px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="head">
            <div>
                <h1 id="title">Vista de datos</h1>
                <small id="endpointLabel"></small>
            </div>
            <a id="openRaw" class="btn" target="_blank" rel="noopener noreferrer">Abrir JSON</a>
        </section>

        <section class="card">
            <p class="helper">Tip: usa el filtro para encontrar datos rapidamente sin conocimientos tecnicos.</p>
            <div id="status" class="status" aria-live="polite">Cargando...</div>
            <div class="tools">
                <input id="tableFilter" class="filter-input" type="search" placeholder="Filtrar por texto en la tabla..." aria-label="Filtrar filas de la tabla">
                <span id="countLabel" class="count">0 filas</span>
            </div>
            <div id="result"></div>
        </section>
    </main>

    <script>
        function applyTheme() {
            const params = new URLSearchParams(window.location.search);
            const fromUrl = params.get('theme');
            const fromStorage = localStorage.getItem('ordena-facil-theme') || localStorage.getItem('barandrest-theme');
            const theme = (fromUrl === 'premium' || fromStorage === 'premium') ? 'premium' : 'clasico';
            document.documentElement.setAttribute('data-theme', theme);
        }

        function toText(value) {
            if (value === null || value === undefined) return '';
            if (typeof value === 'object') return JSON.stringify(value);
            return String(value);
        }

        function renderTable(rows) {
            if (!Array.isArray(rows) || rows.length === 0) {
                return '<pre>[]</pre>';
            }

            const cols = Array.from(rows.reduce((set, row) => {
                Object.keys(row || {}).forEach((k) => set.add(k));
                return set;
            }, new Set()));

            const head = cols.map((c) => `<th>${c}</th>`).join('');
            const body = rows.map((row) => {
                const tds = cols.map((c) => `<td>${toText(row ? row[c] : '')}</td>`).join('');
                return `<tr>${tds}</tr>`;
            }).join('');

            return `<div class="table-wrap"><table><thead><tr>${head}</tr></thead><tbody>${body}</tbody></table></div>`;
        }

        function applyFilter() {
            const input = document.getElementById('tableFilter');
            const count = document.getElementById('countLabel');
            const query = (input.value || '').trim().toLowerCase();
            const rows = Array.from(document.querySelectorAll('tbody tr'));
            let visible = 0;

            rows.forEach((row) => {
                const text = (row.textContent || '').toLowerCase();
                const show = !query || text.includes(query);
                row.style.display = show ? '' : 'none';
                if (show) visible += 1;
            });

            count.textContent = `${visible} filas`;
        }

        function normalizeRows(payload) {
            if (Array.isArray(payload)) return payload;
            if (payload && Array.isArray(payload.data)) return payload.data;
            if (payload && typeof payload === 'object') return [payload];
            return [];
        }

        async function loadData() {
            applyTheme();
            const params = new URLSearchParams(window.location.search);
            const endpoint = params.get('endpoint') || '/api/products';
            const title = params.get('title') || 'Vista de datos';

            document.getElementById('title').textContent = title;
            document.getElementById('endpointLabel').textContent = endpoint;
            document.getElementById('openRaw').href = endpoint;

            const status = document.getElementById('status');
            const result = document.getElementById('result');

            if (!endpoint.startsWith('/api/')) {
                status.textContent = 'Endpoint no valido. Solo se permiten rutas /api/.';
                status.classList.add('error');
                return;
            }

            try {
                const role = localStorage.getItem('ordena-facil-role') || localStorage.getItem('barandrest-role') || 'guest';
                const response = await fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-USER-ROLE': role,
                    },
                });

                if (!response.ok) {
                    status.textContent = `Error HTTP ${response.status}`;
                    status.classList.add('error');
                    result.innerHTML = `<pre>${await response.text()}</pre>`;
                    return;
                }

                const payload = await response.json();
                const rows = normalizeRows(payload);
                status.textContent = `Registros: ${rows.length}`;
                result.innerHTML = renderTable(rows);
                document.getElementById('countLabel').textContent = `${rows.length} filas`;
            } catch (error) {
                status.textContent = `No se pudo cargar el endpoint: ${String(error.message || error)}`;
                status.classList.add('error');
            }
        }

        document.getElementById('tableFilter').addEventListener('input', applyFilter);
        loadData();
    </script>
</body>
</html>
