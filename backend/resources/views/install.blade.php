<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalar - Ordena Facil</title>
    <meta name="theme-color" content="#F2911B">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/assets/branding/comanda-deg.png" type="image/png">
    <link rel="apple-touch-icon" href="/assets/branding/comanda-deg.png">
    <style>
        :root {
            --c1: #F2C230;
            --c2: #F2911B;
            --c3: #F24607;
            --c4: #BF1304;
            --c5: #730C02;
            --bg: #2a1409;
            --bg-soft: #4b180a;
            --panel: #5b1d0f;
            --border: rgba(255,255,255,0.16);
            --text: #fff4ea;
            --muted: #ffd8c0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Figtree", "Segoe UI", sans-serif;
            color: var(--text);
            min-height: 100vh;
            background:
                radial-gradient(900px 500px at -10% -20%, rgba(242,70,7,0.38), transparent 55%),
                radial-gradient(900px 500px at 110% -20%, rgba(242,194,48,0.30), transparent 55%),
                linear-gradient(180deg, var(--bg), var(--bg-soft));
        }

        .wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px;
            display: grid;
            gap: 14px;
        }

        .hero, .card {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: linear-gradient(165deg, var(--panel), #3f140a);
            box-shadow: 0 14px 30px rgba(0,0,0,0.22);
        }

        .hero {
            padding: 18px;
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
            background: rgba(0,0,0,0.25);
            padding: 6px;
        }

        h1 { margin: 0; font-size: clamp(22px, 3vw, 32px); }
        p { margin: 6px 0 0; color: var(--muted); }

        .btn {
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.08);
            color: var(--text);
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn:hover { background: rgba(242,145,27,0.22); }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .card { padding: 14px; }
        .card h2 { margin: 0 0 8px; font-size: 17px; }
        .card ol { margin: 0; padding-left: 18px; color: var(--muted); }
        .card li { margin-bottom: 6px; }

        .hint {
            margin-top: 8px;
            font-size: 12px;
            color: #ffe8b7;
        }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="hero">
            <div class="brand">
                <img src="/assets/branding/comanda-deg.png" alt="Logo Ordena Facil">
                <div>
                    <h1>Instalador Ordena Facil</h1>
                    <p>Instalacion para PC, laptop, tablet y movil desde una sola base del sistema.</p>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button id="btnInstall" class="btn" type="button" style="display:none;">Instalar app</button>
                <a class="btn" href="/">Volver al sistema</a>
            </div>
        </section>

        <section class="grid">
            <article class="card">
                <h2>PC / Laptop (Windows)</h2>
                <ol>
                    <li>Abre PowerShell en <code>backend/</code>.</li>
                    <li>Ejecuta <code>./scripts/install_universal.ps1</code>.</li>
                    <li>Usa el acceso directo <code>Ordena Facil - Iniciar</code>.</li>
                    <li>Para detener, usa <code>Ordena Facil - Detener</code>.</li>
                    <li>Para actualizar cambios futuros, ejecuta <code>./scripts/update_universal.ps1</code>.</li>
                </ol>
            </article>

            <article class="card">
                <h2>Android (Chrome/Edge)</h2>
                <ol>
                    <li>Abre la URL del sistema en el navegador.</li>
                    <li>Toca menu y selecciona <code>Instalar app</code> o <code>Agregar a pantalla principal</code>.</li>
                    <li>Confirma y abre Ordena Facil como app.</li>
                </ol>
            </article>

            <article class="card">
                <h2>iPhone / iPad (Safari)</h2>
                <ol>
                    <li>Abre la URL del sistema en Safari.</li>
                    <li>Toca compartir y elige <code>Anadir a pantalla de inicio</code>.</li>
                    <li>Confirma para usarla como app.</li>
                </ol>
            </article>

            <article class="card">
                <h2>Tablet / Otros dispositivos</h2>
                <ol>
                    <li>Abre la app en navegador moderno.</li>
                    <li>Instala como PWA desde el menu del navegador.</li>
                    <li>Usa modo pantalla completa para operacion continua.</li>
                </ol>
                <div class="hint">Recomendado: HTTPS en produccion para mejor experiencia de instalacion.</div>
            </article>
        </section>
    </main>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }

        let deferredPrompt = null;
        const btnInstall = document.getElementById('btnInstall');

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;
            btnInstall.style.display = '';
        });

        btnInstall.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            btnInstall.style.display = 'none';
        });
    </script>
</body>
</html>
