@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h2>Create Shipment Label</h2>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('shipping.store', $orderId) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Sender</label>
            <select name="sender_id" class="form-control" required>
                @foreach($contacts as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->city }}, {{ $c->country }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Receiver</label>
            <select name="receiver_id" class="form-control" required>
                @foreach($contacts as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->city }}, {{ $c->country }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Box</label>
            <select name="box_id" id="boxSelect" class="form-control" required>
                <option value="">-- Select Box --</option>
                @foreach($boxes as $b)
                    <option value="{{ $b->id }}" data-length="{{ $b->length }}" data-width="{{ $b->width }}" data-height="{{ $b->height }}" data-empty_weight="{{ $b->empty_weight }}">
                        {{ $b->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Box Dimensions (L×W×H cm)</label>
            <input type="text" id="boxDimensions" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>Empty Box Weight (kg)</label>
            <input type="number" step="0.01" id="emptyWeight" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>Product Weight (kg)</label>
            <input type="number" step="0.01" name="product_weight_kg" id="productWeight" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Total Weight (kg)</label>
            <input type="number" step="0.01" id="totalWeight" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success mt-2">Generate Label</button>
    </form>
</div>

<script>
    const boxSelect = document.getElementById('boxSelect');
    const boxDimensions = document.getElementById('boxDimensions');
    const emptyWeight = document.getElementById('emptyWeight');
    const productWeight = document.getElementById('productWeight');
    const totalWeight = document.getElementById('totalWeight');

    boxSelect.addEventListener('change', () => {
        const selected = boxSelect.options[boxSelect.selectedIndex];
        const length = selected.dataset.length || 0;
        const width = selected.dataset.width || 0;
        const height = selected.dataset.height || 0;
        const empty = parseFloat(selected.dataset.empty_weight) || 0;

        boxDimensions.value = `${length} × ${width} × ${height}`;
        emptyWeight.value = empty.toFixed(2);
        totalWeight.value = (empty + parseFloat(productWeight.value || 0)).toFixed(2);
    });

    productWeight.addEventListener('input', () => {
        const empty = parseFloat(emptyWeight.value || 0);
        const product = parseFloat(productWeight.value || 0);
        totalWeight.value = (empty + product).toFixed(2);
    });
</script>
@endsection
