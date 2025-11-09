@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1>Shipping Dashboard</h1>

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body"><h5>Pending Orders</h5><h3>{{ $stats['pending_orders'] }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body"><h5>Shipped Orders</h5><h3>{{ $stats['shipped_orders'] }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body"><h5>In Transit</h5><h3>{{ $stats['in_transit'] }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body"><h5>Delivered</h5><h3>{{ $stats['delivered'] }}</h3></div></div>
        </div>
    </div>

    {{-- Pending Orders --}}
    <h4>Pending Orders</h4>
    <table class="table table-bordered">
        <thead><tr><th>Order ID</th><th>Received At</th><th>Status</th><th>Create Shipment</th></tr></thead>
        <tbody>
            @foreach($openOrders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->received_at }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createShipmentModal" 
                        data-order-id="{{ $order->id }}">
                        Create Shipment
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Recent Shipments --}}
    <h4 class="mt-5">Recent Shipments</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Order ID</th>
                <th>Tracking Number</th>
                <th>Carrier</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentShipments as $ship)
            <tr>
                <td>{{ $ship->id }}</td>
                <td>{{ $ship->order_id ?? 'N/A' }}</td>
                <td>{{ $ship->tracking_number }}</td>
                <td>{{ $ship->carrier }}</td>
                <td>{{ $ship->status }}</td>
                <td>{{ $ship->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Create Shipment Modal --}}
<div class="modal fade" id="createShipmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="createShipmentForm" method="POST" action="{{ route('shipping.store') }}">
        @csrf
        <input type="hidden" name="order_id" id="modalOrderId">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create Shipment</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">

                {{-- Sender & Receiver --}}
                <div class="form-group"><label>Sender</label><input type="text" name="sender" class="form-control"></div>
                <div class="form-group"><label>Receiver</label><input type="text" name="receiver" class="form-control"></div>

                {{-- Box --}}
                <div class="form-group">
                    <label>Box</label>
                    <select name="box_id" id="boxSelect" class="form-control">
                        <option value="">-- Select Box --</option>
                        @foreach($boxes as $box)
                        <option value="{{ $box->id }}"
                            data-length="{{ $box->length }}"
                            data-width="{{ $box->width }}"
                            data-height="{{ $box->height }}"
                            data-empty-weight="{{ $box->empty_weight }}">
                            {{ $box->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group"><label>Box Dimensions (L×W×H cm)</label><input type="text" id="boxDimensions" class="form-control" readonly></div>
                <div class="form-group"><label>Empty Box Weight (kg)</label><input type="text" id="emptyBoxWeight" class="form-control" readonly></div>
                <div class="form-group"><label>Product Weight (kg)</label><input type="number" name="product_weight" id="productWeight" class="form-control"></div>
                <div class="form-group"><label>Total Weight (kg)</label><input type="text" name="total_weight" id="totalWeight" class="form-control" readonly></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success">Create Shipment</button></div>
        </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set order_id when modal opens
    $('#createShipmentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var orderId = button.data('order-id');
        $('#modalOrderId').val(orderId);
    });

    // Auto-fill Box info
    $('#boxSelect').on('change', function() {
        var selected = $(this).find(':selected');
        var length = selected.data('length') || '';
        var width = selected.data('width') || '';
        var height = selected.data('height') || '';
        var emptyWeight = selected.data('empty-weight') || '';

        $('#boxDimensions').val(length + '×' + width + '×' + height);
        $('#emptyBoxWeight').val(emptyWeight);

        calculateTotal();
    });

    $('#productWeight').on('input', calculateTotal);

    function calculateTotal() {
        var empty = parseFloat($('#emptyBoxWeight').val()) || 0;
        var product = parseFloat($('#productWeight').val()) || 0;
        $('#totalWeight').val(empty + product);
    }
});
</script>
@endsection
