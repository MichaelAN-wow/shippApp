@extends('layouts.admin_master')
@section('content')

<div class="container-fluid mt-4">
    <!-- Dashboard-style Header -->
    <div class="card-dashboard mb-4" style="position: relative;">
        <div class="card-dashboard-header"> Replenish </div>
        <span class="badge badge-warning" 
              style="position: absolute; top: 8px; right: 12px; font-size: 0.6rem;">BETA</span>
    </div>

    <p class="text-muted mb-3">
        This tool will help you determine what to remake after a market based on what was sold. 
        Smart restock logic and sales tie-ins are coming soon!
    </p>

    <div class="card shadow-sm">
        <div class="card-body text-center text-muted">
            <p><i class="fas fa-dolly-flatbed fa-2x mb-2" style="color:#FFD700;"></i></p>
            <p class="mb-0">Restock logic is coming — this feature will learn from your sales and suggest refills automatically.</p>
            <small>You'll be able to batch, restock, and prep your shelves in seconds.</small>
        </div>
    </div>
</div>

@endsection