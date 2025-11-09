@extends('layouts.admin_master')
@section('content')
    <style>
        .shopify-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: black;
            font-size: 16px;
        }

        .shopify-logo {
            width: 28px;
            height: 28px;
            margin-right: 10px;
        }

        .shopify-logo-open {
            width: 16px;
            height: 16px;
            margin-left: 5px;
        }

        #add-material, #add-location {
            color: #007bff;
            /* Bootstrap primary color */
            background-color: transparent !important;
            /* Override any background color */
        }

        .collapsible td{
            cursor: pointer;
            background-color: #ffd700; /* Light grey background */
        }

        a {
            color: black;
        }

        .category-label {
            display: inline-block;
            max-width: 150px;
            /* Adjust this width as needed */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 2px 5px;
            border-radius: 5px;
            font-size: 0.85rem;
            color: #fff;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Products
        </div>
        <div class="card-dashboard-sub-header">
            <div class="card-dashboard-sub-header-title">
                @php
                $selectedCategory = $categories->firstWhere('id', $categoryId);
                @endphp
                {{ $selectedCategory ? $selectedCategory->name : 'All Products' }}
            </div>
            <div class="card-dashboard-sub-header-controls">
                <div class="float-left search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search Products" id="customSearchInput">
                </div>
                <div class="float-right">
                    <button type="button" id="combineVariantsBtn" class="btn btn-sm btn-warning float-right"
                        style="margin-right: 20px; display: none;">
                        <i class="fas fa-layer-group"></i> Combine as Variants
                    </button>   
                    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#addProductModal"
                        style="margin-right: 20px;">
                        <i class="fas fa-plus"></i> Add Product
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
                                <th>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>Name</th>
                                <th>Stock Level</th>
                                <th>Min Level</th>
                                <th>Unit Cost</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="table-data">
                            @include('Admin.products_table')
                        </tbody>
                    </table>
                </div>
                <div class="ajax-load text-center" style="display:none">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
        </div>

        <div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog"
            aria-labelledby="productDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productDetailsModalLabel">Product Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Order details will be loaded here -->
                        <div class="product-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <span id="product-name"></span></p>
                                </div>
                                <div class="col-md-6" id="details_shopify_link">
                                    <a href="https://{{ auth()->user()->company->shopify_domain }}/admin/orders/6070034989143"
                                        target="_blank" rel="nofollow noreferrer" class="shopify-link"
                                        style="float: right;">
                                        <img src="{{ asset('images/shopify.ca02b3a3da85bb570f6786752a5f08fc.svg') }}"
                                            alt="shopify" class="shopify-logo" width="28" height="28">
                                        View on Shopify
                                        <img src="{{ asset('images/open-new.57fc55864a4fad0b716b.svg') }}"
                                            alt="open in new tab" class="shopify-logo-open" width="24" height="24">
                                    </a>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Stock Level:</strong> <span id="product-stock"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Min Level:</strong> <span id="product-min-stock"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Unit Cost:</strong> <span id="product-unit-cost"></span></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Used Amount</th>
                                    <th>Available</th>
                                    <th>Min Level</th>
                                    <th>Unit Cost</th>
                                </tr>
                            </thead>
                            <tbody id="product-details-body">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ url('/products/add') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputFirstName">Name</label>
                                        <input class="form-control" name="name" type="text" placeholder=""
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Product Type</label>
                                        <select class="form-control" name="product_type" id="add_product_type">
                                            <option selected value="1">Sourced from Supplier</option>
                                            <option value="2">Made by Me</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="batch_size">Select Location</label>
                                            <select class="form-control" name="locations">
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="batch_size"
                                            style="visibility: hidden">*</label>
                                            <button type="button" id="add-location" class="btn btn-primary w-100"
                                                style="">
                                                <i class="fas fa-plus"></i> Add Location
                                            </button>
                                        </div>
                                    </div>
                                    <div id="location-container">
                                    <!-- Material row template -->

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Current Stock Level</label>
                                        <input class="form-control" name="current_stock_level" type="text"
                                            placeholder="" pattern="\d+" title="Please enter an integer value."
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Stock Unit Type</label>

                                        <select class="form-control" name="unit_id">
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
                                            pattern="\d+" title="Please enter an integer value." value=0 />
                                    </div>
                                </div>
                                <div class="col-md-12" id="add_product_section" style="display: none;">
                                    Production
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="batch_size">Batch Size</label>
                                            <input class="form-control" id="batch_size" name="batch_size" type="text"
                                                pattern="\d+" title="Please enter an integer value." value="1"
                                                required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
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
                                            <label class="small mb-1" for="batch_size"
                                                style="visibility: hidden">*</label>
                                            <button type="button" id="add-material" class="btn btn-primary w-100"
                                                style="">
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
                                                <input class="form-control" id="inputTotal" name="total"
                                                    type="text" placeholder="0" aria-label="Discounts"
                                                    step="any">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" id="add_cost_section">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Cost per unit($)</label>
                                        <input class="form-control" name="cost_per_unit" type="text" placeholder=""
                                            value=0 required />
                                    </div>
                                </div>

                                <div class="col-md-6" id="add_supplier_section">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Supplier</label>
                                        <select class="form-control" name="supplier_id" id ="supplierSelect" multiple>
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
                                        <select class="form-control" name="category_id" id ="categorySelect" multiple>
                                            <option value="">Choose Category Name</option>
                                            @foreach ($categories as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputLastName">Last Order Date</label>
                                        <input class="form-control" name="last_order_date" type="date" placeholder=""
                                            value="{{ date('Y-m-d') }}" />
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputFirstName">Product Code</label>
                                        <input class="form-control" name="product_code" type="text" placeholder="" />
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
                        <form action="{{ route('products/upload') }}" method="POST" enctype="multipart/form-data">
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

        <div class="modal fade" id="combineVariantsModal" tabindex="-1" role="dialog"
            aria-labelledby="combineVariantsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="combineVariantsModalLabel">Combine as Variants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ url('/products/combine-variants') }}">
                            @csrf
                            <div class="form-group">
                                <label for="variantName">Variant Name</label>
                                <input type="text" class="form-control" id="variantName" name="variant_name"
                                    required>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Stock Level</th>
                                            <th>Unit Cost</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedProductsTable">
                                        <!-- Selected products will be appended here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Combine</button>
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

            var initialSelectHtml = document.querySelector('select[name="locations"]').innerHTML;
            function resetLocationSelect() {
                document.querySelector('select[name="locations"]').innerHTML = initialSelectHtml;
            }
         
            var page = 1;rebindEventListeners
            var searchQuery = '';
            var loading = false; // Add a loading flag

            $(window).scroll(function() {
                if (!loading && $(window).scrollTop() + $(window).height() >= $(document).height()) {
                    page++;
                    loadMoreData(page);
                }
            });

            var categoryId = {{ $categoryId }};

            function loadMoreData(page) {
                loading = true; 
                $.ajax({
                        url: '/products/pageScroll?page=' + page + "&categoryId=" + categoryId  + '&search=' + searchQuery,
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
                        $("#table-data").append(data);
                        rebindEventListeners();
                    })
                    .fail(function(jqXHR, ajaxOptions, thrownError) {
                        toastr.error('Server is not responding...');
                    })
                    .always(function() {
                        loading = false; // Reset loading flag
                    });
            }

            function calculateTotal() {
                const subtotals = document.querySelectorAll('[name="sub_total[]"]');
                let total = 0;
                subtotals.forEach(input => {
                    const value = parseFloat(input.value.replace(/[^\d.-]/g, ''));

                    total += isNaN(value) ? 0 : value;
                });

                document.getElementById('inputTotal').value = parseToDecimal(total.toFixed(3));
            }

            // function applyCategoryColors() {
            //     document.querySelectorAll('.category-label').forEach(function(label) {
            //         var category = label.getAttribute('data-category');
            //         var color = getColorByName(category);
            //         label.style.backgroundColor = color;
            //         label.style.color = '#fff';
            //         label.style.padding = '2px 5px';
            //         label.style.borderRadius = '3px';
            //     });
            // }


            var unitsData = JSON.parse('@json($unitsJson)');
            var materialsData = JSON.parse('@json($materials)'); // Convert materials data to JSON
            var materialBasesData = JSON.parse('@json($material_bases)'); // Convert materials data to JSON

            function getProductById(id) {
                for (const product of materialBasesData) {
                    if (product.id === id) {
                        return product;
                    }
                }
                return null;
            }
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

            document.addEventListener('DOMContentLoaded', function() {

                document.getElementById('add-location').addEventListener('click', function() {
                    var selectElement = document.querySelector('select[name="locations"]');
                    var selectedOption = selectElement.options[selectElement.selectedIndex];
                    var selectedId = selectedOption.value;

                    var container = document.getElementById('location-container');
                    var newRow = document.createElement('div');

                    newRow.className = 'row location-row mt-3';
                    newRow.innerHTML = `
                        <div class="col-md-4">
                            <label>Location</label>
                            <input class="form-control" name="location_id[]" type="text" value="${selectedId}" hidden />
                            <input class="form-control unit-input" name="location_name" type="text" value="${selectedOption.textContent}" readonly />
                        </div>
                        <div class="col-md-3">
                            <label>Current stock level</label>
                            <input class="form-control unit-input" name="location_current_level[]" value=0 type="number"/>
                        </div>
                        <div class="col-md-3">
                            <label>Min stock level</label>
                            <input class="form-control unit-input" name="location_min_level[]" value=0 type="number"/>
                        </div>
                        <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                            <button type="button" class="btn btn-secondary btn-sm remove-location-row">×</button>
                        </div>
                        `;
                    container.appendChild(newRow);
                    selectElement.removeChild(selectedOption);

                    function updateTotalMinStockLevel() {
                        let totalMinStockLevel = 0;

                        // Select all inputs for location min stock level
                        const minStockInputs = document.querySelectorAll('[name="location_min_level[]"]');
                        minStockInputs.forEach(input => {
                            const value = parseFloat(input.value);
                            if (!isNaN(value)) {
                                totalMinStockLevel += value;
                            }
                        });

                        // Update the main min_stock_level input with the computed sum
                        const totalMinStockInput = document.querySelector('[name="min_stock_level"]');
                        if (totalMinStockInput) {
                            totalMinStockInput.value = totalMinStockLevel;
                        }
                    }

                    updateTotalMinStockLevel();

                    // Attach the event listener to the document for dynamic inputs
                    document.addEventListener('input', function (event) {
                        if (event.target.name === 'location_min_level[]') {
                            updateTotalMinStockLevel();
                        }
                    });

                    container.addEventListener('click', function(e) {
                        if (e.target.classList.contains('remove-location-row')) {
                            var row = e.target.closest('.location-row');
                            var locationName = row.querySelector('input[name="location_name"]').value;
                            var locationId = row.querySelector('input[name="location_id[]"]').value;

                            // Create a new option element and reinsert it into the select element
                            var newOption = document.createElement('option');
                            newOption.value = locationId;
                            newOption.textContent = locationName;
                            selectElement.appendChild(newOption);

                            // Remove the row from the container
                            row.remove();
                        }
                    }, { once: true });
                });

                
                // Function to add a new material row
                document.getElementById('add-material').addEventListener('click', function() {
                    var selectElement = document.querySelector('select[name="materials"]');
                    var selectedOption = selectElement.options[selectElement.selectedIndex];
                    var selectedId = selectedOption.value;

                    var container = document.getElementById('materials-container');
                    var newRow = document.createElement('div');

                    var selectedMaterial = materialsData.find(material => material.id == selectedId);
                    const isMaterialBase = selectedMaterial.isMaterialBase !== undefined ? selectedMaterial.isMaterialBase : false;
                    newRow.className = 'row material-row mt-3';
                    newRow.innerHTML = `
                        <div class="col-md-3">
                            <label>Material</label>
                            <input class="form-control unit-input" name="material_id[]" type="text" value="${selectedMaterial.id}" hidden />
                            <input class="form-control unit-input" name="_isMaterialBase[]" type="text" value="${isMaterialBase}" hidden />
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
                    const originalConversionFactor = originalUnit === 'pieces' ? 1 : selectedMaterial.unit
                        .conversion_factor;

                    function calculateSubtotal() {
                        unitIdInput.value = unitsSelect.value;
                        const quantity = parseFloat(quantityInput.value);
                        const unitPrice = parseFloat(unitPriceInput.value.replace(/[^\d.-]/g, ''));
                        const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
                        const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) ||
                            1;
                        const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice *
                            selectedConversionFactor / originalConversionFactor);
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
                        e.target.closest('.material-row').remove();

                        calculateTotal();
                    }
                });
            });

            // Attach the event listener to a parent element that exists on page load
            document.body.addEventListener('change', function(event) {
                // Check if the changed element has the class 'material-select'
                if (event.target.classList.contains('material-select')) {
                    var selectedOption = event.target.options[event.target.selectedIndex];
                    var unitString = selectedOption.getAttribute('data-unit');
                    var unitObject = JSON.parse(unitString); // Parse the JSON string into an object
                    var unitName = unitObject.name; // Access the 'name' property of the unit object
                    var unitInput = event.target.closest('.material-row').querySelector('.unit-input');
                    unitInput.value = unitName; // Set the unit name
                }
            });

            const deleteProduct = (productId) => {
                if (confirm('Are you sure you want to delete this product?')) {
                    fetch('/products/' + productId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the product row from the table
                                toastr.success('Successfully deleted!');
                                document.querySelector(`[data-id="${productId}"]`).closest('tr').remove();
                            } else {
                                toastr.error('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            };

            document.body.addEventListener('click', function(event) {
                var target = event.target;
                if (target.matches('.edit-btn, .edit-btn *')) {
                    var button = target.closest('.edit-btn');
                    var productId = button.getAttribute('data-id');

                    fetch('/products/get/' + productId)
                        .then(response => response.json())
                        .then(data => {

                            var modal = document.getElementById('addProductModal');
                            var modalTitle = modal.querySelector('.modal-title');
                            var form = modal.querySelector('form');

                            modalTitle.textContent = 'Edit Product';
                            form.action = '/products/update/' + data.id;
                            form.querySelector('[name="name"]').value = data.name;
                            form.querySelector('[name="current_stock_level"]').value = data.current_stock_level;
                            form.querySelector('[name="min_stock_level"]').value = data.min_stock_level;
                            form.querySelector('[name="product_code"]').value = data.product_code;
                            form.querySelector('[name="notes"]').value = data.notes || '';
                            form.querySelector('[name="isMaterialBase"]').checked = data.isMaterialBase || 0;
                            document.querySelector('[name="unit_id"]').value = data.unit_id || 0;
                            // form.querySelector('[name="last_order_date"]').value = data.last_order_date ? data
                            //     .last_order_date.split(' ')[0] : '';

                            var photoPath = data.photo_path ? '{{ asset('storage') }}/' + data.photo_path : '';
                            //console.log(photoPath);
                            var photoPreview = document.getElementById('photoPreview');
                            if (photoPath) {
                                photoPreview.src = photoPath;
                                photoPreview.style.display = 'block';
                            } else {
                                photoPreview.style.display = 'none';
                            }

                            // Mapping for product type values
                            var productTypeMapping = {
                                'Sourced from Supplier': '1',
                                'Made By Me': '2'
                            };


                            // Select the correct product type
                            var productTypeSelect = form.querySelector('[name="product_type"]');
                            var mappedProductType = productTypeMapping[data.product_type.trim()];

                            if (mappedProductType) {
                                for (var i = 0; i < productTypeSelect.options.length; i++) {
                                    if (productTypeSelect.options[i].value === mappedProductType) {
                                        productTypeSelect.options[i].selected = true;
                                        break;
                                    }
                                }

                                var event = new Event('change');
                                productTypeSelect.dispatchEvent(event);
                            }

                            resetLocationSelect();
                            var locationsContainer = document.getElementById('location-container');
                            var locationSelectElement = document.querySelector('select[name="locations"]');

                            // Populate the materials information
                            var materialsContainer = document.getElementById('materials-container');
                            var selectElement = document.querySelector('select[name="materials"]');
                            locationsContainer.innerHTML = '';
                            materialsContainer.innerHTML = '';
                            data.product_inventory.forEach(inventory => {

                                var locationOption = Array.from(locationSelectElement.options).find(option => option.value == inventory.location_id);
                                locationSelectElement.removeChild(locationOption);

                                var newRow = document.createElement('div');
                                newRow.className = 'row location-row mt-3';
                                newRow.innerHTML = `
                                    <div class="col-md-4">
                                        <label>Location</label>
                                        <input class="form-control" name="location_id[]" type="text" value="${inventory.location_id}" hidden />
                                        <input class="form-control unit-input" name="location_name" type="text" value="${locationOption.textContent}" readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label>Current stock level</label>
                                        <input class="form-control unit-input" name="location_current_level[]" value="${inventory.current_stock_level}" type="number"/>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Min stock level</label>
                                        <input class="form-control unit-input" name="location_min_level[]" value="${inventory.min_stock_level}" type="number"/>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                                        <button type="button" class="btn btn-secondary btn-sm remove-location-row">×</button>
                                    </div>
                                    `;
                                    locationsContainer.appendChild(newRow);
                            });

                            
                            
                            function updateTotalMinStockLevel() {
                                let totalMinStockLevel = 0;

                                // Select all inputs for location min stock level
                                const minStockInputs = document.querySelectorAll('[name="location_min_level[]"]');
                                minStockInputs.forEach(input => {
                                    const value = parseFloat(input.value);
                                    if (!isNaN(value)) {
                                        totalMinStockLevel += value;
                                    }
                                });

                                // Update the main min_stock_level input with the computed sum
                                const totalMinStockInput = document.querySelector('[name="min_stock_level"]');
                                if (totalMinStockInput) {
                                    totalMinStockInput.value = totalMinStockLevel;
                                }
                            }

                            updateTotalMinStockLevel();

                            // Attach the event listener to the document for dynamic inputs
                            document.addEventListener('input', function (event) {
                                if (event.target.name === 'location_min_level[]') {
                                    updateTotalMinStockLevel();
                                }
                            });

                            locationsContainer.addEventListener('click', function(e) {
                                if (e.target.classList.contains('remove-location-row')) {
                                    
                                    var row = e.target.closest('.location-row');
                                    var locationName = row.querySelector('input[name="location_name"]').value;                                    
                                    var locationId = row.querySelector('input[name="location_id[]"]').value;

                                    // Create a new option element and reinsert it into the select element
                                    var newOption = document.createElement('option');
                                    newOption.value = locationId;
                                    newOption.textContent = locationName;
                                    locationSelectElement.appendChild(newOption);

                                    // Remove the row from the container
                                    row.remove();
                                    updateTotalMinStockLevel();
                                }
                            });

                            materialsContainer.innerHTML = ''; // Clear existing materials

                            data.product_materials.forEach(material => {
                                if (!material.material) {
                                    return;
                                }
                                var newRow = document.createElement('div');

                                var selectedLocation = materialsData.find(_material => _material.id ==
                                    material.material_id);
                                var materialName = '';
                                var materialPrice;
                                var unit_id = 0;
                                if (material.product_id_material_base) {
                                    if (!material.product_id_material_base) return;
                                    materialName = getProductById(material.product_id_material_base).name;
                                    materialPrice = getProductById(material.product_id_material_base).price;
                                    unit_id = getProductById(material.product_id_material_base).unit_id;
                                }
                                else {
                                    materialName = material.material.name;
                                    materialPrice = material.material.price_per_unit;
                                    unit_id = material.material.unit_id;
                                }
                                let unitInfo = getUnitById(unit_id);

                                const isMaterialBase = material.isMaterialBase !== undefined ? material.isMaterialBase : false;
                                
                                newRow.className = 'row material-row mt-3';
                                newRow.innerHTML = `
                <div class="col-md-3">
                    <label>Material</label>
                    <input class="form-control unit-input" name="material_id[]" type="text" value="${material.material_id}" hidden />
                    <input class="form-control unit-input" name="_isMaterialBase[]" type="text" value="${isMaterialBase}" hidden />
                    <input class="form-control unit-input" name="material_name" type="text" value="${materialName}" readonly />
                </div>
                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(materialPrice)}/${unitInfo.name}" readonly />
                </div>
                <div class="col-md-2">
                    <label>Quantity</label>
                    <input class="form-control" name="material_count[]" type="number" value="${parseToDecimal(material.used_amount)}"  step="0.01" required />
                </div>
                <div class="col-md-2">
                <label>Unit Type</label>
                <select class="form-control unit-input" name="unit"></select>
                <input class="form-control" name="material_unit_id[]" type="hidden" value="${unit_id}" />
            </div>
              
                <div class="col-md-2">
                    <label>Subtotal</label>
                    <input class="form-control unit-input" name="sub_total[]" type="text" value="${parseFloat(material.unit_price * material.used_amount).toFixed(3)}" readonly />
                </div>
                <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                    <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
                </div>
            `;
                                materialsContainer.appendChild(newRow);


                                var unitsSelect = newRow.querySelector('select[name="unit"]');
                                if (material.product_id_material_base) {
                                    populateUnitsSelect(unitsSelect, getProductById(material.product_id_material_base), material.unit_id);
                                }
                                else {
                                    var selectedMaterial = materialsData.find(_material => _material.id ==
                                    material.material_id);
                                    populateUnitsSelect(unitsSelect, selectedMaterial, material.unit_id);
                                }
                               

                                // Remove the material from the select options
                                for (var i = 0; i < selectElement.options.length; i++) {
                                    if (selectElement.options[i].value == material.material_id) {
                                        selectElement.remove(i);
                                        break;
                                    }
                                }

                                const quantityInput = newRow.querySelector(
                                    '[name="material_count[]"]');
                                const unitPriceInput = newRow.querySelector(
                                    '[name="unit_price[]"]');
                                const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
                                var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');

                                const originalUnit = unitInfo.name;
                                const originalConversionFactor = originalUnit === 'pieces' ? 1 : unitInfo.conversion_factor;

                                function calculateSubtotal() {
                                    unitIdInput.value = unitsSelect.value;
                                    const quantity = parseToDecimal(quantityInput.value);
                                    const unitPrice = parseToDecimal(unitPriceInput.value.replace(/[^\d.-]/g,
                                        ''));
                                    const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
                                    const selectedConversionFactor = parseFloat(selectedOption.dataset
                                            .conversionFactor) ||
                                        1;
                                    const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity *
                                        unitPrice *
                                        selectedConversionFactor / originalConversionFactor);
                                    subtotalInput.value = new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD'
                                    }).format(subtotal);

                                    calculateTotal();
                                }

                                quantityInput.addEventListener('input', calculateSubtotal);
                                unitsSelect.addEventListener('change', calculateSubtotal);

                                calculateSubtotal();
                            });

                            var selectSupplierElement = $('#supplierSelect');
                            if (selectSupplierElement.length) {
                                var supplierId = data.supplier_id;
                                selectSupplierElement.val(supplierId).trigger('change');
                            }

                            var selectCategoryElement = $('#categorySelect');
                            if (selectCategoryElement.length) {
                                var categoryId = data.category_id;
                                selectCategoryElement.val(categoryId).trigger('change');
                            }

                            calculateTotal();


                            $('#addProductModal').modal('show');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            });

            // Reset modal to "Add New Product" state when it is hidden
            $('#addProductModal').on('hidden.bs.modal', function() {
                var modal = document.getElementById('addProductModal');
                var modalTitle = modal.querySelector('.modal-title');
                var form = modal.querySelector('form');

                modalTitle.textContent = 'Add New Product';
                form.action = '/products/add';
                form.reset();
                $('#supplierSelect').val('').trigger('change');
                $('#categorySelect').val('').trigger('change');

                var materialsContainer = document.getElementById('materials-container');
                if (materialsContainer) {
                    // Clear all dynamic material part elements
                    materialsContainer.innerHTML = '';
                }

                var locationsContainer = document.getElementById('location-container');
                if (locationsContainer) {
                    // Clear all dynamic material part elements
                    locationsContainer.innerHTML = '';
                }

                //reset material select
                var materialsData = JSON.parse('@json($materials)');

                // Get the select element
                var selectElement = document.querySelector('select[name="materials"]');

                // Clear current options
                selectElement.innerHTML = '';
                // Populate with new options
                materialsData.forEach(function(material) {
                    var option = document.createElement('option');
                    option.value = material.id;
                    option.text = material.name;
                    selectElement.appendChild(option);
                });

                var locationsData = JSON.parse('@json($locations)');

                // Get the select element
                var locationSelectElement = document.querySelector('select[name="locations"]');

                // Clear current options
                locationSelectElement.innerHTML = '';
                // Populate with new options
                locationsData.forEach(function(location) {
                    var option = document.createElement('option');
                    option.value = location.id;
                    option.text = location.name;
                    locationSelectElement.appendChild(option);
                });

                var productTypeSelect = form.querySelector('[name="product_type"]');
                var event = new Event('change');
                productTypeSelect.dispatchEvent(event);
            });

            //applyCategoryColors(); //apply Category colors

            $(document).on('click', '.collapsible', function() {
                if (!$(event.target).is(':checkbox')) {
                    var variantId = $(this).data('variant-id');
                    var contentRow = $('tr.content[data-variant-id="' + variantId + '"]');
                    if (contentRow.is(":visible")) {
                        contentRow.fadeOut(100); // Fade out to collapse
                    } else {
                        contentRow.fadeIn(100); // Fade in to expand
                    }
                        }
                    });

            function rebindEventListeners() {
                $('.product-name').on('click', function(event) {
                    event.preventDefault();
                    var productId = $(this).data('id');

                    $.ajax({
                        url: '/products/' + productId,
                        method: 'GET',
                        success: function(response) {
                            var product = response.product;
                            var productMaterials = response.productMaterials;

                            // Update the modal with product information
                            if (!product.shopify_id) {
                                $("#details_shopify_link").hide();
                            } else {
                                $("#details_shopify_link").show();
                                $('.shopify-link').attr('href',
                                    "https://{{ auth()->user()->company->shopify_domain }}/admin/products/" + product
                                    .variants_id + '/variants/' + product.shopify_id);
                            }


                            $('#product-name').text(product.name);
                            $('#product-stock').text(product.current_stock_level + ' ' + product
                                .unit.name);

                            $('#product-min-stock').text(product.min_stock_level !== null ? product
                                .min_stock_level + ' ' + product.unit.name : '-');
                            $('#product-unit-cost').text('$' + parseFloat(product.price)
                                .toFixed(3) + '/' + product.unit.name);


                            var total = 0;
                            var orderDetailsBody = '';

                            productMaterials.forEach(function(material) {
                                if (!material.material && !material.product_id_material_base) {
                                    return;
                                }
                                var subtotal = material.quantity * material.unit_price;
                                total += subtotal;
                                var minStockLevel;
                                if (material.product_id_material_base) {
                                    if (getProductById(material.product_id_material_base)) {
                                        minStockLevel = getProductById(material.product_id_material_base).min_stock_level;
                                        material.material = getProductById(material.product_id_material_base);
                                    } else {
                                        return;
                                    }
                                }else {
                                    minStockLevel = material.material && material.material.min_stock_level !== null ? 
                                    `${parseToDecimal(material.material.min_stock_level)} ${material.material.unit.name}` : 
                                    '-';
                                }
                                
                                var rowClass = parseFloat(material.material?.current_stock_level || 0) < 
                                            parseFloat(material.material?.min_stock_level || 0) ? 
                                            'text-danger' : '';

                                let unitInfo = material.unit_id ? getUnitById(material.unit_id) : null;
                                orderDetailsBody += `
                                    <tr class="${rowClass}">
                                        <td>${material.material.name}</td>
                                        <td>${parseToDecimal(material.used_amount)} ${unitInfo.name}</td>
                                        <td>${parseToDecimal(material.material.current_stock_level)} ${material.material.unit.name}</td>
                                        <td>${minStockLevel}</td>
                                        <td>$${parseToDecimal(material.unit_price).toFixed(2)}</td>
                                    </tr>
                                `;
                            });

                            $('#product-details-body').html(orderDetailsBody);

                            $('#productDetailsModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching product details:', error);
                        }
                    });
                });


                $('.product-checkbox').off('change').on('change', function() {
                    toggleCombineVariantsButton();
                });

                $('#select-all').off('click').on('click', function() {
                    $('.product-checkbox').prop('checked', this.checked);
                    $('.variant-checkbox').prop('checked', this.checked);
                    toggleCombineVariantsButton();
                });

                $('.variant-checkbox').off('change').on('change', function() {
                    var isChecked = $(this).is(':checked');
                    var variantId = $(this).closest('tr').data('variant-id');
                    $('tr.content[data-variant-id="' + variantId + '"] .product-checkbox').prop('checked', isChecked);
                    //$(this).closest('tr').next('.content').find('.product-checkbox').prop('checked', isChecked);
                });

               
                

                //applyCategoryColors();
            }

            function toggleCombineVariantsButton() {
                var checkedCount = $('.product-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#combineVariantsBtn').show();
                } else {
                    $('#combineVariantsBtn').hide();
                }
            }

            $(document).ready(function() {

                rebindEventListeners();

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
                $('#categorySelectEdit').select2({
                    tags: true,
                    placeholder: 'Type category name...',
                    width: '100%',
                    insertTag: function(data, tag) {
                        tag.text = 'Create "' + tag.text + '"';
                        // Insert the tag at the end of the results
                        data.push(tag);
                    }
                });

                $('#supplierSelectEdit').select2({
                    tags: true,
                    placeholder: 'Type supplier name...',
                    width: '100%',
                    insertTag: function(data, tag) {
                        tag.text = 'Create "' + tag.text + '"';
                        // Insert the tag at the end of the results
                        data.push(tag);
                    }
                });

                // Show modal with selected products
                $('#combineVariantsBtn').click(function() {
                    var selectedProducts = [];
                    $('.product-checkbox:checked').each(function() {
                        // Check if the parent row of the checkbox is a product row and not a variant row
                        var parentRow = $(this).closest('tr');
                        if (!parentRow.hasClass('collapsible')) {
                            var product = {
                                id: $(this).data('id'),
                                name: parentRow.find('td:nth-child(2)').text(),
                                stockLevel: parentRow.find('td:nth-child(3)').text(),
                                unitCost: parentRow.find('td:nth-child(5)').text(),
                                sku: parentRow.find('td:nth-child(6)').text(),
                                category: parentRow.find('td:nth-child(7)').text()
                            };
                            selectedProducts.push(product);
                        }
                    });

                    var selectedProductsTable = $('#selectedProductsTable');
                    selectedProductsTable.empty();
                    selectedProducts.forEach(function(product) {
                        selectedProductsTable.append(`
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.stockLevel}</td>
                        <td>${product.unitCost}</td>
                        <td>${product.sku}</td>
                        <td>${product.category}</td>
                    </tr>
                    <input type="hidden" name="product_ids[]" value="${product.id}">
                `);
                    });

                    $('#combineVariantsModal').modal('show');
                });

                var events_table = $('#dataTable').DataTable({
                    "serverSide": true,
                    "mark": true,
                    "ordering": false,
                    "searching": true,
                    "ajax": function(data, callback, settings) {
                        $.ajax({
                                url: '/products/pageScroll',
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
                                    $("#table-data").empty(); // Clear the table body
                                    $("#table-data").html(response); // Replace with new data

                                isScrollingBlocked = !!data.search.value; // Updated this line
                                page = 1;
                                rebindEventListeners();
                                },
                                error: function(jqXHR, ajaxOptions, thrownError) {
                                $('.ajax-load').hide(); // Hide loading indicator on error
                                //alert.error('Server is not responding...');
                                },
                                always: function() {
                                    loading = false;
                                }
                        });
                    },
                });

                $('#customSearchInput').on('keyup', function() {
                    events_table.search(this.value).draw();
                });

                $('#dataTable_filter input').on('keyup', function() {
                        searchQuery = this.value; // Update search query
                        events_table.ajax.reload(); // Reload DataTable with new search query
                    });

                });
            document.getElementById('add_product_type').addEventListener('change', function() {

                var productSection = document.getElementById('add_product_section');
                var costSection = document.getElementById('add_cost_section');
                var supplierSection = document.getElementById('add_supplier_section');
                //var costCalcSection = document.getElementById('add_product_cost_calc');

                if (this.value === '2') {
                    productSection.style.display = 'block';
                    costSection.style.display = 'none';
                    supplierSection.style.display = 'none';
                    //costCalcSection.style.display = 'block';
                } else {
                    productSection.style.display = 'none';
                    costSection.style.display = 'block';
                    supplierSection.style.display = 'block';
                    //costCalcSection.style.display = 'none';
                }
            });

            document.getElementById('addProductModal').querySelector('form').addEventListener('submit', function(event) {
                event.preventDefault();
                var form = event.target;
                var formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (response.headers.get('content-type')?.includes('application/json')) {
                            return response.json();
                        } else {
                            throw new Error('Server response was not JSON');
                        }
                    })
                    .then(data => {

                        if (data.success) {
                            // Update the product row in the table
                            if (form.action.includes('/products/add')) {
                                // Reload the page if the form action is '/products/add'
                                window.location.reload();
                            } else {
                                var productRow = document.querySelector(`tr[data-id='${data.product.id}']`);
                                productRow.querySelector('a.product-name').textContent = data.product.name;
                                productRow.querySelector('.product-stock').textContent =
                                    `${data.product.current_stock_level} ${data.product.unit ? data.product.unit.name : ''}`;

                                productRow.querySelector('.product-min-stock').textContent = data.product
                                    .min_stock_level == null ? '-' :
                                    `${data.product.min_stock_level} ${data.product.unit ? data.product.unit.name : ''}`;
                                productRow.querySelector('.product-code').textContent = data.product.product_code;
                                productRow.querySelector('.product-notes').textContent = data.product.notes;
                                productRow.querySelector('.product-price').textContent =
                                    `$${parseFloat(data.product.price).toFixed(2)}/${data.product.unit ? data.product.unit.name : ''}`;
                                if (data.photo_path) {
                                    productRow.querySelector('img[alt="Product Photo"]').src =
                                        '{{ asset('storage') }}/' + data.photo_path;
                                }
                                $('#addProductModal').modal('hide');
                                toastr.success('Product updated successfully!');
                            }



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
            // $(document).ready(function() {
            //     var div1 = document.querySelector('.scrollbar-top');
            //     var scrollWrapper = document.querySelector('.scrollbar-wrapper');
            //     var card = document.querySelector('.card-body');
            //     var table = document.querySelector('.table');


            //     var cardRect = card.getBoundingClientRect();
            //     var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            //     div1.style.width = table.scrollWidth + 90 + 'px';
            //     scrollWrapper.style.width = card.offsetWidth + 'px';
            //     scrollWrapper.style.left = cardRect.left + 'px';

            //     $(".scrollbar-wrapper").scroll(function() {
            //         $(".table-wrapper").scrollLeft($(".scrollbar-wrapper").scrollLeft());
            //     });
            //     $(".table-wrapper").scroll(function() {
            //         $(".scrollbar-wrapper").scrollLeft($(".table-wrapper").scrollLeft());
            //     });

            // });
        </script>
        
    @endsection
