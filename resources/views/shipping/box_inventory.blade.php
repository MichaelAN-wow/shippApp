@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Box Inventory</h1>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    
    <form action="{{ route('shipping.box_inventory.store') }}" method="POST" class="mb-3">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Box Name</th>
                        <th>Length (cm)</th>
                        <th>Height (cm)</th>
                        <th>Width (cm)</th>
                        <th>Box Weight (LBS)</th>
                        <th>Qty</th>
                        <th>Supplier</th>
                        <th>Cost</th>
                        <th style="width:90px;">Add</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="name" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="length" class="form-control"></td>
                        <td><input type="number" step="0.01" name="height" class="form-control"></td>
                        <td><input type="number" step="0.01" name="width" class="form-control"></td>
                        <td><input type="number" step="0.01" name="empty_weight" class="form-control"></td>
                        <td><input type="number" name="quantity" class="form-control" value="0" required></td>
                        <td><input name="supplier" class="form-control"></td>
                        <td><input type="number" step="0.01" name="cost" class="form-control"></td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm">Add</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>

    
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Length (cm)</th>
                    <th>Height (cm)</th>
                    <th>Width (cm)</th>
                    <th>Weight (LBS)</th>
                    <th>Qty</th>
                    <th>Supplier</th>
                    <th>Cost</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boxes as $box)
                    <tr>
                        <td>{{ $box->name }}</td>
                        <td>{{ $box->length }}</td>
                        <td>{{ $box->height }}</td>
                        <td>{{ $box->width }}</td>
                        <td>{{ $box->weight_lbs_ounces }}</td>
                        <td>{{ $box->quantity }}</td>
                        <td>{{ $box->supplier }}</td>
                        <td>{{ $box->cost }}</td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#editBox{{ $box->id }}">Edit</button>
                            <form action="{{ route('shipping.box_inventory.destroy', $box->id) }}" method="POST" onsubmit="return confirm('Delete this box?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="collapse" id="editBox{{ $box->id }}">
                        <td colspan="9">
                            <form action="{{ route('shipping.box_inventory.update', $box->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-3"><input name="name" class="form-control" value="{{ $box->name }}" required></div>
                                    <div class="col-md-1"><input type="number" step="0.01" name="length" class="form-control" value="{{ $box->length }}"></div>
                                    <div class="col-md-1"><input type="number" step="0.01" name="height" class="form-control" value="{{ $box->height }}"></div>
                                    <div class="col-md-1"><input type="number" step="0.01" name="width" class="form-control" value="{{ $box->width }}"></div>
                                    <div class="col-md-1"><input type="number" step="0.01" name="empty_weight" class="form-control" value="{{ $box->empty_weight }}"></div>
                                    <div class="col-md-1"><input type="number" name="quantity" class="form-control" value="{{ $box->quantity }}" required></div>
                                    <div class="col-md-2"><input name="supplier" class="form-control" value="{{ $box->supplier }}"></div>
                                    <div class="col-md-1"><input type="number" step="0.01" name="cost" class="form-control" value="{{ $box->cost }}"></div>
                                    <div class="col-md-1">
                                        <button class="btn btn-success w-100">Save</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No boxes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    
    <div class="mt-2">
        {{ $boxes->links() }}
    </div>
</div>
@endsection
