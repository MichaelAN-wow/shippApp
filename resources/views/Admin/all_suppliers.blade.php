@extends('layouts.admin_master')
@section('content')
    <style>
        #add-material {
            color: #007bff;
            background-color: transparent !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Suppliers
        </div>
        <div class="card-dashboard-sub-header">
            <div class="card-dashboard-sub-header-controls">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
                        data-target="#addSupplierModal">
                        <i class="fas fa-plus"></i> Add Supplier
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Spent</th>
                            <th>Orders</th>
                            <th>Last Order</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $row)
                            <tr>
                                <td><a href="#" class="supplier-name"
                                        data-id="{{ $row->id }}">{{ $row->name }}</a></td>
                                <td>${{ number_format($row->total_spent, 2) }}</td>
                                <td>{{ $row->orders_count }}</td>
                                <td>{{ $row->orders->last()?->created_at }}</td>
                                <td>{{ $row->notes }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info edit-btn" data-toggle="modal"
                                        data-target="#addSupplierModal" data-id="{{ $row->id }}"
                                        title="Edit Supplier"><i class="fa fa-edit"></i></a>
                                    <a href="#" class="btn btn-sm btn-danger delete-btn" data-id="{{ $row->id }}"
                                        title="Delete Supplier"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm" method="POST" action="{{ url('/add-supplier') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="name">Name</label>
                                    <input class="form-control" name="name" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="website">Website</label>
                                    <input class="form-control" name="website" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="contact_name">Contact Name</label>
                                    <input class="form-control" name="contact_name" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="phone">Phone</label>
                                    <input class="form-control" name="phone" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="email">Email</label>
                                    <input class="form-control" name="email" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="notes">Notes</label>
                                    <textarea class="form-control" name="notes" type="text" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Order History Modal -->
    <div class="modal fade" id="supplierOrdersModal" tabindex="-1" role="dialog"
        aria-labelledby="supplierOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierOrdersModalLabel">Supplier Order History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="supplier-name"></h6>
                    <p>Total Spent: $<span id="total-spent"></span></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Material</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="supplier-order-details">
                            <!-- Order details will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        $(document).ready(function() {

            // Show supplier order history in modal
            $('.supplier-name').on('click', function(event) {
                event.preventDefault();
                var supplierId = $(this).data('id');

                $.ajax({
                    url: '/suppliers/' + supplierId + '/orders',
                    method: 'GET',
                    success: function(response) {
                        var supplier = response.supplier;

                        $('#supplier-name').text(supplier.name);

                        var totalSpent = 0;
                        var orderDetails = '';
                        supplier.orders.forEach(function(order) {
                            order.order_materials.forEach(function(material) {
                                var subtotal = material.unit_price * material
                                    .quantity;
                                totalSpent += subtotal;

                                orderDetails += `
                                    <tr>
                                        <td>${order.id}</td>
                                        <td>${material.material.name}</td>
                                        <td>${material.quantity}</td>
                                        <td>${material.unit_price}</td>
                                        <td>${subtotal.toFixed(2)}</td>
                                    </tr>
                                `;
                            });
                        });

                        $('#total-spent').text(totalSpent.toFixed(2));
                        $('#supplier-order-details').html(orderDetails);
                        $('#supplierOrdersModal').modal('show');
                    }
                });
            });

            // Handle Edit button click
            $('.edit-btn').on('click', function() {
                var supplierId = $(this).data('id');

                $.get('/suppliers/' + supplierId + '/edit', function(data) {
                    $('#addSupplierModalLabel').text('Edit Supplier');
                    $('#supplierForm').attr('action', '/suppliers/' + supplierId);
                    $('#supplierForm').append('<input type="hidden" name="_method" value="PUT">');

                    $('#supplierForm input[name="name"]').val(data.name);
                    $('#supplierForm input[name="website"]').val(data.website);
                    $('#supplierForm input[name="contact_name"]').val(data.contact_name);
                    $('#supplierForm input[name="phone"]').val(data.phone);
                    $('#supplierForm input[name="email"]').val(data.email);
                    $('#supplierForm textarea[name="notes"]').val(data.notes);

                    $('#addSupplierModal').modal('show');
                });
            });

            // Reset modal when hidden
            $('#addSupplierModal').on('hidden.bs.modal', function() {
                $('#addSupplierModalLabel').text('New Supplier');
                $('#supplierForm').attr('action', '{{ url('/add-supplier') }}');
                $('#supplierForm').trigger('reset');
                $('#supplierForm input[name="_method"]').remove();
            });

            // Handle Delete button click
            $('.delete-btn').on('click', function() {
                var supplierId = $(this).data('id');

                if (confirm('Are you sure you want to delete this supplier?')) {
                    $.ajax({
                        url: '/suppliers/' + supplierId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
