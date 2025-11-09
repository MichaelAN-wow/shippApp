@extends('layouts.admin_master')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Materials
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-title">
            @php
            $selectedCategory = $categories->firstWhere('id', $categoryId);
            @endphp
            {{ $selectedCategory ? $selectedCategory->name : 'All Materials' }}
        </div>
        <div class="card-dashboard-sub-header-controls">
            <div class="float-left search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search Materials" id="customSearchInput">
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#addMaterialModal"
                    style="margin-right: 20px;">
                    <i class="fas fa-plus"></i> Add Material
                </button>
                <button type="button" class="btn btn-sm btn-white" data-toggle="modal" data-target="#importCSVModal"
                    style="margin-right: 20px;">
                    <i class="fas fa-file-import"></i> Import CSV
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <!-- <div class="scrollbar-wrapper">
                <div class="scrollbar-top"></div>
            </div> -->
            <div class="table-wrapper">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th >Name</th>
                            <th>Stock Level</th>
                            <th>Min Level</th>
                            <th>Unit Cost</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Category</th>
                            <th>Last Ordered</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="post-data">
                        @include('Admin.materials_table')
                    </tbody>
                </table>
            </div>
            <div class="ajax-load text-center" style="display:none">
                <span class="spinner-border text-primary" role="status"></span>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addMaterialModal" tabindex="-1" role="dialog" aria-labelledby="addMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaterialModalLabel">Add New Material</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your form content goes here -->
                    <form method="POST" action="{{ url('/materials/add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Name</label>
                                    <input class="form-control" name="name" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Material Type</label>
                                    <select class="form-control" name="material_type" id="material_type">
                                        <option selected value="supplied">Sourced from Supplier</option>
                                        <option value="batch">Made by Me</option>
                                        <option value="infinite">Fixed cost, untracked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Current Stock Level</label>
                                    <input class="form-control" name="current_stock_level" type="text" placeholder=""
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Stock Unit Type</label>

                                    <select class="form-control" name="unit">
                                        @foreach ($units as $type => $group)
                                        <optgroup label="{{ $type }}">
                                            @foreach ($group as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Minimum Stock Level</label>
                                    <input class="form-control" name="min_stock_level" type="text" placeholder=""
                                        value=0 />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Cost per unit($)</label>
                                    <input class="form-control" name="cost_per_unit" type="text" placeholder="" value=0
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplierSelect" multiple>
                                        <option value="">Choose Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Category</label>
                                    <select class="form-control" name="category_id" id="categorySelect" multiple>
                                        <option value="">Choose Category Name</option>
                                        @foreach ($categories as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Last Order Date</label>
                                    <input class="form-control" name="last_order_date" type="date" placeholder=""
                                        value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Material Code</label>
                                    <input class="form-control" name="material_code" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Notes</label>
                                    <input class="form-control" name="notes" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-check" style="margin-top: 30px;">
                                    <input type="checkbox" class="form-check-input" id="isMaterialBase" name="isMaterialBase">
                                    <label class="form-check-label" for="materialBase">Material Base</label>
                                </div>
                            </div>
                            <div class="col-md-12" id="add_material_section" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="batch_size">Select Material</label>
                                        <select class="form-control" name="materials">
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="batch_size" style="visibility: hidden">*</label>
                                        <button type="button" id="add-material" class="btn btn-primary w-100">
                                            <i class="fas fa-plus"></i> Add Material
                                        </button>
                                    </div>
                                </div>

                                <div id="materials-container">
                                    <!-- Material row template -->
                                </div>
                                <div class="col-md-6 offset-6" style="margin-top: 20px">
                                    <div class="form-group row">
                                        <label class="col-3" for="inputTotal"><b>Total Cost</b></label>
                                        <div class="input-group col-9">
                                            <input class="form-control" id="inputTotal" name="total" type="text" placeholder="0" aria-label="Discounts" step="any">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3" for="inputTotalWeight"><b>Total Weight</b></label>
                                        <div class="input-group col-9">
                                            <input class="form-control" id="inputTotalWeight" name="total_weight" type="number" step="0.01" placeholder="0">
                                            <span class="input-group-text unit-label" style="height: 38px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="photo">Photo</label>
                                    <input class="form-control-file" id="photo" name="photo" type="file" accept="image/*" onchange="previewPhoto();" />
                                </div>
                                <div id="photo-preview-container" style="display:none; margin-top: 10px; align-items: center;">
                                    <img id="photoPreview" src="#" alt="Photo Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;" />
                                    <div style="margin-left: 15px; display: flex; flex-direction: column;">
                                        <span id="photo-name"></span>
                                        <span id="photo-info"></span>
                                    </div>
                                    <div style="margin-left: auto; display: flex; flex-direction: column;">
                                        <button id="edit-button" onclick="editPhoto();" style="background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button id="delete-button" onclick="deletePhoto();" style="background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

    <div class="modal fade" id="editMaterialModal" tabindex="-1" role="dialog" aria-labelledby="editMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMaterialModalLabel">Edit Material</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your form content goes here -->
                    <form method="PUT" action="{{ url('/materials/update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Name</label>
                                    <input class="form-control" name="name" type="text" placeholder="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Material Type</label>
                                    <select class="form-control" name="material_type" id="material_type">
                                        <option selected value="supplied">Sourced from Supplier</option>
                                        <option value="batch">Made by Me</option>
                                        <option value="infinite">Fixed cost, untracked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Current Stock Level</label>
                                    <input class="form-control" name="current_stock_level" type="text" placeholder=""
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Stock Unit Type</label>

                                    <select class="form-control" name="unit">
                                        @foreach ($units as $type => $group)
                                        <optgroup label="{{ $type }}">
                                            @foreach ($group as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Minimum Stock Level</label>
                                    <input class="form-control" name="min_stock_level" type="text" placeholder=""
                                        value=0 />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Cost per unit($)</label>
                                    <input class="form-control" name="cost_per_unit" type="text" placeholder="" value=0
                                        required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplierSelect_edit" multiple>
                                        <option value="">Choose Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Category</label>
                                    <select class="form-control" name="category_id" id="categorySelect_edit" multiple>
                                        <option value="">Choose Category Name</option>
                                        @foreach ($categories as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Last Order Date</label>
                                    <input class="form-control" name="last_order_date" type="date" placeholder=""
                                        value="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Material Code</label>
                                    <input class="form-control" name="material_code" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Notes</label>
                                    <input class="form-control" name="notes" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-check" style="margin-top: 30px;">
                                    <input type="checkbox" class="form-check-input" id="isMaterialBase_edit" name="isMaterialBase">
                                    <label class="form-check-label" for="materialBase">Material Base</label>
                                </div>
                            </div>
                            <div class="col-md-12" id="edit_material_section" style="display: none;">
                                Production
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="batch_size">Select Material</label>
                                        <select class="form-control" name="materials">
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="batch_size" style="visibility: hidden">*</label>
                                        <button type="button" id="add-material-edit" class="btn btn-primary w-100">
                                            <i class="fas fa-plus"></i> Add Material
                                        </button>
                                    </div>
                                </div>

                                <div id="materials-container-edit">
                                    <!-- Material row template -->
                                </div>
                                <div class="col-md-6 offset-6" style="margin-top: 20px">
                                    <div class="form-group row">
                                        <label class="col-3" for="inputTotal"><b>Total Cost</b></label>
                                        <div class="input-group col-9">
                                            <input class="form-control" id="inputTotal-edit" name="total" type="text" placeholder="0" aria-label="Discounts" step="any">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3" for="inputTotalWeight"><b>Total Weight</b></label>
                                        <div class="input-group col-9">
                                            <input class="form-control" id="inputTotalWeight-edit" name="total_weight" type="number" step="0.01" placeholder="0">
                                            <span class="input-group-text unit-label" style="height: 38px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="photo">Photo</label>
                                    <input class="form-control-file" id="photo" name="photo" type="file" accept="image/*" onchange="previewPhoto();" />
                                </div>
                                <div id="photo-preview-container" style="display:none; margin-top: 10px; align-items: center;">
                                    <img id="editPhotoPreview" src="#" alt="Photo Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;" />
                                    <div style="margin-left: 15px; display: flex; flex-direction: column;">
                                        <span id="photo-name"></span>
                                        <span id="photo-info"></span>
                                    </div>
                                    <div style="margin-left: auto; display: flex; flex-direction: column;">
                                        <button id="edit-button" onclick="editPhoto();" style="background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button id="delete-button" onclick="deletePhoto();" style="background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

    <div class="modal fade" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importCSVModalLabel">Import CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('materials/upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="csv_file" accept=".csv">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('script')
<script>
var page = 1;
var isScrollingBlocked = false; // Flag to track scrolling state

$(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
        page++;
        loadMoreData(page);
    }
});

var categoryId = {{ $categoryId }};

var unitsData = JSON.parse('@json($unitsJson)');
var materialsData = JSON.parse('@json($all_materials)');   

// Function to get unit name and conversion factor by ID
function getUnitById(id) {
    for (var type in unitsData) {
        var units = unitsData[type];
        for (var i = 0; i < units.length; i++) {
            if (units[i].id == id) {
                return {
                    name: units[i].name,
                    conversion_factor: units[i].conversion_factor
                };
            }
        }
    }
    return null; // Return null if unit not found
}

function loadMoreData(page) {
    if (isScrollingBlocked) {
        return; // Exit if scrolling is blocked
    }
    $.ajax({
            url: '/materials/pageScroll?page=' + page + "&categoryId=" + categoryId,
            type: "get",
            beforeSend: function() {
                $('.ajax-load').show();
            }
        })
        .done(function(data) {
            if (data == " ") {
                $('.ajax-load').html("No more records found");
                return;
            }
            $('.ajax-load').hide();
            // Append the new posts at the bottom of the post data container
            $("#post-data").append(data);
        })
        .fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr.error('Server is not responding...');
        });
}

