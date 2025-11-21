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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllDelivered');
    const checkboxes = document.querySelectorAll('.select-delivered');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const deliveredTable = document.getElementById('deliveredTable');
    const shipmentModalEl = document.getElementById('shipmentModal');

    // Bootstrap modal instance
    const shipmentModal = new bootstrap.Modal(shipmentModalEl);

    // Convert delivered dataset from backend
    const deliveredShipments = @json($delivered ?? []);
    console.log("Delivered Shipments:", deliveredShipments);

    // Delegate click event on "View" buttons
    deliveredTable.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-devliered-view');
        if (!btn) return;

        const shipmentId = btn.dataset.shipmentId;

        // Find shipment by ID
        const shipment = deliveredShipments.find(s => s.id == shipmentId);
        console.log("Selected Shipment:", shipment);

        if (!shipment) {
            toastr.error("Shipment not found.");
            return;
        }

        document.getElementById('m_ship_id').innerText = shipment.id ?? '-';
        document.getElementById('m_order_id').innerText = shipment.order_id ?? '-';
        document.getElementById('m_customer').innerText = shipment.customer_name ?? '-';
        document.getElementById('m_carrier').innerText = shipment.carrier ?? '-';

        // ---- Tracking & Status ----
        document.getElementById('m_tracking').innerText = shipment.tracking_number ?? '-';
        document.getElementById('m_last_scan').innerText = shipment.last_tracked_at ?? '-';
        document.getElementById('m_eta').innerText = shipment.estimated_delivery ?? '-';

        const statusEl = document.getElementById('m_status');
        statusEl.innerText = shipment.status ?? '-';
        statusEl.className = 'badge ' + getStatusBadgeClass(shipment.status);

        document.getElementById('m_delivered_at').innerText = shipment.delivered_at ?? '-';
        document.getElementById('m_service').innerText = shipment.service ?? '-';

        // ---- Boxes ----
        const boxList = document.getElementById('m_boxes');
        boxList.innerHTML = '';

        if (shipment.boxes?.length) {
            shipment.boxes.forEach(box => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between';
                li.innerHTML = `<span>Box #${box.id}</span><span class="fw-bold">${box.weight} lbs</span>`;
                boxList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.className = 'list-group-item text-muted';
            li.innerText = 'No boxes recorded';
            boxList.appendChild(li);
        }

        // Show modal
        shipmentModal.show();
    });

    function getStatusBadgeClass(status) {
        switch ((status ?? '').toLowerCase()) {
            case 'delivered': return 'bg-success';
            case 'in transit': return 'bg-primary';
            case 'pending': return 'bg-warning';
            case 'failed': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
});
</script>
