@extends('layouts.admin_master')
@section('content')

<div class="container mt-4">
    <h4>Profit & Loss Report – {{ $year }}</h4>

    <form method="GET" action="{{ url('/profit-loss') }}" class="mb-3 d-flex gap-3">
        <div>
            <label for="year">Year:</label>
            <select name="year" id="year" onchange="this.form.submit()" class="form-select">
                @foreach ($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="view">View:</label>
            <select name="view" id="view" onchange="this.form.submit()" class="form-select">
                <option value="monthly" {{ $view === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="quarterly" {{ $view === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="ytd" {{ $view === 'ytd' ? 'selected' : '' }}>Year-to-Date</option>
                <option value="comparison" {{ $view === 'comparison' ? 'selected' : '' }}>Prior Year Comparison</option>
            </select>
        </div>
    </form>

    <canvas id="profitLossChart" style="height: 400px;"></canvas>

    <div class="mt-4">
        <button class="btn btn-outline-secondary" onclick="alert('CSV export coming soon')">Export CSV</button>
        <button class="btn btn-outline-secondary" onclick="alert('PDF export coming soon')">Export PDF</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chart = new Chart(document.getElementById('profitLossChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyData, 'label')) !!},
            datasets: [
                {
                    label: 'Revenue',
                    data: {!! json_encode(array_column($monthlyData, 'sales')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                },
                {
                    label: 'COGS',
                    data: {!! json_encode(array_column($monthlyData, 'cogs')) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                },
                {
                    label: 'Overhead',
                    data: {!! json_encode(array_column($monthlyData, 'overhead')) !!},
                    backgroundColor: 'rgba(255, 206, 86, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Net Profit Breakdown – {{ $year }}'
                }
            }
        }
    });
</script>
@endsection
