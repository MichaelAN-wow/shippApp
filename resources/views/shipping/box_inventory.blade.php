@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <h1 class="m-4">Box Inventory</h1>

    {{-- Alerts --}}
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

    {{-- Add New Box Form --}}
    <form action="{{ route('shipping.box_inventory.store') }}" method="POST" class="m-4">
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
                        <td><input type="number" step="0.01" name="length" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="height" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="width" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="empty_weight" class="form-control" required></td>
                        <td><input type="number" name="quantity" class="form-control" value="0" required></td>
                        <td><input name="supplier" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="cost" class="form-control" required></td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm">Add</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>

    {{-- Box Inventory Table --}}
    <div class="table-responsive m-4" style="width:97%">
        <table class="table table-bordered table-striped align-middle" id="boxInventoryTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Length (cm)</th>
                    <th>Height (cm)</th>
                    <th>Width (cm)</th>
                    <th>Weight (LBS)</th>
                    <th>Qty</th>
                    <th>Supplier</th>
                    <th>Cost</th>
                    <th style="width:8%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boxes as $box)
                <tr>
                    <td>{{ $box->name }}</td>
                    <td>{{ $box->length }}</td>
                    <td>{{ $box->height }}</td>
                    <td>{{ $box->width }}</td>
                    <td>{{ $box->empty_weight }}</td>
                    <td>{{ $box->quantity }}</td>
                    <td>{{ $box->supplier }}</td>
                    <td>{{ $box->cost }}</td>
                    <td class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-secondary edit-btn"
                                type="button"
                                data-id="{{ $box->id }}"
                                data-name="{{ $box->name }}"
                                data-length="{{ $box->length }}"
                                data-height="{{ $box->height }}"
                                data-width="{{ $box->width }}"
                                data-weight="{{ $box->empty_weight }}"
                                data-qty="{{ $box->quantity }}"
                                data-supplier="{{ $box->supplier }}"
                                data-cost="{{ $box->cost }}">
                            Edit
                        </button>
                        <form action="{{ route('shipping.box_inventory.destroy', $box->id) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this box?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- {{-- Collapsible Edit Row (ignored by DataTables) --}}
                <tr class="collapse ignore-dt" id="editBox{{ $box->id }}">
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
                                <div class="col-md-1"><button class="btn btn-success w-100">Save</button></div>
                            </div>
                        </form>
                    </td>
                </tr> -->
                @empty
                <tr>
                    <td colspan="9" class="text-center">No boxes found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Box Modal -->
<div class="modal fade" id="editBoxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBoxForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Box</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input id="edit_name" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Length (cm)</label>
                            <input id="edit_length" type="number" step="0.01" name="length" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Height (cm)</label>
                            <input id="edit_height" type="number" step="0.01" name="height" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Width (cm)</label>
                            <input id="edit_width" type="number" step="0.01" name="width" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Weight (LBS)</label>
                            <input id="edit_weight" type="number" step="0.01" name="empty_weight" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input id="edit_qty" type="number" name="quantity" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <input id="edit_supplier" name="supplier" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Cost</label>
                            <input id="edit_cost" type="number" step="0.01" name="cost" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    $('#boxInventoryTable').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],

        // Ignore collapsible edit rows
        rowCallback: function(row) {
            if ($(row).hasClass('ignore-dt')) {
                $(row).hide();
            }
        }
    });

    // OPEN MODAL ON EDIT CLICK
    $('.edit-btn').on('click', function () {
        let id = $(this).data('id');

        // Fill modal inputs
        $('#edit_name').val($(this).data('name'));
        $('#edit_length').val($(this).data('length'));
        $('#edit_height').val($(this).data('height'));
        $('#edit_width').val($(this).data('width'));
        $('#edit_weight').val($(this).data('weight'));
        $('#edit_qty').val($(this).data('qty'));
        $('#edit_supplier').val($(this).data('supplier'));
        $('#edit_cost').val($(this).data('cost'));

        // Update form action URL
        let updateUrl = "/shipping/box-inventory/" + id; 
        $('#editBoxForm').attr('action', updateUrl);

        // Open the modal
        $('#editBoxModal').modal('show');
    });
});
</script>
@endsection
