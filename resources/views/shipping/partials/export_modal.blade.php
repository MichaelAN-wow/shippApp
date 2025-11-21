{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="exportForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Shipments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Shipment Type</label>
                        <select id="exportTabSelect" class="form-select" required>
                            <option value="open-orders" selected>Open Orders</option>
                            <option value="in-transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="exceptions">Exceptions</option>
                        </select>
                    </div>
                    <p>All key columns will be included: <strong>Order #, Customer, Carrier, Tracking, Status, Dates, Cost</strong>.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Export CSV</button>
                </div>
            </div>
        </form>
    </div>
</div>
