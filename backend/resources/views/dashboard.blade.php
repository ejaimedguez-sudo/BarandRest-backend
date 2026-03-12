<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - BarandRest</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --bg: #f9f1eb;
      --panel: #ffffff;
      --text: #2b160c;
      --muted: #7f5d4a;
      --border: #f0dccd;
      --line: #e06000;
      --line-soft: rgba(224, 96, 0, 0.14);
      --danger: #b91c1c;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Figtree", "Segoe UI", sans-serif;
      color: var(--text);
      background: radial-gradient(1000px 400px at 0% -30%, rgba(224,96,0,0.12), transparent 60%), var(--bg);
    }

    .brand-mini {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .brand-mini img {
      width: 36px;
      height: 36px;
      object-fit: contain;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: #fff;
      padding: 4px;
    }

    .wrap {
      max-width: 1180px;
      margin: 0 auto;
      padding: 18px;
      display: grid;
      gap: 14px;
    }

    .head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .head h1 {
      margin: 0;
      font-size: 28px;
    }

    .head p {
      margin: 4px 0 0;
      color: var(--muted);
    }

    .tag {
      padding: 8px 12px;
      border-radius: 999px;
      background: #ffedd5;
      color: #9a3412;
      font-size: 12px;
      font-weight: 700;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
    }

    .card {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 12px;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .card h3 {
      margin: 0;
      font-size: 13px;
      color: var(--muted);
      font-weight: 600;
    }

    .metric {
      margin-top: 8px;
      font-size: 24px;
      font-weight: 700;
    }

    .chart-panel {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 14px;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .chart-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }

    .chart-head h2 {
      margin: 0;
      font-size: 18px;
    }

    .chart-head small {
      color: var(--muted);
    }

    .empty {
      display: none;
      margin-top: 10px;
      border-radius: 12px;
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      padding: 12px;
      color: var(--muted);
      font-size: 13px;
    }

    .fallback {
      display: none;
      margin-top: 10px;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: #f8fafc;
    }

    .fallback h3 {
      margin: 0 0 8px;
      font-size: 14px;
    }

    .bar {
      margin: 8px 0;
    }

    .bar-label {
      font-size: 12px;
      color: #5b3f31;
      margin-bottom: 4px;
    }

    .bar-track {
      height: 10px;
      border-radius: 999px;
      background: #f3e0d3;
      overflow: hidden;
    }

    .bar-fill {
      height: 100%;
      background: linear-gradient(90deg, #c2410c, #e06000);
    }

    .error {
      display: none;
      border-radius: 12px;
      border: 1px solid rgba(185, 28, 28, 0.3);
      background: rgba(185, 28, 28, 0.08);
      color: var(--danger);
      padding: 10px 12px;
      font-size: 13px;
    }

    @media (max-width: 980px) {
      .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 560px) {
      .grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <main class="wrap">
    <section class="head">
      <div>
        <div class="brand-mini">
          <img src="/assets/branding/comanda-deg.png" alt="Logo BarandRest">
          <div>
            <h1>Dashboard</h1>
            <p>Rendimiento semanal y resumen rapido de ventas.</p>
          </div>
        </div>
      </div>
      <div class="tag">Actualizado en vivo</div>
    </section>

    <section class="grid">
      <article class="card">
        <h3>Total Semana</h3>
        <div class="metric" id="kpiTotal">--</div>
      </article>
      <article class="card">
        <h3>Promedio Diario</h3>
        <div class="metric" id="kpiAvg">--</div>
      </article>
      <article class="card">
        <h3>Mejor Dia</h3>
        <div class="metric" id="kpiBest">--</div>
      </article>
      <article class="card">
        <h3>Registros</h3>
        <div class="metric" id="kpiCount">--</div>
      </article>
    </section>

    <section class="chart-panel">
      <div class="chart-head">
        <h2>Ventas por dia</h2>
        <small id="rangeLabel"></small>
      </div>

      <div id="errorBox" class="error"></div>
      <div id="emptyBox" class="empty">No hay datos semanales para mostrar.</div>

      <canvas id="salesChart" height="120"></canvas>
      <div id="chartFallback" class="fallback"></div>
    </section>
  </main>

  <script>
    function fmt(value) {
      return new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(value || 0));
    }

    function showError(msg) {
      const box = document.getElementById('errorBox');
      box.textContent = msg;
      box.style.display = 'block';
    }

    function setKpis(labels, sales) {
      const total = sales.reduce((acc, v) => acc + v, 0);
      const avg = sales.length ? total / sales.length : 0;
      const max = sales.length ? Math.max(...sales) : 0;
      const maxIdx = sales.indexOf(max);
      const bestDay = maxIdx >= 0 ? labels[maxIdx] : '--';

      document.getElementById('kpiTotal').textContent = fmt(total);
      document.getElementById('kpiAvg').textContent = fmt(avg);
      document.getElementById('kpiBest').textContent = bestDay;
      document.getElementById('kpiCount').textContent = String(sales.length);
    }

    function renderFallback(labels, sales) {
      const fallback = document.getElementById('chartFallback');
      fallback.style.display = 'block';
      fallback.innerHTML = '<h3>Vista alternativa de ventas</h3>' + labels.map((label, idx) => {
        const value = Number(sales[idx] || 0);
        const width = Math.max(4, Math.min(100, Math.round(value)));
        return '<div class="bar"><div class="bar-label">' + label + ': ' + fmt(value) + '</div><div class="bar-track"><div class="bar-fill" style="width:' + width + '%"></div></div></div>';
      }).join('');
    }

    const startDate = "{{ date('Y-m-d', strtotime('-6 days')) }}";
    const endDate = "{{ date('Y-m-d') }}";
    const weeklyUrl = `/api/reports/weekly?start=${encodeURIComponent(startDate)}&end=${encodeURIComponent(endDate)}`;

    document.getElementById('rangeLabel').textContent = `${startDate} a ${endDate}`;

    fetch(weeklyUrl)
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then(data => {
        const labels = data.map(d => d.day);
        const sales = data.map(d => Number(d.sales || 0));

        setKpis(labels, sales);

        if (!labels.length) {
          document.getElementById('emptyBox').style.display = 'block';
          return;
        }

        if (typeof window.Chart === 'function') {
          new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: {
              labels,
              datasets: [{
                label: 'Ventas',
                data: sales,
                borderColor: '#e06000',
                backgroundColor: 'rgba(224, 96, 0, 0.14)',
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointHoverRadius: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              plugins: {
                legend: { display: false }
              },
              scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(160, 120, 90, 0.22)' } },
                x: { grid: { display: false } }
              }
            }
          });
        } else {
          document.getElementById('salesChart').style.display = 'none';
          renderFallback(labels, sales);
        }
      })
      .catch(error => {
        showError(`No se pudo cargar el reporte semanal (${String(error.message || error)}).`);
        document.getElementById('salesChart').style.display = 'none';
      });
  </script>
</body>
</html>