document.body.addEventListener('click', function(event) {
    var target = event.target;
    while (target != document.body) {
        if (target.classList.contains('delete-btn')) {
            var materialId = target.dataset.id;

            if (confirm('Are you sure you want to delete this material?')) {
                // Perform an AJAX request to delete the material
                fetch('/materials/delete/' + materialId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the material row from the table
                            event.target.closest('tr').remove();
                            toastr.success('Success');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
            break;
        }
        target = target.parentNode;
    }
});


document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(event) {
        var target = event.target;
        while (target != document.body) {
            if (target.classList.contains('edit-btn')) {
                var materialId = target.getAttribute('data-id');
                // Fetch the material data from the server
                fetch('/materials/get/' + materialId)
                    .then(response => response.json())
                    .then(data => {
                        var modalBody = document.querySelector('#editMaterialModal .modal-body');
                        var form = modalBody.querySelector('form');
                        var methodInput = form.querySelector('input[name="_method"]');
                        if (!methodInput) {
                            methodInput = document.createElement('input');
                            methodInput.setAttribute('type', 'hidden');
                            methodInput.setAttribute('name', '_method');
                            form.appendChild(methodInput);
                        }
                        methodInput.setAttribute('value', 'POST');
                        form.action = '/materials/update/' + data.id;
                        form.method = 'POST';
                        form.setAttribute('data-id', data.id);
                        form.querySelector('[name="name"]').value = data.name;
                        form.querySelector('[name="material_type"]').value = data.material_type;
                        form.querySelector('[name="unit"]').value = data.unit_id;
                        
                        // Set the unit label for total weight
                        const mainUnitSelect = form.querySelector('[name="unit"]');
                        const mainUnitOption = mainUnitSelect.options[mainUnitSelect.selectedIndex];
                        const mainUnitName = mainUnitOption.text;
                        const unitLabel = form.querySelector('.unit-label');
                        if (unitLabel) {
                            unitLabel.textContent = mainUnitName;
                        }
                        
                        form.querySelector('[name="current_stock_level"]').value = parseToDecimal(data.current_stock_level);
                        form.querySelector('[name="min_stock_level"]').value = parseToDecimal(data.min_stock_level) || 0;
                        form.querySelector('[name="cost_per_unit"]').value = parseToDecimal(data.price_per_unit) || 0;
                        form.querySelector('[name="material_code"]').value = data.material_code;
                        form.querySelector('[name="notes"]').value = data.notes || '';
                        form.querySelector('[name="last_order_date"]').value = data.last_order_date.split(' ')[0];

                        // Set total weight if it exists
                        const totalWeightInput = form.querySelector('[name="total_weight"]');
                        if (totalWeightInput && data.total_weight) {
                            totalWeightInput.value = parseToDecimal(data.total_weight);
                        }

                        // Handle material base checkbox
                        var isMaterialBaseCheckbox = form.querySelector('[name="isMaterialBase"]');
                        isMaterialBaseCheckbox.checked = data.material_base || false;
                        
                        // Trigger change event to show/hide sections
                        var event = new Event('change');
                        isMaterialBaseCheckbox.dispatchEvent(event);

                        // If it's a material base, populate the materials
                        if (data.material_base && data.material_materials) {
                            var materialsContainer = document.getElementById('materials-container-edit');
                            materialsContainer.innerHTML = ''; // Clear existing materials

                            data.material_materials.forEach(material => {
                                if (!material.material) return;

                                var newRow = document.createElement('div');
                                var selectedMaterial = materialsData.find(_material => _material.id == material.material_id);

                                
                                newRow.className = 'row material-row mt-3';
                                newRow.innerHTML = `
                                    <div class="col-md-3">
                                        <label>Name</label>
                                        <input class="form-control unit-input" name="material_id[]" type="text" value="${material.material_id}" hidden />
                                        <input class="form-control unit-input" name="material_name" type="text" value="${material.material.name}" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label>Unit Price($)</label>
                                        <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(material.material.price_per_unit)}/${material.material.unit.name}" readonly />
                                        <input type="hidden" name="raw_unit_price[]" value="${material.material.price_per_unit}" />
                                    </div>
                                    <div class="col-md-2">
                                        <label>Quantity</label>
                                        <input class="form-control" name="material_count[]" type="number" value="${parseToDecimal(material.used_amount)}" step="0.01" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label>Unit Type</label>
                                        <select class="form-control unit-input" name="unit"></select>
                                        <input class="form-control" name="material_unit_id[]" type="hidden" value="${material.unit_id}" />
                                    </div>
                                    <div class="col-md-2">
                                        <label>Subtotal($)</label>
                                        <input class="form-control unit-input" name="sub_total[]" type="text" value="${parseToDecimal(material.unit_price * material.used_amount)}" readonly />
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                                        <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
                                    </div>
                                `;

                                materialsContainer.appendChild(newRow);

                                var unitsSelect = newRow.querySelector('select[name="unit"]');
                                populateUnitsSelect(unitsSelect, material.material, material.unit_id);

                                // Remove the material from the select options
                                var selectElement = document.querySelector('select[name="materials"]');
                                for (var i = 0; i < selectElement.options.length; i++) {
                                    if (selectElement.options[i].value == material.material_id) {
                                        selectElement.remove(i);
                                        break;
                                    }
                                }

                                // Add event listeners for calculations
                                const quantityInput = newRow.querySelector('[name="material_count[]"]');
                                const unitPriceInput = newRow.querySelector('[name="unit_price[]"]');
                                const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
                                var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');

                                const originalUnit = material.material.unit.name;
                                const originalConversionFactor = originalUnit === 'pieces' ? 1 : material.material.unit.conversion_factor;

                                function calculateSubtotal() {
                                    unitIdInput.value = unitsSelect.value;
                                    const quantity = parseFloat(quantityInput.value);
                                    const unitPrice = parseFloat(unitPriceInput.value.replace(/[^\d.-]/g, ''));
                                    const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
                                    const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) || 1;
                                    const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice * selectedConversionFactor / originalConversionFactor);
                                    subtotalInput.value = new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD'
                                    }).format(subtotal);

                                    calculateTotal();
                                }

                                calculateSubtotal();

                                quantityInput.addEventListener('input', calculateSubtotal);
                                unitsSelect.addEventListener('change', calculateSubtotal);
                            });

                            calculateTotal();
                        }

                        var selectSupplierElement = $('#supplierSelect_edit');
                        if (selectSupplierElement.length) {
                            var supplierId = data.supplier_id;
                            selectSupplierElement.val(supplierId).trigger('change');
                        }

                        var selectCategoryElement = $('#categorySelect_edit');
                        if (selectCategoryElement.length) {
                            var categoryId = data.category_id;
                        selectCategoryElement.val(categoryId).trigger('change');
                        }

                        var photoPath = data.photo_path ? '{{ asset('storage') }}/' + data.photo_path: '';
                        var photoPreview = document.getElementById('editPhotoPreview');
                        if (photoPath) {
                            photoPreview.src = photoPath;
                            photoPreview.style.display = 'block';
                        } else {
                            photoPreview.style.display = 'none';
                        }

                        $('#editMaterialModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                break;
            }
            target = target.parentNode;
        }
    });
});

