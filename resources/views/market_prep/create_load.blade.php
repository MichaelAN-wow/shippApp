@extends('layouts.admin_master')
@section('content')

<div class="container-fluid mt-4">
    <!-- Dashboard-style Header -->
    <div class="card-dashboard mb-4" style="position: relative;">
        <div class="card-dashboard-header"> Load Out Planner </div>
        <span class="badge badge-warning" 
              style="position: absolute; top: 8px; right: 12px; font-size: 0.6rem;">BETA</span>
    </div>

    <p class="text-muted mb-3">
        Use this tool to prep what inventory to bring to your next market or pop-up. Smart autofill, event templates, and restock triggers coming soon.
    </p>

    <div class="card shadow-sm">
        <div class="card-body text-center text-muted">
            <p><i class="fas fa-boxes fa-2x mb-2" style="color:#FFD700;"></i></p>
            <p class="mb-0">This feature is still under construction — but not for long.</p>
            <small>We’re building smart packing logic based on your actual sales and inventory.</small>
        </div>
    </div>
</div>

@endsection