<!-- Shipment Details Modal -->
<style>
    .section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #495057;
        margin-top: 20px;
        margin-bottom: 10px;
        text-transform: uppercase;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 4px;
    }

    .info-item label {
        font-weight: 600;
        color: #6c757d;
        display: block;
        font-size: 0.85rem;
        margin-bottom: 2px;
    }

    .info-item div {
        font-size: 1rem;
        font-weight: 500;
        color: #2c2c2c;
        padding: 6px 8px;
        border-bottom: 1px solid #f1f3f5;
        border-radius: 4px;
        background-color: #fafafa;
    }

    #m_boxes .list-group-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 6px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    .modal-header,
    .modal-footer {
        border-radius: 0;
    }
    .modal-content {
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
</style>

<div class="modal fade" id="shipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded-4">

            <!-- Header -->
            <div class="modal-header rounded-top-4">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="fas fa-box text-primary me-2"></i> Shipment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">

                <!-- Basic Information -->
                <h6 class="section-title">Basic Information</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6 info-item">
                        <label>Shipment ID</label>
                        <div id="m_ship_id">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Order ID</label>
                        <div id="m_order_id">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Customer</label>
                        <div id="m_customer">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Carrier</label>
                        <div id="m_carrier">-</div>
                    </div>
                </div>

                <!-- Tracking & Status -->
                <h6 class="section-title">Tracking & Status</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6 info-item">
                        <label>Tracking #</label>
                        <div id="m_tracking">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Last Scan</label>
                        <div id="m_last_scan">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>ETA</label>
                        <div id="m_eta">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Status</label>
                        <div><span id="m_status" class="badge bg-secondary">-</span></div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Delivered At</label>
                        <div id="m_delivered_at">-</div>
                    </div>
                    <div class="col-md-6 info-item">
                        <label>Service Type</label>
                        <div id="m_service">-</div>
                    </div>
                </div>

                <!-- Boxes -->
                <h6 class="section-title">Boxes</h6>
                <ul id="m_boxes" class="list-group mb-0"></ul>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-light border-top rounded-bottom-4">
                <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const shipmentModalEl = document.getElementById('shipmentModal');

    const shipmentModal = new bootstrap.Modal(shipmentModalEl);

    shipmentModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            shipmentModal.hide();
        });
    });

</script>