function calculateTotal() {
    // Get the current active modal
    const activeModal = document.querySelector('.modal.show');
    if (!activeModal) return;

    // Get the appropriate total input based on which modal is active
    const totalInput = activeModal.id === 'addMaterialModal' ? 
        document.getElementById('inputTotal') : 
        document.getElementById('inputTotal-edit');

    if (!totalInput) return;

    // Get all subtotal inputs in the active modal
    const subtotals = activeModal.querySelectorAll('[name="sub_total[]"]');
    let total = 0;

    subtotals.forEach(input => {
        const value = parseFloat(input.value.replace(/[^\d.-]/g, ''));
        total += isNaN(value) ? 0 : value;
    });

    totalInput.value = parseToDecimal(total.toFixed(3));
}

// Add event listener for main unit change to update unit label
document.querySelector('#editMaterialModal select[name="unit"]').addEventListener('change', function() {
    const mainUnitSelect = this;
    const mainUnitOption = mainUnitSelect.options[mainUnitSelect.selectedIndex];
    const mainUnitName = mainUnitOption.text;
    
    // Update the unit label
    const unitLabel = document.querySelector('#inputTotalWeight-edit').parentElement.querySelector('.unit-label');
    unitLabel.textContent = mainUnitName;
});

// Add event listener for edit material section
document.getElementById('materials-container-edit').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-material-row')) {
        var row = e.target.closest('.material-row');
        var materialId = row.querySelector('input[name="material_id[]"]').value;
        var materialName = row.querySelector('input[name="material_name"]').value;
        
        // Add the material back to the select dropdown
        var selectElement = document.querySelector('#editMaterialModal select[name="materials"]');
        var option = document.createElement('option');
        option.value = materialId;
        option.text = materialName;
        selectElement.appendChild(option);
        
        // Remove the row
        row.remove();
        calculateTotal();
    }
});

 // Function to populate units select dropdown
 function populateUnitsSelect(selectElement, selectedMaterial, unitId) {
    selectElement.innerHTML = '';
    var unitsGroup = unitsData[selectedMaterial.unit.type];
    unitsGroup.forEach(unit => {
        var option = document.createElement('option');
        option.value = unit.id;
        option.text = unit.name;
        if (unit.id === unitId) {
            option.selected = true;
        }
        option.dataset.conversionFactor = unit.conversion_factor;
        selectElement.appendChild(option);
    });
}


