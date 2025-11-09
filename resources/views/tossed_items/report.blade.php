@extends('layouts.admin_master')

@section('content')
<div class="container py-5" style="max-width: 1200px;">
    <h2 class="mb-4">🗑️ Tossed Items Report</h2>

    @if(session('success'))
        <div class="alert" style="background-color: #28a745; color: white;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="alert" style="background-color: #dc3545; color: white;">
            @if(session('error'))
                {{ session('error') }}
            @endif
            @if($errors->any())
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- Loss Summary Section -->
    @if(isset($lossSummary) && $lossSummary['total']->total_incidents > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">📊 Inventory Loss Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-danger">{{ number_format($lossSummary['total']->total_incidents) }}</h3>
                                <p class="text-muted">Total Incidents</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-warning">{{ number_format($lossSummary['total']->total_quantity_lost) }}</h3>
                                <p class="text-muted">Total Quantity Lost</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-danger">${{ number_format($lossSummary['total']->total_value_lost, 2) }}</h3>
                                <p class="text-muted">Total Value Lost</p>
                            </div>
                        </div>
                    </div>

                    @if($lossSummary['by_reason']->count() > 0)
                    <hr>
                    <h6>Loss Breakdown by Reason:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Reason</th>
                                    <th class="text-center">Incidents</th>
                                    <th class="text-center">Quantity Lost</th>
                                    <th class="text-center">Value Lost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lossSummary['by_reason'] as $reasonSummary)
                                <tr>
                                    <td>{{ $reasonSummary->reason ?: 'No reason provided' }}</td>
                                    <td class="text-center">{{ number_format($reasonSummary->total_incidents) }}</td>
                                    <td class="text-center">{{ number_format($reasonSummary->total_quantity_lost) }}</td>
                                    <td class="text-center">${{ number_format($reasonSummary->total_value_lost, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tossedItems->isEmpty())
        <div class="alert alert-info">
            No tossed items have been logged yet.
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detailed Tossed Items Log</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped bg-white">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Material</th>
                            <th class="text-center">Quantity Tossed</th>
                            <th>Unit</th>
                            <th class="text-center">Value Lost</th>
                            <th>Reason</th>
                            <th>Tossed By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tossedItems as $item)
                            <tr>
                                <td>{{ $item->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <strong>{{ $item->material->name ?? '—' }}</strong>
                                    @if($item->material && $item->material->sku)
                                        <br><small class="text-muted">SKU: {{ $item->material->sku }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($item->quantity) }}</td>
                                <td>{{ $item->material->unit ?? '—' }}</td>
                                <td class="text-center">
                                    @if($item->material && $item->material->price_per_unit)
                                        <span class="text-danger font-weight-bold">
                                            ${{ number_format($item->quantity * $item->material->price_per_unit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $item->reason ?? '—' }}</td>
                                <td>
                                    @if($item->user)
                                        <strong>{{ $item->user->name }}</strong>
                                        <br><small class="text-muted">{{ ucfirst($item->user->type ?? 'user') }}</small>
                                    @else
                                        <span class="text-muted">Unknown User</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('tossed_items.destroy', $item->id) }}" onsubmit="return confirm('Restore inventory and delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-warning" title="Restore to inventory">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection