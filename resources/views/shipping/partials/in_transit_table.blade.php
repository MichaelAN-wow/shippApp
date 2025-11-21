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
                        <ul class="dropdown-menu" aria-labelledby="actionMenu{{ $shipment->id }}">
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
                            <li><button class="dropdown-item btn-report-lost" data-shipment-id="{{ $shipment->id }}">Report Lost</button></li>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('inTransitTable');

        table.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-mark-delivered');
            if (!btn) return;

            e.preventDefault();

            const shipmentId = btn.dataset.shipmentId;
            const updateUrl = btn.dataset.updateUrl; // <-- Get URL from data attribute
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (!confirm(`Are you sure you want to mark shipment #${shipmentId} as Delivered?`)) return;

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status: 'delivered' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = btn.closest('tr');
                    row.querySelector('td:nth-child(8)').textContent = 'Delivered';
                    toastr.success('Shipment marked as Delivered!');
                } else {
                    toastr.error(data.message || 'Failed to update shipment.');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                toastr.error('Failed to update shipment.');
            });
        });

        // Optional: Report Lost
        table.querySelectorAll('.btn-report-lost').forEach(btn => {
            btn.addEventListener('click', function() {
                const shipmentId = this.dataset.shipmentId;
                alert(`Report Shipment #${shipmentId} as lost`);
                
            });
        });

        const shipmentModal = document.getElementById('shipmentModal');

        shipmentModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget; 
            const shipmentId = btn.dataset.shipmentId;

            const inTransit = @json($inTransit ?? []);
            const shipment = inTransit.find(s => s.id == shipmentId);

            if (!shipment) {
                toastr.error('Shipment details not found.');
                return;
            }

            // --- Populate Basic Info ---
            document.getElementById('m_ship_id').innerText = shipment.id ?? '-';
            document.getElementById('m_order_id').innerText = shipment.order_id ?? '-';
            document.getElementById('m_customer').innerText = shipment.customer_name ?? '-';
            document.getElementById('m_carrier').innerText = shipment.carrier ?? '-';

            // --- Populate Tracking & Status ---
            document.getElementById('m_tracking').innerText = shipment.tracking_number ?? '-';
            document.getElementById('m_last_scan').innerText = shipment.last_tracked_at ?? '-';
            document.getElementById('m_eta').innerText = shipment.estimated_delivery ?? '-';
            
            const statusEl = document.getElementById('m_status');
            statusEl.innerText = shipment.status ?? '-';
            statusEl.className = 'badge ' + getStatusBadgeClass(shipment.status);

            document.getElementById('m_delivered_at').innerText = shipment.delivered_at ?? '-';
            document.getElementById('m_service').innerText = shipment.service ?? '-';

            // --- Populate Boxes ---
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

            // Helper: Return badge class based on status
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
    });
</script>
