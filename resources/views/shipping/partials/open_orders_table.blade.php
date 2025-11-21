<h3>Open Orders (Needs Label)</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="openOrdersTable">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Destination</th>
                <th style="width: 30%;">Items Summary</th>
                <th style="width: 10%;">Box</th>
                <th style="width: 10%;">Weight</th>
                <th>Carrier / Service</th>
                <th>Rate</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($openOrders ?? [] as $order)
            <tr>
                <td>{{ $order->order_id ?? '-'}}</td>
                <td>{{ $order->customer_name ?? '-' }}</td>
                <td>{{ $order->destination ?? '-' }}</td>
                <td>{{ $order->notes ?? '-' }}</td>

                {{-- Box --}}
                <td>
                    {{ $order->box_name ?? '-' }}
                </td>

                {{-- Product Weight --}}
                <td>
                    {{ $order->product_weight_lb ?? 0 }} lb {{ $order->product_weight_oz ?? 0 }} oz
                </td>

                {{-- Carrier / Service --}}
                <td>
                    {{ $order->carrier ?? '-' }}
                </td>

                {{-- Rate --}}
                <td class="rate-cell">
                    $ {{ $order->tax ?? '-' }}
                </td>

                {{-- Actions --}}
                <td>
                    <button class="btn btn-primary btn-sm btn-create-shipment"
                        style="font-size: 0.7rem;"
                        data-order-id="{{ $order->id }}"
                        data-bs-toggle="modal"
                        data-bs-target="#createShipmentModal">
                        Purchase Label
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No open orders found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAllOrders');
        const checkboxes = document.querySelectorAll('.select-order');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });

        document.querySelectorAll('.btn-create-shipment').foreach(btn => {
            const orderId = this.dataset.orderId;
            window.selectedOrderID = orderId;
        })
    });
</script>
