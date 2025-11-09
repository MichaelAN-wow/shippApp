@extends('layouts.admin_master')
@section('content')
<link href="{{ asset('backend/css/production.css') }}" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Production
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-controls">
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
                    data-target="#addProductionModal">
                    <i class="fas fa-plus"></i> New Production Run
                </button>
            </div>
        </div>
    </div>
    <div class="card-body card-production">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-4">
                    <div id="todo" class="droppable" data-zone="1">
                        <h4>Planned</h4>
                        @php
                            $plannedCount = isset($productions) ? $productions->filter(function($production) {
                                return $production->status === 'Planned';
                            })->count() : 0;
                        @endphp
                        <span class="production-count">{{ $plannedCount }}</span>
                        <div class="draggable-container">
                            @foreach ($productions as $row)
                            @if ($row->status == 'Planned')
                            <div class="draggable" data="{{ $row->id }}">
                                <div class="production-name">{{ $row->name }}
                                    <span class="avatar-tooltip" title="{{ $row->user->name }}" style="float: right">
                                        <div class="avatar" data-username="{{ $row->user->name }}">
                                            {{ substr($row->user->name, 0, 1) }}
                                        </div>
                                    </span>
                                </div>
                                <div class="production-details">
                                    {{ $row->production_product->sum('quantity') }} pieces total &bull;
                                    <span
                                        class="due-date {{ \Carbon\Carbon::parse($row->due_date)->isPast() ? 'text-danger' : '' }}">
                                        Due {{ \Carbon\Carbon::parse($row->due_date)->format('M d') }}
                                    </span>
                                </div>
                                <div class="production-location">
                                    @if ($row->location)
                                    <img src="{{ asset('images/svg/location.svg') }}" alt="Location Icon" class="location-icon">
                                    <span>{{ $row->location->name }}</span>
                                    @else
                                    <i class="fas fa-exclamation-circle"></i>
                                    <!-- Alternative icon or message -->
                                    <span>No location available</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="inprogress" class="droppable" data-zone="2">
                        <h4>In Progress</h4>
                        @php
                            $plannedCount = isset($productions) ? $productions->filter(function($production) {
                                return $production->status === 'In Progress';
                            })->count() : 0;
                        @endphp
                        <span class="production-count">{{ $plannedCount }}</span>
                        <div class="draggable-container">
                            @foreach ($productions as $row)
                            @if ($row->status == 'In Progress')
                            <div class="draggable" data="{{ $row->id }}">
                                <div class="production-name">{{ $row->name }}
                                    <span class="avatar-tooltip" title="{{ $row->user->name }}" style="float: right">
                                        <div class="avatar" data-username="{{ $row->user->name }}">
                                            {{ substr($row->user->name, 0, 1) }}
                                        </div>
                                    </span>
                                </div>
                                <div class="production-details">
                                    {{ $row->production_product->sum('quantity') }} pieces total &bull;
                                    <span
                                        class="due-date {{ \Carbon\Carbon::parse($row->due_date)->isPast() ? 'text-danger' : '' }}">
                                        Due {{ \Carbon\Carbon::parse($row->due_date)->format('M d') }}
                                    </span>
                                </div>
                                <div class="production-location">
                                    @if ($row->location)
                                    <img src="{{ asset('images/svg/location.svg') }}" alt="Location Icon" class="location-icon">
                                    <span>{{ $row->location->name }}</span>
                                    @else
                                    <i class="fas fa-exclamation-circle"></i>
                                    <!-- Alternative icon or message -->
                                    <span>No location available</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="done" class="droppable" data-zone="3">
                        <h4>Complete</h4>
                        @php
                            $plannedCount = isset($productions) ? $productions->filter(function($production) {
                                return $production->status === 'Completed';
                            })->count() : 0;
                        @endphp
                        <span class="production-count">{{ $plannedCount }}</span>
                        <div class="draggable-container">
                            @foreach ($productions as $row)
                            @if ($row->status == 'Completed')
                            <div class="draggable" data="{{ $row->id }}">
                                <div class="production-name">{{ $row->name }}
                                    <span class="avatar-tooltip" title="{{ $row->user ? $row->user->name : 'Unknown User' }}" style="float: right">
                                        <div class="avatar" data-username="{{ $row->user ? $row->user->name : 'Unknown' }}">
                                            {{ $row->user ? substr($row->user->name, 0, 1) : '?' }}
                                        </div>
                                    </span>
                                </div>
                                <div class="production-details">
                                    {{ $row->production_product->sum('quantity') }} pieces total &bull;
                                    <span class="due-date">
                                        {{ \Carbon\Carbon::parse($row->due_date)->format('M d') }}
                                    </span>
                                </div>
                                <div class="production-location">
                                    @if ($row->location)
                                    <img src="{{ asset('images/svg/location.svg') }}" alt="Location Icon" class="location-icon">
                                    <span>{{ $row->location->name }}</span>
                                    @else
                                    <i class="fas fa-exclamation-circle"></i>
                                    <!-- Alternative icon or message -->
                                    <span>No location available</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addProductionModal" tabindex="-1" role="dialog" aria-labelledby="addProductionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductionModalLabel">Add Production</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/production/add') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="inputFirstName">Production Name</label>
                                <input class="form-control" name="name" type="text" placeholder="" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="selectShopifyLocation">Location: Please select the
                                    relevant location.</label>
                                <select class="form-control" name="shopify_location_id" id="selectShopifyLocation"
                                    required>
                                    @foreach ($shopifyLocations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="inputUser">Assigned User</label>
                                <select class="form-control" name="user_id" id="selectUser" required>
                                    @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ Auth::user()->id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small mb-1" for="inputLastName">Due Date</label>
                                <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" name="due_date"
                                        data-target="#datetimepicker3" value="{{ date('Y-m-d H:i') }}" />
                                    <div class="input-group-append" data-target="#datetimepicker3"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <label class="small mb-1" for="inputLastName">Add Another Item</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" name="products">
                                        @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" id="add-product" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 refill-config" style="display: none;">
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5>Dough Bowl Refill Details</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fragrance Oil</label>
                                                <select class="form-control" name="fragrance_id" required>
                                                    @foreach($fragrances as $fragrance)
                                                    <option value="{{ $fragrance->id }}">{{ $fragrance->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fragrance Amount (oz)</label>
                                                <input type="number" class="form-control" name="fragrance_amount" 
                                                    value="2" min="0.1" step="0.1" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="products-container">
                            <!-- Product row template -->

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small mb-1" for="inputFirstName">Notes</label>
                                <input class="form-control" name="notes" type="text" placeholder="" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block"
                            id="saveButton">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="confirmModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>We'll update the following items:</p>
                <div id="updateItemsList">
                    <!-- Example of an update item -->
                    <div id="updateItemsList">
                        <!-- Dynamically generated items will be added here -->
                    </div>
                    <!-- Add dynamically generated items here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Complete Run</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productionDetailsModal" tabindex="-1" aria-labelledby="productionDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productionDetailsModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4" id="production-items">
                        <h5>Items</h5>
                        <!-- Product items will be listed here -->
                    </div>
                    <div class="col-md-8" id="production-details">
                        <div id="production-header">
                            <!-- Header will be filled by JavaScript -->
                        </div>
                        <div id="production-summary"></div>
                        <h5>Materials Used</h5>
                        <div id="production-materials"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-xs" id="editProductionBtn">Edit</button>
                <button type="button" class="btn btn-danger btn-xs" id="deleteProductionBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(255,255,255,0.7);align-items:center;justify-content:center;">
    <div style="text-align:center;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div style="margin-top:1rem;font-size:1.2rem;">Processing, please wait...</div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    var productionsData = @json($productions);
    var productsData = @json($products);
    var weightUnits = @json($weightUnits);
    var defaultUnitId = @json($defaultUnitId);
    var unitsData = @json($unitsJson);

    function getProductById(id) {
        for (const product of productsData) {
            if (product.id === id) {
                return product;
            }
        }
        return null;
    }

    // Function to get unit name
    function getWeigthUnitNameById(unitId) {
        const unit = weightUnits.find(u => u.id === unitId);
        return u
        nit ? unit.name : null;
    }

    function getUnitNameById(unitId) {
        for (const type in unitsData) {
            const units = unitsData[type];
            for (const unit of units) {
                if (unit.id === unitId) {
                    return unit.name;
                }
            }
        }
        return null; // Return null if unit_id not found
    }

    // Function to calculate conversion factor between two unit_ids
    function calculateConversionFactor(unitIdFrom, unitIdTo) {
        const fromUnit = weightUnits.find(u => u.id === unitIdFrom);
        const toUnit = weightUnits.find(u => u.id === unitIdTo);

        if (fromUnit && toUnit && fromUnit.type === toUnit.type) {
            // Convert from source unit to base unit (grams) and then to target unit
            return parseFloat(fromUnit.conversion_factor) / parseFloat(toUnit.conversion_factor);
        }

        return 1; // Return 1 if units are incompatible or not found (no conversion)
    }

    // Function to make elements draggable
    function makeDraggable() {
        $(".draggable").draggable({
            revert: "invalid",
            cursor: "move",
            helper: "clone",
            start: function() {
                $(this).css('opacity', '0.5');
            },
            stop: function() {
                $(this).css('opacity', '1');
            }
        });
    }

    // Function to update empty state
    function updateEmptyState() {
        $(".droppable").each(function() {
            var $draggableContainer = $(this).find('.draggable-container');
            
            if ($draggableContainer.children('.draggable').length === 0) {
                if ($draggableContainer.find('.empty-state').length === 0) {
                    $draggableContainer.html(
                        '<div class="empty-state"><h4 class="empty-title">Nothing here!</h4><p class="empty-text"></p></div>'
                    );
                }
            } else {
                $draggableContainer.find('.empty-state').remove();
            }
        });
    }

    // Function to set avatars color
    function setAvatarColors() {
        var avatars = document.querySelectorAll('.avatar');
        avatars.forEach(function(avatar) {
            var username = avatar.getAttribute('data-username');
            var color = getColorForCategory(username);
            avatar.style.backgroundColor = color;
        });
    }

    // Function to update materials used
    function updateMaterialsUsed(materials) {
        if (!materials || materials.length === 0) {
            document.getElementById('production-materials').innerHTML = 'No materials used';
        } else {
            let materialsHtml = '';
            materials.forEach(material => {
                materialsHtml +=
                    `<div>${material.name} - ${parseToDecimal(material.quantity)} ${getUnitNameById(material.unit_id)}</div>`;
            });
            document.getElementById('production-materials').innerHTML = materialsHtml;
        }
    }

    // Initialize draggable elements and empty states
    makeDraggable();
    updateEmptyState();
    setAvatarColors();

    // Droppable functionality
    $(".droppable").droppable({
        accept: ".draggable",
        drop: function(event, ui) {
            var droppedItem = ui.helper.clone().removeAttr('style');
            var itemId = droppedItem.attr('data');
            var zoneName = $(this).data('zone');

            var production = productionsData.find(function(prod) {
                return prod.id == itemId;
            });

            if (zoneName == 3 && production.is_completed == 0) {
                if (production) {
                    var itemsHtml = '';
                    var materialsMap = new Map();

                    $("#confirmModal .modal-title").text(production.name);

                    // Calculate updated stock levels for products
                    production.production_product.forEach(function(pp) {
                        var product = pp.product;
                        var currentStockLevel = product.current_stock_level;
                        var updatedStockLevel = currentStockLevel + pp
                            .quantity; // Adjusted for increment
                        var unitName = product.unit ? product.unit.name : 'units';
                        var productName = product.name;

                        if (pp.material_id) {
                            fragrances.forEach(function(fragrance) {
                                if (fragrance.id == pp.material_id) {
                                    var fragranceId = fragrance.id;
                                    var fragranceCurrentLevel = fragrance.current_stock_level;
                                    
                                    // Get the conversion factor between the units
                                    var conversionFactor = calculateConversionFactor(pp.material_unit_id, fragrance.unit_id);
                                    
                                    // Convert the quantity to the material's unit
                                    var fragranceUsed = parseToDecimal(pp.material_quantity * conversionFactor);
                                    
                                    productName += '+' + fragrance.name;
                                    if (materialsMap.has(fragranceId)) {
                                        materialsMap.get(fragranceId).used += fragranceUsed;
                                    } else {
                                        materialsMap.set(fragranceId, {
                                            id: fragranceId,
                                            name: fragrance.name,
                                            currentLevel: fragranceCurrentLevel,
                                            used: fragranceUsed,
                                            unit: getUnitNameById(fragrance.unit_id),
                                            minStockLevel: fragrance.min_stock_level,
                                            unit_price: fragrance.price_per_unit
                                        });
                                    }
                                }
                            });
                        }
                        itemsHtml += `
                                    <div class="update-item">
                                        <strong>${productName}</strong>
                                        <span>${currentStockLevel} ${unitName} &rarr; ${updatedStockLevel} ${unitName}</span>
                                    </div>`;
                        // Accumulate material usage
                        if (product.product_materials) {
                            product.product_materials.forEach(function(pm) {
                                var material = pm.material;
                                console.log('product:', product);
                                if (material) {
                                    var materialId;
                                    if (pm.product_id_material_base){
                                        material = getProductById(pm.product_id_material_base);
                                        materialId = '###' + material.id; // this means the soap base id
                                    } else 
                                        materialId = material.id;
                                    
                                    var materialCurrentLevel = material
                                        .current_stock_level;
                                    console.log('material.unit_id:', material.unit_id);
                                    console.log('pm.unit_id:', pm.unit_id);
                                    var conversion_factor = calculateConversionFactor(pm.unit_id, material.unit_id);
                                    console.log(conversion_factor);
                                    var materialUsed = parseToDecimal(pm.used_amount *
                                        pp.quantity * conversion_factor);
                                    if (materialsMap.has(materialId)) {
                                        materialsMap.get(materialId).used +=
                                            materialUsed;
                                    } else {
                                        materialsMap.set(materialId, {
                                            id: materialId,
                                            name: material.name,
                                            currentLevel: materialCurrentLevel,
                                            used: materialUsed,
                                            unit: getUnitNameById(material
                                                .unit_id),
                                            minStockLevel: material
                                                .min_stock_level,
                                            unit_price: material
                                                .price_per_unit
                                        });
                                    }
                                }
                            });
                        }

                      
                    });

                    // Calculate updated stock levels for materials
                    materialsMap.forEach(function(material) {
                        var materialUpdatedLevel = material.currentLevel - material
                            .used;
                        var isBelowMinStockLevel = materialUpdatedLevel < material
                            .minStockLevel;
                        var stockLevelColor = isBelowMinStockLevel ?
                            'style="color: #c50c0c;"' : '';

                        itemsHtml += `
                                    <div class="update-item ml-3">
                                        <em ${stockLevelColor}>${material.name}</em>
                                        <span ${stockLevelColor}>${parseToDecimal(material.currentLevel)} ${material.unit} &rarr; ${parseToDecimal(materialUpdatedLevel)} ${material.unit}</span>
                                    </div>`;
                    });

                    $("#updateItemsList").html(itemsHtml);
                    $("#confirmModal").modal('show');
                } else {
                    toastr.error('Failed to find production details.');
                }

                $("#confirmBtn").off('click').on('click', function() {
                    $("#confirmModal").modal('hide');
                    $("#loadingOverlay").show(); // Show loading overlay
                    $(".droppable[data-zone='3']").append(droppedItem);
                    makeDraggable();
                    ui.draggable.remove();
                    updateEmptyState();

                    $.ajax({
                        url: '/production/complete-run',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        data: {
                            production_id: production.id,
                            products: production.production_product.map(pp => ({
                                product_id: pp.product.id,
                                before_level: pp.product
                                    .current_stock_level,
                                to_level: pp.product
                                    .current_stock_level + pp
                                    .quantity,
                                quantity: pp.quantity,
                                unit_price: pp.product.price
                            })),
                            materials: Array.from(materialsMap.values()).map(
                                material => ({
                                    material_id: material.id,
                                    before_level: material.currentLevel,
                                    to_level: material.currentLevel -
                                        material.used,
                                    quantity: material.used,
                                    unit_price: material.unit_price
                                }))
                        },
                        success: function(response) {
                            $("#loadingOverlay").hide(); // Hide loading overlay
                            toastr.success('Success!');
                            location.reload();
                        },
                        error: function(response) {
                            $("#loadingOverlay").hide(); // Hide loading overlay
                            let errorMsg = 'An error occurred. Please try again.';
                            
                            // Check if the response contains a message from the server
                            if (response.responseJSON && response.responseJSON.msg) {
                                errorMsg = response.responseJSON.msg;
                            }
                            
                            toastr.error(errorMsg); // Show the error message

                            setTimeout(function() {
                                window.location.reload();
                            }, 3000); // Reload after a delay to let the user read the error message
                        }
                    });
                });

                $("#confirmModal").on('hidden.bs.modal', function() {
                    if (!$(".droppable[data-zone='3']").find(`[data='${itemId}']`)
                        .length) {
                        ui.draggable.show();
                    }
                });
            } else {
                $(this).append(droppedItem);
                makeDraggable();
                ui.draggable.remove();
                updateEmptyState();

                $.ajax({
                    url: '/production/update',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]').getAttribute('content')
                    },
                    data: {
                        id: itemId,
                        zone: zoneName
                    },
                    success: function(response) {
                        toastr.success('Success!');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed!');
                    }
                });
            }
        }
    });

    const doughBowlOrCustomCategoryIds = {{ $doughBowlOrCustomCategoryIds ?? 'null' }};
    const fragrances = @json($fragrances);

    $('#add-product').on('click', function() {
        const selectElement = $('select[name="products"]');
        const selectedOption = selectElement.find('option:selected');
        const selectedId = selectedOption.val();
        const container = $('#products-container');
        const selectedProduct = productsData.find(product => product.id == selectedId);

        if (!selectedProduct) return;

        // Check if this is a dough bowl refill product
        const isRefill = doughBowlOrCustomCategoryIds.length > 0 && 
                 doughBowlOrCustomCategoryIds.includes(selectedProduct.category_id);

        let newRow;
        
        if (isRefill) {
            const fragranceOptions = fragrances.map(f => 
                `<option value="${f.id}">${f.name}</option>`
            ).join('');

            // Create unit options for fragrance with OZ as default
            const fragranceUnitOptions = weightUnits
                .map(unit => `<option value="${unit.id}" ${unit.id === defaultUnitId ? 'selected' : ''}>${unit.name}</option>`)
                .join('');

            newRow = `
            <div class="row product-row mt-3">
                <div class="col-md-4">
                    <label>Item</label>
                    <input type="hidden" name="product_id[]" value="${selectedProduct.id}">
                    <input type="hidden" name="is_refill[]" value="${selectedProduct.id}">
                    <input class="form-control" value="${selectedProduct.name}" readonly>
                </div>
                <div class="col-md-2">
                    <label>Quantity</label>
                    <input type="number" class="form-control product-quantity" 
                        name="product_quantity[]" value="1" min="1" required>
                </div>
                <div class="col-md-2">
                    <label>Fragrance</label>
                    <select class="form-control" name="fragrance_id[]" required>
                        ${fragranceOptions}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Fragrance Amount</label>
                    <div class="input-group">
                        <input type="number" class="form-control fragrance-amount-number" 
                            value="2" name="fragrance_amount_number[]" step="0.1">
                        <select class="form-control fragrance-unit" name="fragrance_unit_id[]" required>
                            ${fragranceUnitOptions}
                        </select>
                    </div>
                </div>
                <div class="col-md-1" style="margin-top: 34px">
                    <button type="button" class="btn btn-secondary btn-sm remove-product-row">×</button>
                </div>
            </div>`;
        } else {
            // Regular product row
            newRow = `
                <div class="row product-row mt-3 align-items-center">
                    <div class="col-md-5">
                        <label>Item</label>
                        <input class="form-control unit-input" name="product_id[]" type="text" value="${selectedProduct.id}" hidden />
                        <input class="form-control unit-input" name="product_name" type="text" value="${selectedProduct.name}" readonly />
                    </div>
                    <div class="col-md-2">
                        <label>Current Level</label>
                        <input class="form-control" type="text" value="${selectedProduct.current_stock_level} ${selectedProduct.unit.name}" readonly />
                    </div>
                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input class="form-control" name="product_quantity[]" type="text" value="1" required />
                    </div>
                    <div class="col-md-2">
                        <label>Unit Type</label>
                        <input class="form-control unit-input" name="unit" type="text" value="${selectedProduct.unit.name}" readonly />
                    </div>
                    <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                        <button type="button" class="btn btn-secondary btn-sm remove-product-row">×</button>
                    </div>
                </div>
                `;
        }

        container.append(newRow);

        if (isRefill) {
            // Add auto-calculation for refill rows
            container.find('.product-quantity').last().on('input', function() {
                const quantity = $(this).val();
                const fragranceAmount = quantity * 2;
                const $row = $(this).closest('.product-row');
                $row.find('.fragrance-amount-number').val(fragranceAmount);
            });
        }
        
        selectElement.find(`option[value='${selectedId}']`).remove();
        updateSaveButtonStatus();
    });

    // Remove product row button click event
    $('#products-container').on('click', '.remove-product-row', function() {
        var productRow = $(this).closest('.product-row');
        var productId = productRow.find('input[name="product_id[]"]').val();
        var productName = productRow.find('input[name="product_name"]').val();

        productRow.remove();

        var selectElement = $('select[name="products"]');
        var newOption = $('<option>', {
            value: productId,
            text: productName
        });
        selectElement.append(newOption);
    });

    // Change event for product select
    $('body').on('change', '.product-select', function() {
        var selectedOption = $(this).find('option:selected');
        var unitString = selectedOption.data('unit');
        var unitObject = JSON.parse(unitString);
        var unitName = unitObject.name;
        $(this).closest('.product-row').find('.unit-input').val(unitName);
    });

    // Draggable click event for delete
    $('.draggable').on('click', function() {
        const productionId = $(this).attr('data');

        $.getJSON(`/productions/${productionId}`, function(data) {
            // Update modal title
            $('#productionDetailsModalLabel').html(`
                        <h5>${data.name}</h5>
                        <div>
                            ${data.production_product.reduce((sum, product) => sum + product.quantity, 0)} pieces total &bull;
                            Completed ${data.completed_time !== 'N/A' ? `${data.completed_time} hours ago` : 'N/A'} &bull;
                            Assigned to ${data.assigned_to !== 'N/A' ? `<span class="badge badge-secondary">${data.assigned_to}</span>` : 'N/A'} &bull;
                            <img src="{{ asset('images/svg/location.svg') }}" alt="Location Icon" class="location-icon"> ${data.location}
                        </div>
                    `);

            // Update production items and select the first item by default
            let itemsHtml = '';
            data.production_product.forEach((product, index) => {
                itemsHtml +=
                    `<div class="product-item ${index === 0 ? 'selected' : ''}" data-product-id="${product.id}">${product.product.name} - ${product.quantity} pieces</div>`;
            });
            $('#production-items').html(itemsHtml);

            // Update production materials for the first product
            if (data.production_product.length > 0) {
                updateMaterialsUsed(data.production_product[0].product.materials);
            }

            // Add click event listener to each product item
            $('.product-item').on('click', function() {
                $('.product-item').removeClass('selected');
                $(this).addClass('selected');
                const productId = $(this).attr('data-product-id');
                const selectedProduct = data.production_product.find(product =>
                    product.id == productId);
                updateMaterialsUsed(selectedProduct.product.materials);
            });

            // Show modal
            $('#productionDetailsModal').modal('show');
            $('#editProductionBtn').data('production-id', productionId);
            $('#deleteProductionBtn').data('production-id', productionId);
        }).fail(function(error) {
            console.error('Error fetching production details:', error);
        });
    });

    $('#editProductionBtn').on('click', function() {
        const productionId = $(this).data('production-id');
        $.getJSON(`/productions/${productionId}`, function(data) {
            $('#addProductionModalLabel').text('Edit Production');
            $('#addProductionModal').find('form').attr('action',
                `/production/update/${productionId}`);

            $('#addProductionModal').find('input[name="name"]').val(data.name);
            $('#addProductionModal').find('select[name="shopify_location_id"]').val(data
                .shopify_location_id);
            $('#addProductionModal').find('select[name="user_id"]').val(data.user_id);
            $('#addProductionModal').find('input[name="due_date"]').val(data.due_date);

            // Clear previous product rows
            $('#products-container').empty();

            // Populate products
            data.production_product.forEach(product => {
                var selectedProduct = productsData.find(_product => _product.id == product.product.id);
                
                // Check if this is a refill or custom spray product
                const isRefill = doughBowlOrCustomCategoryIds.length > 0 && 
                    doughBowlOrCustomCategoryIds.includes(selectedProduct.category_id);

                let newRow;
                
                if (isRefill) {
                    const fragranceOptions = fragrances.map(f => 
                        `<option value="${f.id}" ${product.material_id === f.id ? 'selected' : ''}>${f.name}</option>`
                    ).join('');

                    // Create unit options for fragrance
                    const fragranceUnitOptions = weightUnits
                        .map(unit => `<option value="${unit.id}" ${product.material_unit_id === unit.id ? 'selected' : unit.id === defaultUnitId ? 'selected' : ''}>${unit.name}</option>`)
                        .join('');

                    newRow = $('<div>', {
                        class: 'row product-row mt-3'
                    }).html(`
                        <div class="col-md-4">
                            <label>Item</label>
                            <input type="hidden" name="product_id[]" value="${selectedProduct.id}">
                            <input type="hidden" name="is_refill[]" value="${selectedProduct.id}">
                            <input class="form-control" value="${selectedProduct.name}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input type="number" class="form-control product-quantity" 
                                name="product_quantity[]" value="${product.quantity}" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label>Fragrance</label>
                            <select class="form-control" name="fragrance_id[]" required>
                                ${fragranceOptions}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Fragrance Amount</label>
                            <div class="input-group">
                                <input type="number" class="form-control fragrance-amount-number" 
                                    value="${product.material_quantity}" name="fragrance_amount_number[]" step="0.1">
                                <select class="form-control fragrance-unit" name="fragrance_unit_id[]" required>
                                    ${fragranceUnitOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1" style="margin-top: 34px">
                            <button type="button" class="btn btn-secondary btn-sm remove-product-row">×</button>
                        </div>
                    `);
                } else {
                    // Regular product row
                    newRow = $('<div>', {
                        class: 'row product-row mt-3'
                    }).html(`
                        <div class="col-md-5">
                            <label>Item</label>
                            <input class="form-control unit-input" name="product_id[]" type="text" value="${selectedProduct.id}" hidden />
                            <input class="form-control unit-input" name="product_name" type="text" value="${selectedProduct.name}" readonly />
                        </div>
                        <div class="col-md-2">
                            <label>Current Level</label>
                            <input class="form-control" type="text" value="${selectedProduct.current_stock_level} ${selectedProduct.unit.name}" readonly />
                        </div>
                        <div class="col-md-2">
                            <label>Quantity</label>
                            <input class="form-control" name="product_quantity[]" type="text" value="${product.quantity}" required />
                        </div>
                        <div class="col-md-2">
                            <label>Unit Type</label>
                            <input class="form-control unit-input" name="unit" type="text" value="${selectedProduct.unit.name}" readonly />
                        </div>
                        <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                            <button type="button" class="btn btn-secondary btn-sm remove-product-row">×</button>
                        </div>
                    `);
                }

                $('#products-container').append(newRow);

                if (isRefill) {
                    // Add auto-calculation for refill rows
                    newRow.find('.product-quantity').on('input', function() {
                        const quantity = $(this).val();
                        const fragranceAmount = quantity * 2;
                        const $row = $(this).closest('.product-row');
                        $row.find('.fragrance-amount-number').val(fragranceAmount);
                    });
                }
            });

            // Hide the production detail modal and show the add modal
            $('#productionDetailsModal').modal('hide');
            $('#addProductionModal').on('shown.bs.modal', function() {
                $('body').addClass('modal-open');
            }).modal('show');
        }).fail(function(error) {
            console.error('Error fetching production details:', error);
        });
    });

    $('#deleteProductionBtn').on('click', function() {
        const productionId = $(this).data('production-id');
        if (confirm('Are you sure you want to delete this production?')) {
            $.ajax({
                url: `/production/delete/${productionId}`,
                type: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        alert('Production deleted successfully');
                        $('#productionDetailsModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Failed to delete production');
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    });
});
</script>
@endsection