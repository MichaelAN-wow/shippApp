@extends('layouts.admin_master')

@section('content')
<div class="container my-4">

    <h1 class="mb-4">Shipping Dashboard</h1>

   
    <div class="row mb-4">
        <div class="col"><div class="p-3 bg-light border">Pending Orders: {{ $stats['pending_orders'] }}</div></div>
        <div class="col"><div class="p-3 bg-light border">Shipped Orders: {{ $stats['shipped_orders'] }}</div></div>
        <div class="col"><div class="p-3 bg-light border">In Transit: {{ $stats['in_transit'] }}</div></div>
        <div class="col"><div class="p-3 bg-light border">Delivered: {{ $stats['delivered'] }}</div></div>
    </div>

    
    <h3>Pending Orders</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Received At</th>
                <th>Status</th>
                <th>Create Shipment</th>
            </tr>
        </thead>
        <tbody>
        @foreach($openOrders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->received_at }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <button class="btn btn-primary btn-sm btn-create-shipment" 
                        data-order-id="{{ $order->id }}"
                        data-bs-toggle="modal"
                        data-bs-target="#createShipmentModal">
                        Create Shipment
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    
    <h3>Recent Shipments</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Order ID</th>
                <th>Tracking</th>
                <th>Carrier</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($recentShipments as $shipment)
            <tr>
                <td>{{ $shipment->id }}</td>
                <td>{{ $shipment->order_id ?? 'N/A' }}</td>
                <td>{{ $shipment->tracking_number }}</td>
                <td>{{ $shipment->carrier }}</td>
                <td>{{ ucfirst($shipment->status) }}</td>
                <td>{{ $shipment->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="createShipmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('shipping.store') }}">
      @csrf
      <input type="hidden" name="order_id" id="modalOrderId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Shipment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
            
            <div class="col-md-6">
                <label class="form-label">Sender</label>
                <select name="sender_id" class="form-select" required>
                    <option value="">-- Select Sender --</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Receiver</label>
                <select name="receiver_id" class="form-select" required>
                    <option value="">-- Select Receiver --</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Box</label>
                <select name="box_id" id="modalBox" class="form-select" required>
                    <option value="">-- Select Box --</option>
                    @foreach($boxes as $b)
                        @php
                            $totalOz = $b->empty_weight * 35.2739619;
                            $lb = floor($totalOz / 16);
                            $oz = $totalOz % 16;
                        @endphp
                        <option value="{{ $b->id }}"
                            data-length="{{ $b->length }}"
                            data-width="{{ $b->width }}"
                            data-height="{{ $b->height }}"
                            data-weight-lb="{{ $lb }}"
                            data-weight-oz="{{ $oz }}">
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Box Dimensions (L×W×H cm)</label>
                <input type="text" id="modalBoxDimensions" class="form-control" readonly>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Empty Box Weight</label>
                <input type="text" id="modalBoxWeight" class="form-control" readonly>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Product Weight</label>
                <div class="input-group">
                    <input type="number" name="product_weight_lb" id="modalProductWeightLb" class="form-control" placeholder="LB" min="0" required>
                    <span class="input-group-text">lb</span>
                    <input type="number" step="0.1" name="product_weight_oz" id="modalProductWeightOz" class="form-control" placeholder="OZ" min="0" required>
                    <span class="input-group-text">oz</span>
                </div>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Total Weight</label>
                <input type="text" id="modalTotalWeight" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Generate Label</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalBox = document.getElementById('modalBox');
    const modalBoxDimensions = document.getElementById('modalBoxDimensions');
    const modalBoxWeight = document.getElementById('modalBoxWeight');
    const modalProductWeightLb = document.getElementById('modalProductWeightLb');
    const modalProductWeightOz = document.getElementById('modalProductWeightOz');
    const modalTotalWeight = document.getElementById('modalTotalWeight');
    const modalOrderId = document.getElementById('modalOrderId');

    function formatLbOz(lb, oz) {
        return `${lb} lb ${oz.toFixed(1)} oz`;
    }

    function addLbOz(lb1, oz1, lb2, oz2){
        let totalOz = oz1 + oz2;
        let extraLb = Math.floor(totalOz / 16);
        let remainingOz = totalOz % 16;
        let totalLb = lb1 + lb2 + extraLb;
        return { lb: totalLb, oz: remainingOz };
    }

    function updateTotalWeight() {
        const boxLb = parseInt(modalBox.selectedOptions[0]?.dataset.weightLb || 0);
        const boxOz = parseFloat(modalBox.selectedOptions[0]?.dataset.weightOz || 0);
        const prodLb = parseInt(modalProductWeightLb.value || 0);
        const prodOz = parseFloat(modalProductWeightOz.value || 0);

        const total = addLbOz(boxLb, boxOz, prodLb, prodOz);
        modalTotalWeight.value = formatLbOz(total.lb, total.oz);
    }

    document.querySelectorAll('.btn-create-shipment').forEach(btn => {
        btn.addEventListener('click', function() {
            modalOrderId.value = this.dataset.orderId;
            modalBoxDimensions.value = '';
            modalBoxWeight.value = '';
            modalProductWeightLb.value = '';
            modalProductWeightOz.value = '';
            modalTotalWeight.value = '';
        });
    });

    modalBox.addEventListener('change', function() {
        const selected = this.selectedOptions[0];
        if(selected){
            modalBoxDimensions.value = `${selected.dataset.length}×${selected.dataset.width}×${selected.dataset.height}`;
            const boxLb = parseInt(selected.dataset.weightLb || 0);
            const boxOz = parseFloat(selected.dataset.weightOz || 0);
            modalBoxWeight.value = formatLbOz(boxLb, boxOz);
        } else {
            modalBoxDimensions.value = '';
            modalBoxWeight.value = '0 lb 0 oz';
        }
        updateTotalWeight();
    });

    modalProductWeightLb.addEventListener('input', updateTotalWeight);
    modalProductWeightOz.addEventListener('input', updateTotalWeight);
});
</script>
@endsection
