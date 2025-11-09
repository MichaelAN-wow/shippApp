@extends('layouts.admin_master')


<style>
    .quantity-wrapper {
        position: relative;
    }

    .arrow-wrapper {
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        display: none;
    }

    .quantity-input:hover+.arrow-wrapper,
    .arrow-wrapper:hover {
        display: block;
    }

    .arrow {
        cursor: pointer;
        background-color: #ccc;
        color: #333;
        text-align: center;
        line-height: 1.2;
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }

    .up-arrow {
        margin-bottom: 5px;
    }

    .down-arrow {
        margin-top: 5px;
    }
</style>
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Orders List
        </div>
        <div class="card-dashboard-sub-header">
            <div class="card-dashboard-sub-header-controls">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#addSupplyModal">
                        <i class="fas fa-plus"></i> Add Supply Order
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order Id</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Items</th>
                            <th>Date Received</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-data">
                        @include('Admin.orders_table')
                    </tbody>
                </table>
                <div class="ajax-load text-center" style="display:none">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the modal to show order details -->
    <div class="modal fade" id="addSupplyModal" tabindex="-1" role="dialog" aria-labelledby="addSupplyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplyModalLabel">Add Supply Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ url('/orders/add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplierSelect">
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="0">Draft</option>
                                        <option value="1">Placed</option>
                                        <option selected value="2">Received</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Received At</label>
                                    <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" name="received_at"
                                            data-target="#datetimepicker3" value="{{ date('Y-m-d H:i') }}" />
                                        <div class="input-group-append" data-target="#datetimepicker3"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="materials" id="materialSelect">
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="add-material" class="btn btn-primary w-100">
                                    <i class="fas fa-plus"></i> Add Material
                                </button>
                            </div>
                            <div id="materials-container"></div>
                            <div class="col-md-6 offset-6" style="margin-top: 20px">
                                <div class="form-group row">
                                    <label class="col-3" for="inputDiscounts">Discounts</label>
                                    <div class="search-container">
                                        <img src="{{ asset('images/svg/dollar.svg') }}" alt="Dollar Icon" class="search-icon">
                                        <input class="form-control search-input" id="inputDiscounts" name="discount" type="text"
                                            placeholder="0" aria-label="Discounts" step="any">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 offset-6">
                                <div class="form-group row">
                                    <label class="col-3" for="inputTax">Tax</label>
                                    <div class="search-container">
                                        <img src="{{ asset('images/svg/dollar.svg') }}" alt="Dollar Icon" class="search-icon">
                                        <input class="form-control search-input" id="inputTax" type="text" name="tax"
                                            placeholder="0" aria-label="Tax" step="any">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 offset-6">
                                <div class="form-group row">
                                    <label class="col-3" for="inputShipping">Shipping</label>
                                    <div class="search-container">
                                        <img src="{{ asset('images/svg/dollar.svg') }}" alt="Dollar Icon" class="search-icon">
                                        <input class="form-control search-input" id="inputShipping" name="shipping" type="text"
                                            placeholder="0" aria-label="Additional Charges" step="any">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 offset-6">
                                <div class="form-group row">
                                    <label class="col-3" for="inputTotal"><b>Total</b></label>
                                    <div class="search-container">
                                        <img src="{{ asset('images/svg/dollar.svg') }}" alt="Dollar Icon" class="search-icon">
                                        <input class="form-control search-input" id="inputTotal" name="total" type="text"
                                            placeholder="0" aria-label="Discounts" step="any">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Order Id</label>
                                    <input class="form-control" name="order_number" type="text" placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Notes</label>
                                    <input class="form-control" name="notes" type="text" placeholder="" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog"
        aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Order details will be loaded here -->
                    <div class="supplier-info">
                        <h5>Supplier Information</h5>
                        <p><strong>Name:</strong> <span id="supplier-name"></span></p>
                        <p><strong>Email:</strong> <span id="supplier-email"></span></p>
                    </div>
                    <hr>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="order-details-body">
                            <!-- Dynamic rows will be added here -->
                        </tbody>
                    </table>
                    <div>Total: $<span id="order-total"></span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var page = 1;
        // $(window).scroll(function() {
        //     if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
        //         page++;
        //         loadMoreData(page);
        //     }
        // });

        var materialsData = JSON.parse('@json($materials)');
        var unitsData = JSON.parse('@json($unitsJson)');

        function loadMoreData(page) {
            $.ajax({
                    url: '/orders/pageScroll?page=' + page,
                    type: "get",
                    beforeSend: function() {
                        $('.ajax-load').show();
                    }
                })
                .done(function(data) {
                    if (data.trim() === "") {
                        $('.ajax-load').html("No more records found");
                        return;
                    }
                    $('.ajax-load').hide();
                    $("#table-data").append(data);
                    rebindEventListeners();
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    toastr.error('Server is not responding...');
                });
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

        function calculateTotal() {
            let total = 0;
            const subtotalInputs = document.querySelectorAll('[name="sub_total[]"]');
            subtotalInputs.forEach(input => {
                const value = parseFloat(input.value.replace(/[^\d.-]/g, ''));
                total += isNaN(value) ? 0 : value;
            });

            const discountValue = parseFloat(document.getElementById('inputDiscounts').value.replace(/[^\d.-]/g, ''));
            total -= isNaN(discountValue) ? 0 : discountValue;

            const taxValue = parseFloat(document.getElementById('inputTax').value.replace(/[^\d.-]/g, ''));
            total += isNaN(taxValue) ? 0 : taxValue;

            const shippingValue = parseFloat(document.getElementById('inputShipping').value.replace(/[^\d.-]/g, ''));
            total += isNaN(shippingValue) ? 0 : shippingValue;

            document.getElementById('inputTotal').value = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(total);
        }

        document.getElementById('inputDiscounts').addEventListener('input', calculateTotal);
        document.getElementById('inputTax').addEventListener('input', calculateTotal);
        document.getElementById('inputShipping').addEventListener('input', calculateTotal);

        document.addEventListener('DOMContentLoaded', function() {
            

            document.getElementById('add-material').addEventListener('click', function() {
                var selectElement = document.querySelector('select[name="materials"]');
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                var selectedId = selectedOption.value;

                var container = document.getElementById('materials-container');
                var newRow = document.createElement('div');

                var selectedMaterial = materialsData.find(material => material.id == selectedId);
                newRow.className = 'form-row material-row mt-3';
                newRow.innerHTML = `
                <div class="col-md-2">
                    <label>Material</label>
                    <input class="form-control unit-input" name="material_id[]" type="text" value="${selectedMaterial.id}" hidden />
                    <input class="form-control unit-input" name="material_name" type="text" value="${selectedMaterial.name}" readonly />
                </div>
                <div class="col-md-1">
                    <label>Price($)</label>
                    <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(selectedMaterial.price_per_unit)}" />
                </div>
                <div class="col-md-2">
                    <label>Unit Name</label>
                    <input class="form-control unit-input" type="text" value="${selectedMaterial.unit.name}" readonly />
                </div>
                <div class="col-md-2">
                    <label>Quantity</label>
                    <input class="form-control material-quantity" name="material_count[]" type="number" value="0.01" step="any" required />
                </div>
                <div class="col-md-2">
                    <label>Unit Type</label>
                    <select class="form-control unit-input" name="unit"></select>
                    <input class="form-control" name="material_unit_id[]" type="hidden" value="${selectedMaterial.unit_id}" />
                    <input class="form-control" name="material_unit_factor[]" type="hidden" value="1" />
                </div>
                <div class="col-md-2">
                    <label>Subtotal</label>
                    <input class="form-control unit-input material-subtotal" name="sub_total[]" type="text" value="${selectedMaterial.price_per_unit}" readonly />
                </div>
                <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                    <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
                </div>
            `;

                container.appendChild(newRow);
                var unitsSelect = newRow.querySelector('select[name="unit"]');
                populateUnitsSelect(unitsSelect, selectedMaterial, selectedMaterial.unit_id);

                selectElement.removeChild(selectedOption);

                const quantityInput = newRow.querySelector('[name="material_count[]"]');
                const unitPriceInput = newRow.querySelector('[name="unit_price[]"]');
                const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
                var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');
                var unitFactorInput = newRow.querySelector('input[name="material_unit_factor[]"]');

                const originalUnit = selectedMaterial.unit.name;
                const originalConversionFactor = originalUnit === 'pieces' ? 1 : selectedMaterial.unit.conversion_factor;

                function calculateSubtotal() {
                    unitIdInput.value = unitsSelect.value;
                    const quantity = parseFloat(quantityInput.value);
                    const unitPrice = parseFloat(unitPriceInput.value.replace(
                        /[^\d.-]/g, ''));
                    const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
                    const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) ||
                        1;
                    unitFactorInput.value = selectedConversionFactor / originalConversionFactor;
                    const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice *
                        selectedConversionFactor / originalConversionFactor);
                    
                    subtotalInput.value = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(parseToDecimal(subtotal));
                    calculateTotal();
                }
                unitPriceInput.addEventListener('input', calculateSubtotal);
                quantityInput.addEventListener('input', calculateSubtotal);
                unitsSelect.addEventListener('change', calculateSubtotal);
                calculateSubtotal();
            });

            document.getElementById('materials-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-material-row')) {
                    var materialRow = e.target.closest('.material-row');
                    var materialId = materialRow.querySelector('input[name="material_id[]"]').value;
                    var materialName = materialRow.querySelector('input[name="material_name"]').value;

                    materialRow.remove();

                    var selectElement = document.querySelector('select[name="materials"]');
                    var newOption = document.createElement('option');
                    newOption.value = materialId;
                    newOption.textContent = materialName;
                    selectElement.appendChild(newOption);

                    calculateTotal();
                }
            });

            document.body.addEventListener('click', function(event) {
                var target = event.target;
                while (target != document.body) {
                    if (target.classList.contains('delete-btn')) {
                        var productId = target.dataset.id;
                        if (confirm('Are you sure you want to delete this order?')) {
                            fetch('/orders/' + productId, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json'
                                    },
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        target.closest('tr').remove();
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

            // var supplierNameElements = document.querySelectorAll('.supplier-name');
            // supplierNameElements.forEach(function(element) {
            //     element.addEventListener('click', function(event) {
            //         event.preventDefault();
            //         var orderId = this.getAttribute('data-id');

            //         fetch(`/orders/${orderId}/details`)
            //             .then(response => response.json())
            //             .then(data => {
            //                 var orderDetailsBody = document.getElementById(
            //                     'order-details-body');
            //                 orderDetailsBody.innerHTML = ''; // Clear existing rows
            //                 var total = 0;

            //                 document.getElementById('supplier-name').innerText = data.order
            //                     .supplier.name;
            //                 document.getElementById('supplier-email').innerText = data.order
            //                     .supplier.email;

            //                 data.order.order_materials.forEach(function(detail) {
            //                     var row = document.createElement('tr');

            //                     var cellMaterial = document.createElement('td');
            //                     cellMaterial.innerText = detail.material.name;
            //                     row.appendChild(cellMaterial);

            //                     var cellUnitPrice = document.createElement('td');
            //                     cellUnitPrice.innerText = `$${detail.unit_price}`;
            //                     row.appendChild(cellUnitPrice);

            //                     var cellQuantity = document.createElement('td');
            //                     cellQuantity.innerText = detail.quantity;
            //                     row.appendChild(cellQuantity);

            //                     var cellTotalPrice = document.createElement('td');
            //                     cellTotalPrice.innerText =
            //                         `$${(detail.unit_price * detail.quantity).toFixed(2)}`;
            //                     row.appendChild(cellTotalPrice);

            //                     orderDetailsBody.appendChild(row);
            //                     total += detail.unit_price * detail.quantity;
            //                 });

            //                 document.getElementById('order-total').innerText = total.toFixed(2);
            //                 $('#orderDetailsModal').modal('show');
            //             })
            //             .catch(error => console.error('Error:', error));
            //     });
            // });

            $('.edit-btn').on('click', function(event) {
                event.preventDefault();
                var orderId = $(this).data('id');

                fetch(`/orders/${orderId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        var order = data.order;
                        var units = data.units;

                        $('#addSupplyModalLabel').text('Edit Supply Order');
                        $('form').attr('action', `/orders/${order.id}/edit`);

                        $('select[name="supplier_id"]').val(order.supplier_id).trigger('change');
                        $('select[name="status"]').val(order.status);
                        $('input[name="received_at"]').val(order.received_at);
                        $('input[name="discount"]').val(parseToDecimal(order.discount));
                        $('input[name="tax"]').val(parseToDecimal(order.tax));
                        $('input[name="shipping"]').val(parseToDecimal(order.shipping));
                        $('input[name="total"]').val(order.total);
                        $('input[name="order_number"]').val(order.order_number);
                        $('input[name="notes"]').val(order.notes);

                        var materialsContainer = $('#materials-container');
                        materialsContainer.empty(); // Clear existing materials

                        order.order_materials.forEach(function(material) {
                            var selectedMaterial = material.material;
                            var unitName = units.find(unit => unit.id === selectedMaterial
                                .unit_id).name;

                            var newRow = document.createElement('div');
                            newRow.className = 'form-row material-row mt-3';
                            newRow.innerHTML = `
                            <div class="col-md-2">
                                <label>Material</label>
                                <input class="form-control unit-input" name="material_id[]" type="text" value="${selectedMaterial.id}" hidden />
                                <input class="form-control unit-input" name="material_name" type="text" value="${selectedMaterial.name}" readonly />
                            </div>
                            <div class="col-md-1">
                                <label>Price($)</label>
                                <input class="form-control unit-input" name="unit_price[]" type="text" value="${parseToDecimal(material.unit_price)}" />
                            </div>
                            <div class="col-md-2">
                                <label>Unit Name</label>
                                <input class="form-control unit-input" type="text" value="${unitName}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label>Quantity</label>
                                <input class="form-control material-quantity" name="material_count[]" type="number" value="${material.quantity}" step="0.01" required />
                            </div>
                            <div class="col-md-2">
                                <label>Unit Type</label>
                                <select class="form-control unit-input" name="unit"></select>
                                <input class="form-control" name="material_unit_id[]" type="hidden" value="${material.unit_id}" />
                                <input class="form-control" name="material_unit_factor[]" type="hidden" value="1" />
                            </div>
                            <div class="col-md-2">
                                <label>Subtotal</label>
                                <input class="form-control unit-input material-subtotal" name="sub_total[]" type="text" value="${(material.unit_price * material.quantity).toFixed(2)}" readonly />
                            </div>
                            <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                                <button type="button" class="btn btn-secondary btn-sm remove-material-row">×</button>
                            </div>
                        `;
                            materialsContainer.append(newRow);

                            var unitsSelect = newRow.querySelector('select[name="unit"]');
                            var selectedMaterial = materialsData.find(_material => _material.id ==
                                    material.material_id);
                            populateUnitsSelect(unitsSelect, selectedMaterial, material.unit_id);

                            const quantityInput = newRow.querySelector(
                                '[name="material_count[]"]');
                            const unitPriceInput = newRow.querySelector(
                                '[name="unit_price[]"]');
                            var unitIdInput = newRow.querySelector('input[name="material_unit_id[]"]');
                            var unitFactorInput = newRow.querySelector('input[name="material_unit_factor[]"]');
                            const subtotalInput = newRow.querySelector('[name="sub_total[]"]');
                            const originalUnit = selectedMaterial.unit.name;
                            const originalConversionFactor = originalUnit === 'pieces' ? 1 : selectedMaterial.unit.conversion_factor;

                            function calculateSubtotal() {
                                unitIdInput.value = unitsSelect.value;
                                const quantity = parseFloat(quantityInput.value);
                                const unitPrice = parseFloat(unitPriceInput.value.replace(
                                    /[^\d.-]/g, ''));
                                const selectedOption = unitsSelect.options[unitsSelect.selectedIndex];
                                const selectedConversionFactor = parseFloat(selectedOption.dataset.conversionFactor) ||
                                    1;
                                unitFactorInput.value = selectedConversionFactor / originalConversionFactor;
                                const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice *
                                    selectedConversionFactor / originalConversionFactor);
                               
                                subtotalInput.value = new Intl.NumberFormat('en-US', {
                                    style: 'currency',
                                    currency: 'USD'
                                }).format(parseToDecimal(subtotal));
                                calculateTotal();
                            }
                            unitPriceInput.addEventListener('input', calculateSubtotal);
                            quantityInput.addEventListener('input', calculateSubtotal);
                            unitsSelect.addEventListener('change', calculateSubtotal);
                            calculateSubtotal();
                        });

                        $('#addSupplyModal').modal('show');
                    })
                    .catch(error => console.error('Error:', error));
            });

            $('#addSupplyModal').on('hidden.bs.modal', function() {
                var modal = document.getElementById('addSupplyModal');
                var modalTitle = modal.querySelector('.modal-title');
                var form = modal.querySelector('form');

                modalTitle.textContent = 'Add Supply Order';
                form.action = '/orders/add';
                form.reset();
                $('#supplierSelect').val('').trigger('change');
                $('#categorySelect').val('').trigger('change');
                $('#materials-container').empty(); // Clear material rows
            });

            $('#materials-container').on('click', '.remove-material-row', function() {
                $(this).closest('.material-row').remove();
                calculateTotal();
            });
        });

        $(document).ready(function() {
            $('.order-id').on('click', function(event) {
                event.preventDefault();
                var orderId = $(this).data('id');

                $.ajax({
                    url: '/orders/' + orderId + '/details',
                    method: 'GET',
                    success: function(response) {
                        var order = response.order;
                        var total = 0;
                        var orderDetails = '';

                        $('#supplier-name').text(order.supplier.name);
                        $('#supplier-email').text(order.supplier.email);

                        order.order_materials.forEach(function(detail) {

                            orderDetails += `
                                <tr>
                                    <td>${detail.material.name}</td>
                                    <td>$${detail.unit_price}</td>
                                    <td>${detail.quantity}</td>
                                    <td>$${(detail.unit_price * detail.quantity).toFixed(2)}</td>
                                </tr>
                            `;
                            total += detail.unit_price * detail.quantity;
                        });

                        $('#order-details-body').html(orderDetails);
                        $('#order-total').text(total.toFixed(2));
                        $('#orderDetailsModal').modal('show');
                    }
                });
            });

            function rebindEventListeners() {
                $('.order-id').off('click').on('click', function(event) {
                    event.preventDefault();
                    var orderId = $(this).data('id');

                    $.ajax({
                        url: '/orders/' + orderId + '/details',
                        method: 'GET',
                        success: function(response) {
                            var order = response.order;
                            var total = 0;
                            var orderDetails = '';

                            $('#supplier-name').text(order.supplier.name);
                            $('#supplier-email').text(order.supplier.email);

                            order.order_materials.forEach(function(detail) {
                                var rowClass = parseFloat(detail.material
                                    .current_stock_level) < parseFloat(detail
                                    .material.min_stock_level) ? 'text-danger' : '';

                                orderDetails += `
                        <tr class="${rowClass}">
                            <td>${detail.material.name}</td>
                            <td>${detail.quantity}</td>
                            <td>${detail.material.current_stock_level}</td>
                            <td>$${detail.unit_price}</td>
                            <td>$${(detail.unit_price * detail.quantity).toFixed(2)}</td>
                        </tr>
                    `;
                                total += detail.unit_price * detail.quantity;
                            });

                            $('#order-details-body').html(orderDetails);
                            $('#order-total').text(total.toFixed(2));
                            $('#orderDetailsModal').modal('show');
                        }
                    });
                });
            }

            const form = document.querySelector('#addSupplyModal form');
            form.addEventListener('submit', function (event) {
                const materialsContainer = document.querySelector('#materials-container');
                if (!materialsContainer.hasChildNodes()) {
                    // If no child elements are found, prevent form submission and show alert
                    event.preventDefault();
                    toastr.error('Please add at least one material before submitting the form.');
                }
            });
        });
    </script>

    @if(session('success'))
        <script type="text/javascript">
            $(document).ready(function() {
                toastr.success('{{ session('success') }}');
            });
        </script>
    @endif
@endsection
