<h3>In Transit</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover" id="inTransitTable" style="table-layout: fixed; width: 100%;">
        <thead class="table-light">
            <tr>
                <th>Shipment ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Carrier</th>
                <th>Tracking #</th>
                <th>Last Scan</th>
                <th>ETA</th>
                <th>Status</th>
                <th style="width: 80px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inTransit ?? [] as $shipment)
            <tr>
                <td>{{ $shipment->id }}</td>
                <td>{{ $shipment->order_id ?? '-' }}</td>
                <td class="text-truncate" title="{{ $shipment->customer_name ?? '-' }}">{{ $shipment->customer_name ?? '-' }}</td>
                <td>{{ $shipment->carrier ?? '-' }}</td>
                <td>{{ $shipment->tracking_number ?? '-' }}</td>
                <td>{{ $shipment->last_tracked_at ?? '-' }}</td>
                <td>{{ $shipment->estimated_delivery ?? '-' }}</td>
                <td>{{ ucfirst($shipment->status) }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionMenu{{ $shipment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionMenu{{ $shipment->id }}" style="z-index: 1000;position: absolute;">
                            <li>
                                <button class="dropdown-item btn-in-transit-view" 
                                    data-shipment-id="{{ $shipment->id }}" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#shipmentModal">
                                    View
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item btn-mark-delivered" 
                                    data-shipment-id="{{ $shipment->id }}" 
                                    data-update-url="{{ route('shipping.shipped.updateStatus', $shipment->id) }}">
                                    Mark Delivered
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item btn-report-lost"
                                    data-shipment-id="{{ $shipment->id }}"
                                    data-update-url="{{ route('shipping.shipped.updateStatus', $shipment->id) }}">
                                    Report Lost
                                </button>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No shipments in transit</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

