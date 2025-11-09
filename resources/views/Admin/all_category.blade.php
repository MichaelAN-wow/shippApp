@extends('layouts.admin_master')
@section('content')
<style>
#add-material {
    color: #007bff;
    /* Bootstrap primary color */
    background-color: transparent !important;
    /* Override any background color */
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Categories
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-controls">
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
                    data-target="#addCustomerModal">
                    <i class="fas fa-plus"></i> Add Category
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
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ ucfirst($row->type) }}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" data-toggle="modal" data-target="#editCategoryModal"
                                data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-type="{{ $row->type }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn delete-category" data-id="{{ $row->id }}" data-type="{{ $row->type }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/categories/add') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="name">Category Name</label>
                                <input class="form-control" name="name" type="text" placeholder="" required />
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="type">Category Type</label>
                                <select class="form-control" name="type" required>
                                    <option value="product">Product</option>
                                    <option value="material">Material</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/categories/update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="edit-category-id">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="edit-name">Category Name</label>
                                <input class="form-control" name="name" id="edit-name" type="text" required />
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="edit-type">Category Type</label>
                                <select class="form-control" name="type" id="edit-type" required>
                                    <option value="product">Product</option>
                                    <option value="material">Material</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
    crossorigin="anonymous" />

<script>
$('#editCategoryModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var name = button.data('name');
    var type = button.data('type');

    var modal = $(this);
    modal.find('.modal-body #edit-category-id').val(id);
    modal.find('.modal-body #edit-name').val(name);
    modal.find('.modal-body #edit-type').val(type);
});

$(document).ready(function() {
    $(document).on('click', '.delete-category', function() {
        var categoryId = $(this).data('id');
        var categoryType = $(this).data('type');
        var token = $('meta[name="csrf-token"]').attr('content');

        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: '/categories/' + categoryId,
                type: 'DELETE',
                data: {
                    '_token': token,
                    'type': categoryType
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success);
                        location.reload(); // Reload the page to reflect the changes
                    } else {
                        toastr.error('Failed to delete the category.');
                    }
                },
                error: function(response) {
                    toastr.error('An error occurred while deleting the category.');
                }
            });
        }
    });
});
</script>
@endsection