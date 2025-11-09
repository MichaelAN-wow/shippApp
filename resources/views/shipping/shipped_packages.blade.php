@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1>Shipped Packages</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('shipping.index') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search Tracking Number" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="carrier" class="form-control">
                <option value="">All Carriers</option>
                <option value="UPS" {{ request('carrier')=='UPS'?'selected':'' }}>UPS</option>
                <option value="USPS" {{ request('carrier')=='USPS'?'selected':'' }}>USPS</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="pending"    {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="in_transit" {{ request('status')=='in_transit'?'selected':'' }}>In Transit</option>
                <option value="delivered"  {{ request('status')=='delivered'?'selected':'' }}>Delivered</option>
                <option value="cancelled"  {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3 d-flex">
            <button class="btn btn-primary w-100 me-2">Filter</button>
            <a href="{{ route('shipping.shipments.autoUpdateAll') }}" class="btn btn-warning w-100">Track</a>
        </div>
    </form>

    
    <form method="POST" action="#" class="row mb-3" onsubmit="alert('Please implement trackPast feature or define route!'); return false;">
        @csrf
        <div class="col-md-6">
            <input type="text" name="tracking_number" class="form-control" placeholder="Enter Past Tracking Number">
        </div>
        <div class="col-md-3">
            <button class="btn btn-info w-100">Track Past Package</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Tracking #</th>
                <th>Carrier</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Status</th>
                <th>Label</th>
                <th>Actions</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $ship)
            <tr>
                <td>{{ $ship->id }}</td>
                <td>{{ $ship->tracking_no ?? '-' }}</td>
                <td>{{ $ship->carrier ?? '-' }}</td>
                <td>{{ $ship->sender_name ?? '-' }}</td>
                <td>{{ $ship->receiver_name ?? '-' }}</td>
                <td>{{ ucfirst($ship->status) ?? '-' }}</td>
                <td>
                    @if(!empty($ship->label_path) && Storage::disk('public')->exists($ship->label_path))
                        <a href="{{ route('shipping.download', $ship->id) }}" class="btn btn-sm btn-primary" target="_blank">Download</a>
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    {{-- Status Update --}}
                    <form action="{{ route('shipping.shipped.updateStatus', $ship->id) }}" method="POST" class="d-flex mb-1">
                        @csrf
                        <select name="status" class="form-select form-select-sm me-2">
                            <option value="pending"    {{ $ship->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="in_transit" {{ $ship->status=='in_transit'?'selected':'' }}>In Transit</option>
                            <option value="delivered"  {{ $ship->status=='delivered'?'selected':'' }}>Delivered</option>
                            <option value="cancelled"  {{ $ship->status=='cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                        <button class="btn btn-success btn-sm">Update</button>
                    </form>

                    {{-- Purchase Label (UPS only) --}}
                    @if(strtoupper($ship->carrier) === 'UPS')
                        <a href="{{ route('shipping.create', $ship->order_id) }}" class="btn btn-primary btn-sm w-100 mb-1">Purchase Label</a>
                        <button class="btn btn-warning btn-sm w-100 auto-update-btn" data-id="{{ $ship->id }}">Track</button>
                    @endif
                </td>
                <td>{{ $ship->created_at }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No shipments found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $shipments->links() }}
</div>

{{-- AJAX for UPS Auto Update --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.auto-update-btn').forEach(button => {
        button.addEventListener('click', function () {
            const btn = this;
            const shipmentId = btn.dataset.id;
            const row = btn.closest('tr');
            const statusCell = row.querySelector('td:nth-child(6)');

            btn.disabled = true;
            btn.textContent = 'Updating...';

            fetch(`/shipping/shipments/${shipmentId}/auto-update`)
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        statusCell.textContent = data.status ?? statusCell.textContent;
                        statusCell.style.fontWeight = 'bold';
                        statusCell.style.color = (data.status==='delivered')?'blue':'green';
                    } else {
                        alert(data.message || 'Update failed.');
                    }
                })
                .catch(err => { console.error(err); alert('Error updating shipment.'); })
                .finally(()=>{ btn.disabled=false; btn.textContent='Track'; });
        });
    });
});
</script>
@endsection