// Add event listener for Add Material button in edit modal
document.getElementById('add-material-edit').addEventListener('click', function() {
    var selectElement = document.querySelector('#editMaterialModal select[name="materials"]');
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var selectedId = selectedOption.value;

    var container = document.getElementById('materials-container-edit');
    var newRow = document.createElement('div');

    var selectedMaterial = materialsData.find(material => material.id == selectedId);
    newRow.className = 'row material-row mt-3';
    newRow.innerHTML = `
        <div class="col-md-3">
            <label>Name</label>
            <input class="form-control unit-input" name="material_id[]" type="text" value="${selectedMaterial.id}" hidden />
            <input class="form-control unit-input" name="material_name" type="text" value="${selectedMaterial.name}" readonly />
        </div>
        <div class="col-md-2">
            <label>Unit Price($)</label>
            <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(selectedMaterial.price_per_unit)}/${selectedMaterial.unit.name}" readonly />
        </div>
        <div class="col-md-2">
            <label>Quantity</label>
            <input class="form-control" name="material_count[]" type="number" value="1" step="0.01" required />
        </div>
        <div class="col-md-2">
            <label>Unit Type</label>
            <select class="form-control unit-input" name="unit"></select>
            <input class="form-control" name="material_unit_id[]" type="hidden" value="${selectedMaterial.unit.id}" />
        </div>
        <div class="col-md-2">
            <label>Subtotal($)</label>
            <input class="form-control unit-input" name="sub_total[]" type="text" value="${parseToDecimal(selectedMaterial.price_per_unit)}" readonly />
        </div>
        <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
            <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
        </div>
    `;

    container.appendChild(newRow);

    var unitsSelect = newRow.querySelector('select[name="unit"]');
    var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');

    populateUnitsSelect(unitsSelect, selectedMaterial, selectedMaterial.id);

    // Remove the selected option from the select element
    selectElement.removeChild(selectedOption);

    // Get the quantity input, unit price, and subtotal input of the newly added row
    const quantityInput = newRow.querySelector('[name="material_count[]"]');
    const unitPriceInput = newRow.querySelector('[name="unit_price[]"]');
    const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
    const originalUnit = selectedMaterial.unit.name;
    const originalConversionFactor = originalUnit === 'pieces' ? 1 : selectedMaterial.unit.conversion_factor;

    function calculateSubtotal() {
        unitIdInput.value = unitsSelect.value;
        const quantity = parseFloat(quantityInput.value);
        const unitPrice = parseFloat(unitPriceInput.value.replace(/[^\d.-]/g, ''));
        const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
        const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) || 1;
        const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice * selectedConversionFactor / originalConversionFactor);
        subtotalInput.value = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(subtotal);

        calculateTotal();
    }

    // Add event listener to quantity input
    quantityInput.addEventListener('input', calculateSubtotal);

    // Add event listener to unit price input
    unitPriceInput.addEventListener('input', calculateSubtotal);

    // Add event listener to unit select
    unitsSelect.addEventListener('change', calculateSubtotal);

    calculateTotal();
});

