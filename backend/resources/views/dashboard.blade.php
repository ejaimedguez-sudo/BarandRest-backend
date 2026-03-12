<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - BarandRest</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>body{font-family:Arial,Helvetica,sans-serif;padding:20px}</style>
</head>
<body>
  <h1>Dashboard</h1>
  <canvas id="salesChart" width="600" height="300"></canvas>
  <div id="chartFallback" style="display:none;margin-top:16px"></div>
  <script>
    function renderFallback(labels, sales) {
      const fallback = document.getElementById('chartFallback');
      fallback.style.display = 'block';
      fallback.innerHTML = `<h3 style="margin:0 0 8px">Ventas semanales</h3>${labels.map((label, idx) => {
        const value = Number(sales[idx] || 0);
        const width = Math.max(4, Math.min(100, Math.round(value)));
        return `<div style=\"margin:8px 0\"><div style=\"font-size:12px;color:#444\">${label}: ${value.toFixed(2)}</div><div style=\"height:10px;background:#e8eef8;border-radius:6px;overflow:hidden\"><div style=\"height:100%;width:${width}%;background:#2563eb\"></div></div></div>`;
      }).join('')}`;
    }

    const startDate = "{{ date('Y-m-d', strtotime('-6 days')) }}";
    const endDate = "{{ date('Y-m-d') }}";
    const weeklyUrl = `/api/reports/weekly?start=${encodeURIComponent(startDate)}&end=${encodeURIComponent(endDate)}`;

    fetch(weeklyUrl)
      .then(r => {
        if (!r.ok) {
          throw new Error(`HTTP ${r.status}`);
        }
        return r.json();
      })
      .then(data => {
        const labels = data.map(d => d.day);
        const sales = data.map(d => +d.sales);

        if (typeof window.Chart === 'function') {
          new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Ventas', data: sales, borderColor: 'blue', fill: false }] },
          });
        } else {
          renderFallback(labels, sales);
        }
      })
      .catch(error => {
        document.body.insertAdjacentHTML('beforeend', `<p style="color:#b00020">No se pudo cargar el reporte semanal (${String(error.message || error)}).</p>`);
      });
  </script>
</body>
</html>
