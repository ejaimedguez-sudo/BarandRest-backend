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
    fetch('/api/reports/weekly?start={{ date('Y-m-d', strtotime('-6 days')) }}&end={{ date('Y-m-d') }}')
      .then(r => r.json())
      .then(data => {
        const labels = data.map(d => d.day);
        const sales = data.map(d => +d.sales);
        new Chart(document.getElementById('salesChart').getContext('2d'), {
          type: 'line',
          data: { labels, datasets: [{ label: 'Ventas', data: sales, borderColor: 'blue', fill: false }] },
        });
      });
  </script>
</body>
</html>