$(document).ready(function() {
    $('#supplierSelect').select2({
        tags: true,
        placeholder: 'Type supplier name...',
        width: '100%',
        insertTag: function(data, tag) {
            tag.text = 'Create "' + tag.text + '"';
            // Insert the tag at the end of the results
            data.push(tag);
        }
    });
    $('#categorySelect').select2({
        tags: true,
        placeholder: 'Type category name...',
        width: '100%',
        insertTag: function(data, tag) {
            tag.text = 'Create "' + tag.text + '"';
            // Insert the tag at the end of the results
            data.push(tag);
        }
    });
    $('#supplierSelect_edit').select2({
        tags: true,
        placeholder: 'Type supplier name...',
        width: '100%',
        insertTag: function(data, tag) {
            tag.text = 'Create "' + tag.text + '"';
            // Insert the tag at the end of the results
            data.push(tag);
        }
    });
    $('#categorySelect_edit').select2({
        tags: true,
        placeholder: 'Type category name...',
        width: '100%',
        insertTag: function(data, tag) {
            tag.text = 'Create "' + tag.text + '"';
            // Insert the tag at the end of the results
            data.push(tag);
        }
    });

    var events_table = $('#dataTable').DataTable({
        "serverSide": true,
        "mark": true,
        "ordering": false,
        "searching": true,
        "ajax": function(data, callback, settings) {
            $.ajax({
                url: '/materials/pageScroll',
                type: "GET",
                data: {
                    categoryId: categoryId,
                    search: data.search.value
                },
                beforeSend: function() {
                    $('.ajax-load').show();

                },
                success: function(response) {
                    $('.ajax-load').hide();
                    $("#post-data").empty(); // Clear the table body
                    $("#post-data").html(response); // Replace with new data

                    isScrollingBlocked = !!data.search.value; // Updated this line
                    page = 1;
                },
                error: function(jqXHR, ajaxOptions, thrownError) {
                    $('.ajax-load').hide(); // Hide loading indicator on error
                    toastr.error('Server is not responding...');
                }
            });
        },
    });

    $('#customSearchInput').on('keyup', function() {
        events_table.search(this.value).draw();
});


document.getElementById('editMaterialModal').querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault();
    var form = event.target;
    var formData = new FormData(form);
    
    // Ensure unit value is included
    const unitSelect = form.querySelector('[name="unit"]');
    formData.set('unit', unitSelect.value);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        } else {
            throw new Error('Server response was not JSON');
        }
    })
    .then(data => {
        if (data.success) {
            var material = data.material;
            var materialRow = document.querySelector(`tr[data-id='${material.id}']`);
            var materialNameCell = materialRow.querySelector('.material-name');
            materialNameCell.innerHTML = material.name;
            if (material.photo_path) {
                materialNameCell.innerHTML +=
                    `<img src="{{ asset('storage') }}/${material.photo_path}" alt="Material Photo" width="28" height="28" style="float: right; margin-left: 10px;">`;
            }
            const unit = getUnitById(material.unit_id);
            materialRow.children[1].textContent = `${material.current_stock_level} ${unit.name}`;

            // Update minimum stock level
            materialRow.children[2].textContent = material.min_stock_level == null ? '-' :
                `${material.min_stock_level} ${unit.name}`;

            // Update price per unit
            materialRow.children[3].textContent =
                `$${parseFloat(material.price_per_unit).toFixed(2)}/${unit.name}`;

            // Update material code
            materialRow.children[4].textContent = material.material_code || '';

            // Update supplier name
            materialRow.children[5].textContent = material.supplier ? material.supplier.name : '';

            // Update category name
            materialRow.children[6].textContent = material.category ? material.category.name : '';

            // Update last order date
            var updatedDate = new Date(material.last_order_date);
            var formattedDate = updatedDate.toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            materialRow.children[7].textContent = formattedDate;

            // Update notes
            materialRow.children[8].textContent = material.notes || '';
            $('#editMaterialModal').modal('hide');
            // Show success message
            toastr.success('Material updated successfully!');
        } else {
            toastr.error('Failed to update product. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred. Please try again.');
    });
});

