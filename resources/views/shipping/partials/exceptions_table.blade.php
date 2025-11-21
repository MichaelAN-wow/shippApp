<h3>Exceptions</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="exceptionsTable">
        <thead>
            <tr>
                <!-- <th><input type="checkbox" id="selectAllExceptions"></th> -->
                <th>Shipment ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Carrier</th>
                <th>Tracking #</th>
                <th>Issue</th>
                <th style="width: 15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exceptionShipments ?? [] as $shipment)
            <tr>
                <!-- <td>
                    <input type="checkbox" class="select-exception" data-shipment-id="{{ $shipment->id }}">
                </td> -->
                <td>{{ $shipment->id }}</td>
                <td>{{ $shipment->order_id ?? '-' }}</td>
                <td>{{ $shipment->customer_name ?? '-' }}</td>
                <td>{{ $shipment->carrier ?? '-' }}</td>
                <td>{{ $shipment->tracking_number ?? '-' }}</td>
                <td>{{ $shipment->status ?? '-' }}</td>
                <td>
                    <button class="btn btn-sm btn-success mr-3">Mark as resolved</button>
                    <button class="btn btn-sm btn-success">resend</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No exceptions found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllExceptions');
    const checkboxes = document.querySelectorAll('.select-exception');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
});
</script>
