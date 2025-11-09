@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Shipped Packages</h1>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Order</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Carrier</th>
                <th>Tracking #</th>
                <th>Status</th>
                <th>Label</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>
                        @if($s->order)
                            #{{ $s->order->id }} ({{ $s->order->status }})
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $s->sender?->name ?? '-' }}</td>
                    <td>{{ $s->receiver?->name ?? '-' }}</td>
                    <td>{{ $s->carrier }}</td>
                    <td>{{ $s->tracking_number ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $s->status === 'delivered' ? 'success' : ($s->status === 'pending' ? 'warning' : 'info') }}">
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td>
                        @if($s->label_path)
                            <a href="{{ asset('storage/' . $s->label_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $s->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">No shipments yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $shipments->links() }}
    </div>
</div>
@endsection
