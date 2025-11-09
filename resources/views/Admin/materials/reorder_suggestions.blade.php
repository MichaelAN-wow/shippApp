@extends('layouts.admin_master')

@section('content')
<style>
    .container {
        padding-top: 40px;
        margin: auto;
    }
    table {
        width: 100%;
    }
    table td, table th {
        padding: 0.4rem !important;
        vertical-align: middle;
        text-align: center;
        font-size: 14px;
        white-space: nowrap;
    }
    .emoji-col {
        width: 80px;
    }
    .qty-col {
        width: 90px;
    }
</style>

<div class="container" style="max-width: 1100px;">
    <h2 class="mb-3 mt-4 text-center">🧠 Smart Reorder Suggestions</h2>

    <form method="GET" class="mb-3 d-flex align-items-center gap-2 justify-content-center">
        <label for="days" class="mb-0">📅 View Based On:</label>
        <select name="days" id="days" onchange="this.form.submit()" class="form-select w-auto">
            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
            <option value="60" {{ $days == 60 ? 'selected' : '' }}>Last 60 days</option>
            <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>

    <div class="mt-3 text-center">
        <strong>Status key:</strong><br>
        🔴 Under 3 days left &nbsp;&nbsp;
        🟡 Under {{ $leadTime + $bufferDays }} days &nbsp;&nbsp;
        🟢 Plenty in stock &nbsp;&nbsp;
        ⚪️ No usage in timeframe
    </div>

    @if($suggestions->isEmpty())
        <div class="alert alert-success mt-4 text-center">All materials are above reorder levels ✅</div>
    @else
        <table class="table table-bordered table-striped bg-white mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Material</th>
                    <th>Current Stock</th>
                    <th>Avg Daily Use</th>
                    <th>Lead Time Forecast</th>
                    <th>Buffer</th>
                    <th class="emoji-col">Est. Days Left</th>
                    <th class="qty-col">Suggested Reorder Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suggestions as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->stock_level, 0) }} {{ $item->unit ?? '' }}</td>
                    <td>{{ number_format($item->avg_daily_use, 0) }}</td>
                    <td>{{ number_format($item->forecast_lead_time, 0) }}</td>
                    <td>{{ number_format($item->buffer, 0) }}</td>
                    @php
                        if ($item->estimated_days === null) {
                            $emoji = '⚪️';
                        } elseif ($item->estimated_days <= 3) {
                            $emoji = '🔴';
                        } elseif ($item->estimated_days <= ($leadTime + $bufferDays)) {
                            $emoji = '🟡';
                        } else {
                            $emoji = '🟢';
                        }
                    @endphp
                    <td>
                        @if(!is_null($item->estimated_days))
                            {{ $item->estimated_days }}
                        @endif
                        {{ $emoji }}
                    </td>
                    <td><strong>{{ number_format($item->suggested_reorder, 0) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="mt-4 text-center">
        <p><strong>Reorder logic:</strong> Avg Daily × ({{ $leadTime }} lead + {{ $bufferDays }} buffer) = {{ $leadTime + $bufferDays }} day reorder suggestion</p>
        <p><strong>Based on past {{ $days }} days of usage.</strong></p>
    </div>
</div>
@endsection