<h3>Delivered</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="deliveredTable">
        <thead>
            <tr>
                <!-- <th><input type="checkbox" id="selectAllDelivered"></th> -->
                <th>Shipment ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Carrier</th>
                <th>Tracking #</th>
                <th>Delivered At</th>
                <th style="width: 5%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($delivered ?? [] as $shipment)
            <tr>
                <!-- <td>
                    <input type="checkbox" class="select-delivered" data-shipment-id="{{ $shipment->id }}">
                </td> -->
                <td>{{ $shipment->id }}</td>
                <td>{{ $shipment->order_id ?? '-' }}</td>
                <td>{{ $shipment->customer_name ?? '-' }}</td>
                <td>{{ $shipment->carrier ?? '-' }}</td>
                <td>{{ $shipment->tracking_number ?? '-' }}</td>
                <td>{{ $shipment->delivered_at ?? '-' }}</td>
                <td>
                    <button class="btn btn-delivered-view btn-secondary" 
                        data-shipment-id="{{ $shipment->id }}" 
                        data-bs-toggle="modal"
                        data-bs-target="#shipmentModal">
                        View
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No delivered shipments</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
