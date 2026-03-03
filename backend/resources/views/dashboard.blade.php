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
  <script>
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
        new Chart(document.getElementById('salesChart').getContext('2d'), {
          type: 'line',
          data: { labels, datasets: [{ label: 'Ventas', data: sales, borderColor: 'blue', fill: false }] },
        });
      })
      .catch(error => {
        document.body.insertAdjacentHTML('beforeend', `<p style="color:#b00020">No se pudo cargar el reporte semanal (${String(error.message || error)}).</p>`);
      });
  </script>
</body>
</html>