function previewPhoto() {
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoName = document.getElementById('photo-name');
    const photoInfo = document.getElementById('photo-info');

    const file = photoInput.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            // Hide the file input and show the preview container
            photoInput.style.display = 'none';
            photoPreviewContainer.style.display = 'flex';

            // Set the preview image source
            photoPreview.src = e.target.result;
            
            // Set the file name and info
            photoName.textContent = file.name;
            photoInfo.textContent = `${file.width}x${file.height} ~ ${(file.size / 1024).toFixed(2)}KB`;
        }

        reader.readAsDataURL(file);
    }
}

function editPhoto() {
    const photoInput = document.getElementById('photo');

    // Trigger the click event on the file input to open the file dialog
    photoInput.click();
}

function deletePhoto() {
    const photoInput = document.getElementById('photo');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreview = document.getElementById('photoPreview');
    
    // Hide the preview container and show the file input
    photoPreviewContainer.style.display = 'none';
    photoInput.style.display = 'block';

    // Clear the file input and preview image
    photoInput.value = '';
    photoPreview.src = '#';
    document.getElementById('photo-name').textContent = '';
    document.getElementById('photo-info').textContent = '';
}

function previewEditPhoto() {
    const photoInput = document.getElementById('photo_edit');
    const photoPreview = document.getElementById('editPhotoPreview');

    const file = photoInput.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            photoPreview.src = e.target.result;
            photoPreview.style.display = 'block';
        }

        reader.readAsDataURL(file);
    } else {
        photoPreview.src = '#';
        photoPreview.style.display = 'none';
    }
}

   
    // Add material base functionality
    document.getElementById('isMaterialBase').addEventListener('change', function() {
        var materialSection = document.getElementById('add_material_section');
        var costSection = document.getElementById('add_cost_section');
        var supplierSection = document.getElementById('add_supplier_section');
        var costPerUnitInput = document.querySelector('#addMaterialModal input[name="cost_per_unit"]');
        var totalWeightInput = document.getElementById('inputTotalWeight');

        if (this.checked) {
            materialSection.style.display = 'block';
            costSection.style.display = 'none';
            supplierSection.style.display = 'none';
            costPerUnitInput.disabled = true;
            costPerUnitInput.value = '0';
            if (totalWeightInput) totalWeightInput.required = true;
        } else {
            materialSection.style.display = 'none';
            costSection.style.display = 'block';
            supplierSection.style.display = 'block';
            costPerUnitInput.disabled = false;
            if (totalWeightInput) {
                totalWeightInput.required = false;
                totalWeightInput.value = '';
            }
        }
    });

    document.getElementById('isMaterialBase_edit').addEventListener('change', function() {
        var materialSection = document.getElementById('edit_material_section');
        var costSection = document.getElementById('edit_cost_section');
        var supplierSection = document.getElementById('edit_supplier_section');
        var costPerUnitInput = document.querySelector('#editMaterialModal input[name="cost_per_unit"]');
        var totalWeightInput = document.querySelector('#inputTotalWeight-edit');

        if (this.checked) {
            materialSection.style.display = 'block';
            costSection.style.display = 'none';
            supplierSection.style.display = 'none';
            costPerUnitInput.disabled = true;
            costPerUnitInput.value = '0';
            totalWeightInput.required = true;
        } else {
            materialSection.style.display = 'none';
            costSection.style.display = 'block';
            supplierSection.style.display = 'block';
            costPerUnitInput.disabled = false;
            totalWeightInput.required = false;
            totalWeightInput.value = '';
        }
    });

    // Function to add a new material row
    document.getElementById('add-material').addEventListener('click', function() {
        var selectElement = document.querySelector('select[name="materials"]');
        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var selectedId = selectedOption.value;

        var container = document.getElementById('materials-container');
        var newRow = document.createElement('div');

        var selectedMaterial = materialsData.find(material => material.id == selectedId);
        newRow.className = 'row material-row mt-3';
        newRow.innerHTML = `
            <div class="col-md-3">
                <label>Name</label>
                <input class="form-control unit-input" name="material_id[]" type="text" value="${selectedMaterial.id}" hidden />
                <input class="form-control unit-input" name="material_name" type="text" value="${selectedMaterial.name}" readonly />
            </div>
            <div class="col-md-2">
                <label>Unit Price($)</label>
                <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(selectedMaterial.price_per_unit)}/${selectedMaterial.unit.name}" readonly />
            </div>
            <div class="col-md-2">
                <label>Quantity</label>
                <input class="form-control" name="material_count[]" type="number" value="1" step="0.01" required />
            </div>
            <div class="col-md-2">
                <label>Unit Type</label>
                <select class="form-control unit-input" name="unit"></select>
                <input class="form-control" name="material_unit_id[]" type="hidden" value="${selectedMaterial.unit.id}" />
            </div>
            <div class="col-md-2">
                <label>Subtotal($)</label>
                <input class="form-control unit-input" name="sub_total[]" type="text" value="${parseToDecimal(selectedMaterial.price_per_unit)}" readonly />
            </div>
            <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
            </div>
        `;

        container.appendChild(newRow);

        var unitsSelect = newRow.querySelector('select[name="unit"]');
        var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');

        populateUnitsSelect(unitsSelect, selectedMaterial, selectedMaterial.id);

        // Remove the selected option from the select element
        selectElement.removeChild(selectedOption);

        // Get the quantity input, unit price, and subtotal input of the newly added row
        const quantityInput = newRow.querySelector('[name="material_count[]"]');
        const unitPriceInput = newRow.querySelector('[name="unit_price[]"]');
        const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
        const originalUnit = selectedMaterial.unit.name;
        const originalConversionFactor = originalUnit === 'pieces' ? 1 : selectedMaterial.unit.conversion_factor;

        function calculateSubtotal() {
            unitIdInput.value = unitsSelect.value;
            const quantity = parseFloat(quantityInput.value);
            const unitPrice = parseFloat(unitPriceInput.value.replace(/[^\d.-]/g, ''));
            const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
            const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) || 1;
            const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice * selectedConversionFactor / originalConversionFactor);
            subtotalInput.value = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(subtotal);

            calculateTotal();
        }

        // Add event listener to quantity input
        quantityInput.addEventListener('input', calculateSubtotal);

        // Add event listener to unit price input
        unitPriceInput.addEventListener('input', calculateSubtotal);

        // Add event listener to unit select
        unitsSelect.addEventListener('change', calculateSubtotal);

        calculateTotal();
    });

    // Function to remove a material row
    document.getElementById('materials-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-material-row')) {
            var row = e.target.closest('.material-row');
            var materialId = row.querySelector('input[name="material_id[]"]').value;
            var materialName = row.querySelector('input[name="material_name"]').value;
            
            // Add the material back to the select dropdown
            var selectElement = document.querySelector('select[name="materials"]');
            var option = document.createElement('option');
            option.value = materialId;
            option.text = materialName;
            selectElement.appendChild(option);
            
            // Remove the row
            row.remove();
            calculateTotal();
        }
    });

    // Reset modal to initial state when it is hidden
    $('#editMaterialModal').on('hidden.bs.modal', function() {
        var form = this.querySelector('form');
        form.reset();
        $('#supplierSelect_edit').val('').trigger('change');
        $('#categorySelect_edit').val('').trigger('change');
        
        // Clear materials container
        var materialsContainer = document.getElementById('materials-container-edit');
        if (materialsContainer) {
            materialsContainer.innerHTML = '';
        }
        
        // Reset material select
        var selectElement = document.querySelector('#editMaterialModal select[name="materials"]');
        selectElement.innerHTML = '';
        materialsData.forEach(function(material) {
            var option = document.createElement('option');
            option.value = material.id;
            option.text = material.name;
            selectElement.appendChild(option);
        });
        
        // Reset material base section
        document.getElementById('isMaterialBase_edit').checked = false;
        document.getElementById('edit_material_section').style.display = 'none';
        document.getElementById('edit_cost_section').style.display = 'block';
        document.getElementById('edit_supplier_section').style.display = 'block';
    });
});

</script>
@endsection