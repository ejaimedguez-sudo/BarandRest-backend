@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-md-6">
            <canvas id="salesChart"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Top Productos</h4>
            <ul id="topProducts"></ul>
            <h4>Low Stock</h4>
            <ul id="lowStock"></ul>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadMetrics(){
    const res = await fetch('/api/dashboard/metrics');
    const data = await res.json();

    const labels = data.weeklySales.map(i => i.day);
    const totals = data.weeklySales.map(i => parseFloat(i.total));

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Ventas', data: totals, borderColor: 'rgba(75,192,192,1)', tension: 0.3 }] },
    });

    const topList = document.getElementById('topProducts');
    data.topProducts.forEach(p => { const li = document.createElement('li'); li.textContent = `${p.name} — ${p.qty}`; topList.appendChild(li); });

    const lowList = document.getElementById('lowStock');
    data.lowStock.forEach(p => { const li = document.createElement('li'); li.textContent = `${p.name} — ${p.stock}`; lowList.appendChild(li); });
}

document.addEventListener('DOMContentLoaded', loadMetrics);
</script>
@endpush
