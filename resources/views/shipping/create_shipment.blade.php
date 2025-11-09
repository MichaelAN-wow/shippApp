@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1>Create Shipment</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shipping.store', $orderId) }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="order_id">Order (optional)</label>
            <select name="order_id" class="form-control">
                <option value="">-- Select Order --</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" {{ $orderId == $order->id ? 'selected' : '' }}>
                        Order #{{ $order->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="contact_id">Contact (from POS)</label>
            <select name="contact_id" class="form-control" id="contact_id">
                <option value="">-- Select Contact --</option>
                @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}">
                        {{ $contact->name }} ({{ $contact->email ?? 'no email' }})
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Selecting a contact will auto-fill recipient fields.
            </small>
        </div>

        <div class="form-group mb-3">
            <label for="recipient_name">Recipient Name</label>
            <input type="text" name="recipient_name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="destination">Street Address</label>
            <input type="text" name="destination" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="city">City</label>
                <input type="text" name="city" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="state">State</label>
                <input type="text" name="state" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="postal_code">ZIP</label>
                <input type="text" name="postal_code" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="country">Country</label>
                <input type="text" name="country" value="US" class="form-control">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="weight_kg">Package Weight (kg)</label>
            <input type="number" step="0.1" name="weight_kg" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Generate Label</button>
    </form>
</div>

<script>
document.getElementById('contact_id').addEventListener('change', function() {
    const contacts = @json($contacts);
    const contact = contacts.find(c => c.id == this.value);

    if (contact) {
        document.querySelector('[name="recipient_name"]').value = contact.name || '';
        document.querySelector('[name="destination"]').value = contact.address ?? '';
        document.querySelector('[name="city"]').value = contact.city ?? '';
        document.querySelector('[name="state"]').value = contact.state ?? '';
        document.querySelector('[name="postal_code"]').value = contact.zip ?? '';
        document.querySelector('[name="country"]').value = contact.country ?? 'US';
    }
});
</script>
@endsection
