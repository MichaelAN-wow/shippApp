

@extends('layouts.admin_master')

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<!-- Include jQuery and Bootstrap 4 JS -->
<script src="{{ asset('libs/jquery/js/jquery-3.5.1.min.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>

<!-- Include Timepicker CSS and JS (using Tempus Dominus) -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js">
</script>

<style>

    @media (max-width: 768px) {

        .input-group-text,
        .form-control {
            width: 100%;
            /* Full width on small screens */
            margin-bottom: 0.5rem;
            /* Adds space between stacked elements */
        }
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

        background-color: #c50c0c;
    }

    .category-label.paid {
        background-color: #008498;
    }

    .category-label.unpaid {
        background-color: #c50c0c;
    }

    .category-label.pending {
        background-color: #97e286
    }

    /* Add more classes as needed for other statuses */
    .category-label.fulfilled {
        background-color: #7ec06e;
    }

    .category-label.unfulfilled {
        background-color: gray;
    }
</style>
@php
    $salesTypes = [
        1 => 'Retail Sales',
        2 => 'Wholesale Sales',
        3 => 'Shopify',
    ];
@endphp
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Sales
        </div>
        <div class="card-dashboard-sub-header">
            <div class="card-dashboard-sub-header-title">
            @if(array_key_exists($categoryId, $salesTypes))
                {{ $salesTypes[$categoryId] }}
            @else
                All Sales
            @endif
            </div>
            <div class="card-dashboard-sub-header-controls d-flex justify-content-between align-items-center p-3 border-bottom">
    <!-- Yearly Goal, Total Sales, Daily Goal -->

<div class="d-flex gap-4 mb-2">
    <h5 class="mb-0 mr-4">
        <i class="fas fa-bullseye text-primary"></i>
        Yearly Goal: <span id="yearlyGoal">{{ $yearly_goal !== null ? '$' . number_format($yearly_goal, 0) : 'Not Set' }}</span>
    </h5>
    <h5 class="mb-0 mr-4">
        <i class="fas fa-chart-line text-success"></i>
        Total Sales This Year: <span>{{ '$' . number_format($totalSalesThisYear, 2) }}</span>
    </h5>
</div>


<div class="d-flex gap-4 mb-2">
    <h5 class="mb-0 mr-4">
        <i class="fas fa-forward text-info"></i>
        Projected Total: <span id="projectedTotal">{{ number_format($projectedTotal, 2) }}</span>
    </h5>
    <h5 class="mb-0 mr-4">
        <i class="fas fa-minus-circle text-danger"></i>
        Gap to Goal: <span id="gapToGoal">{{ number_format($gap, 2) }}</span>
    </h5>
</div>

<div class="d-flex gap-4">
    <h5 class="mb-0 mr-4">
        <i class="fas fa-calendar-alt text-warning"></i>
        Daily Goal: <span id="dailyGoal">{{ $daily_goal !== null ? '$' . number_format($daily_goal, 2) : 'Not Set' }}</span>
    </h5>
</div>

 <div class="d-flex align-items-center">
        <button type="button" class="btn btn-sm btn-primary mr-3" data-toggle="modal" data-target="#addSaleModal">
            <i class="fas fa-plus"></i> Add Sale
        </button>

        @if (Auth::user()->type == 'super_admin' || Auth::user()->type == 'admin')
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addYearlyTarget">
                <i class="fas fa-bullseye"></i> Set Yearly Target
            </button>
        @endif
    </div>
</div>

        </div>
<div class="card-body">
    <div class="table-responsive mx-auto" style="max-width: 1100px;">
        <table class="table table-bordered table-sm" id="dataTable" cellspacing="0" style="table-layout: fixed; width: 100%; font-size: 16px;">
            <thead>
                <tr>
                    <th style="width: 110px;">Sale #</th>
                    <th style="width: 90px;">Total</th>
                    <th style="width: 160px;">Status</th>
                    <th style="width: 120px;">Date</th>
                    <th style="width: 60px;">Type</th>
                </tr>
            </thead>
            <tbody id="table-data">
                @include('Admin.sales_table')
            </tbody>
        </table>
        <div class="ajax-load text-center" style="display:none">
            <span class="spinner-border text-primary" role="status"></span>
        </div>
    </div>
</div>
    <!-- Sale Details Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="saleDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleDetailsModalLabel">Sale Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 id="sale-id"></h6>
                        </div>
                        <div class="col-md-6" id="details_shopify_link">
                            <a href="https://{{ auth()->user()->company->shopify_domain }}/admin/orders/6070034989143" target="_blank"
                                rel="nofollow noreferrer" class="shopify-link" style="float: right;">
                                <img src="{{ asset('images/shopify.ca02b3a3da85bb570f6786752a5f08fc.svg') }}" alt="shopify"
                                    class="shopify-logo" width="28" height="28">
                                View on Shopify
                                <img src="{{ asset('images/open-new.57fc55864a4fad0b716b.svg') }}" alt="open in new tab"
                                    class="shopify-logo-open" width="24" height="24">
                            </a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="sale-details">
                            <!-- Sale details will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Sales Modal -->
    <div class="modal fade" id="customerSalesModal" tabindex="-1" role="dialog" aria-labelledby="customerSalesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerSalesModalLabel">Customer Sales</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="customer-name"></h6>
                    <p>Total Spent: $<span id="customer-total-spent"></span></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sale ID</th>
                                <th>Total($)</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="customer-sales-details">
                            <!-- Customer sales details will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSaleModal" tabindex="-1" role="dialog" aria-labelledby="addSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Add Sale</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ url('/sales/add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small mb-1" for="saleType">Sale Type</label>
                                    <select class="form-control" name="saleType">
                                        <option value="1" selected>Reail</option>
                                        <option value="2">WholeSale</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputLastName">Sale Date</label>
                                    <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                            name = "sale_date" data-target="#datetimepicker3"
                                            value="{{ date('Y-m-d H:i') }}" />
                                        <div class="input-group-append" data-target="#datetimepicker3"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="small mb-1" for="inputLastName">Customer Name</label>
                                <select class="form-control" name="customer_id">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->first_name }}
                                            {{ $customer->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="small mb-1" for="inputLastName">Add Another Product</label>
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
                                            <i class="fas fa-plus"></i> Add Product
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <div id="products-container">
                                <!-- Product row template -->

                            </div>

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
                                    <label class="col-3" for="inputShipping">Additional<br>Charges</label>
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
                                    <label class="small mb-1" for="inputFirstName">Notes</label>
                                    <input class="form-control" name="notes" type="text" placeholder="" />
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Shipping</label>
                                    <select class="form-control" name="saleType">
                                        <option value="1" selected>Free Local Pick</option>
                                        <option value="2">Shipped - Enter New Address</option>
                                    </select>
                                </div>
                            </div> --}}

                        </div>

                        <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block"
                                id="saveButton">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addYearlyTarget" tabindex="-1" role="dialog" aria-labelledby="addSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Add Yearly Target</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ url('/sales/addYearlyTarget') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="search-container">
                                <img src="{{ asset('images/svg/dollar.svg') }}" alt="Dollar Icon" class="search-icon">
                                <input class="form-control search-input" id="inputDiscounts" name="yearly_target" type="text"
                                    placeholder="0" aria-label="Discounts" step="any">
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
@endsection
@section('script')
    <script>
        var page = 1;

        var categoryId = {{ $categoryId }};

        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                loadMoreData(page);
            }
        });

        function loadMoreData(page) {
            $.ajax({
                    url: '/sales/pageScroll?page=' + page + "&categoryId=" + categoryId,
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
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    toastr.error('Server is not responding...');
                });
        }

        let productsAdded = false;

        function updateSaveButtonStatus() {
            const saveButton = document.getElementById('saveButton');
            if (productsAdded) {
                saveButton.disabled = false;
            } else {
                saveButton.disabled = true;
            }
        }

        function calculateTotal() {
            let total = 0;
            const subtotalInputs = document.querySelectorAll('[name="sub_total[]"]');
            productsAdded = subtotalInputs.length > 0 ? true : false;
            subtotalInputs.forEach(input => {
                const value = parseFloat(input.value.replace(/[^\d.-]/g, ''));
                total += isNaN(value) ? 0 : value;
            });

            const discountInputs = document.getElementById('inputDiscounts');
            const discountValue = parseFloat(discountInputs.value.replace(/[^\d.-]/g, ''));
            total -= isNaN(discountValue) ? 0 : discountValue;

            const taxInputs = document.getElementById('inputTax');
            const taxValue = parseFloat(taxInputs.value.replace(/[^\d.-]/g, ''));
            total += isNaN(taxValue) ? 0 : taxValue;

            const shippingInputs = document.getElementById('inputShipping');
            const shippingValue = parseFloat(shippingInputs.value.replace(/[^\d.-]/g, ''));
            total += isNaN(shippingValue) ? 0 : shippingValue;

            document.getElementById('inputTotal').value = parseToDecimal(total.toFixed(2));

            updateSaveButtonStatus();
        };

        updateSaveButtonStatus();

        document.getElementById('inputDiscounts').addEventListener('input', calculateTotal);
        document.getElementById('inputTax').addEventListener('input', calculateTotal);
        document.getElementById('inputShipping').addEventListener('input', calculateTotal);
        const onSaleIdClick = (saleId) => {

            $.ajax({
                url: '/sales/' + saleId,
                method: 'GET',
                success: function(response) {
                    var sale = response;
                    var isShopify = sale.sale_type == 'Shopify';
                    if (isShopify)
                        $('#sale-id').text('Sale ID: ' + sale.shopify_order_name);
                    else 
                        $('#sale-id').text('Sale ID: ' + sale.id);
                    if (isShopify) {
                        $("#details_shopify_link").show();
                        $('.shopify-link').attr('href',
                            "https://{{ auth()->user()->company->shopify_domain }}/admin/orders/" +
                            sale
                            .shopify_id);
                    } else {
                        $("#details_shopify_link").hide();
                    }

                    var totalSum = 0;
                    var saleDetails = '';

                    sale.items.forEach(function(item) {
                        var unitPrice = parseFloat(item
                            .unit_price); // Ensure unit_price is a number
                        var quantity = parseFloat(item
                            .quantity); // Ensure quantity is a number
                        var subtotal = quantity * unitPrice;
                        totalSum += subtotal;

                        var product_name = isShopify ? item.product_name :
                            item
                            .product.name;
                        saleDetails += `
                            <tr>
                                <td>${product_name}</td>
                                <td>${parseToDecimal(quantity)}</td>
                                <td>${parseToDecimal(unitPrice)}</td>
                                <td>${parseToDecimal(subtotal)}</td>
                            </tr>`;
                    });

                    saleDetails += `
                        <tr>
                            <td colspan="3" style="text-align:right;">Subtotal($):</td>
                            <td><strong>${parseToDecimal(totalSum)}</strong></td>
                        </tr>
                    `;

                    saleDetails += `
                        <tr>
                            <td colspan="3" style="text-align:right;">Tax($):</td>
                            <td><strong>${parseToDecimal(sale.tax)}</strong></td>
                        </tr>
                    `;

                    saleDetails += `
                        <tr>
                            <td colspan="3" style="text-align:right;">Discount(-$):</td>
                            <td><strong>${parseToDecimal(sale.discount)}</strong></td>
                        </tr>
                    `;

                    saleDetails += `
                        <tr>
                            <td colspan="3" style="text-align:right;"><strong>Total Cost($):</strong></td>
                            <td><strong>${parseToDecimal(sale.total)}</strong></td>
                        </tr>
                    `;
                    console.log(saleDetails);

                    $('#sale-details').html(saleDetails);
                    $('#saleDetailsModal').modal('show');
                }
            });

        }
        document.addEventListener('DOMContentLoaded', function() {




            var productsData = JSON.parse('@json($products)');

            document.getElementById('add-product').addEventListener('click', function() {
                var selectElement = document.querySelector('select[name="products"]');
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                var selectedId = selectedOption.value;

                var container = document.getElementById('products-container');
                var newRow = document.createElement('div');

                var selectedProduct = productsData.find(product => product.id == selectedId);

                newRow.className = 'row product-row mt-3';
                newRow.innerHTML = `
                <div class="col-md-3">
                    <label>Product</label>
                    <input class="form-control unit-input" name="product_id[]" type="text"
                    value="${selectedProduct.id}"
                    hidden />
                    <input class="form-control unit-input" name="product_name" type="text"
                    value="${selectedProduct.name}"
                    readonly />
                </div>
                <div class="col-md-2">
                    <label>Unit Price</label>
                    <input class="form-control unit-input" name="unit_price[]" type="text"
                    value="${selectedProduct.price}" />
                </div>
                <div class="col-md-2">
                    <label>Quantity</label>
                    <input class="form-control" name="product_quantity[]" type="text" value="1" required />
                </div>
                <div class="col-md-2">
                    <label>Unit Type</label>
                    <input class="form-control unit-input" name="unit" type="text"
                    value="${selectedProduct.unit.name}"
                    readonly />
                </div>
                <div class="col-md-2">
                    <label>Subtotal</label>
                    <input class="form-control unit-input" name="sub_total[]" type="text"
                    value="${selectedProduct.price}"
                    readonly />
                </div>
                <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                    <button type="button" class="btn btn-secondary btn-sm remove-product-row">
                        × 
                    </button>
                </div>
            `;

                container.appendChild(newRow);

                selectElement.removeChild(selectedOption);

                const quantityInput = newRow.querySelector('[name="product_quantity[]"]');
                const unitPriceInput = newRow.querySelector('[name="unit_price[]"]');
                const subtotalInput = newRow.querySelector('[name="sub_total[]"]');

                function calculateSubtotal() {
                    const quantity = parseInt(quantityInput.value);
                    const unitPrice = parseFloat(unitPriceInput.value.replace(/[^\d.-]/g, ''));
                    const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (quantity * unitPrice);
                    subtotalInput.value = subtotal.toFixed(2);

                    calculateTotal();
                }

                quantityInput.addEventListener('input', calculateSubtotal);
                unitPriceInput.addEventListener('input', calculateSubtotal);

                calculateTotal();
                updateSaveButtonStatus();
            });

            document.getElementById('products-container').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product-row')) {
                    var productRow = e.target.closest('.product-row');
                    var productNameInput = productRow.querySelector('input[name="product_name"]');
                    var productName = productNameInput.value;

                    var productIdInput = productRow.querySelector('input[name="product_id[]"]');
                    var productId = productIdInput.value;

                    e.target.closest('.product-row').remove();

                    var selectElement = document.querySelector('select[name="products"]');

                    var newOption = document.createElement('option');
                    newOption.value = productId;
                    newOption.textContent = productName;

                    selectElement.appendChild(newOption);

                    calculateTotal();
                }
            });

            document.body.addEventListener('change', function(event) {
                if (event.target.classList.contains('product-select')) {
                    var selectedOption = event.target.options[event.target.selectedIndex];
                    var unitString = selectedOption.getAttribute('data-unit');
                    var unitObject = JSON.parse(unitString);
                    var unitName = unitObject.name;
                    var unitInput = event.target.closest('.product-row').querySelector('.unit-input');
                    unitInput.value = unitName;
                }
            });

            document.body.addEventListener('click', function(event) {
                var target = event.target;
                while (target != document.body) {
                    if (target.classList.contains('delete-btn')) {
                        var salesId = target.dataset.id;

                        if (confirm('Are you sure you want to delete this sale?')) {
                            fetch('/sales/' + salesId, {
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
                                        event.target.closest('tr').remove();
                                        toastr.success('Success');
                                    } else {
                                        toastr.error('Error: ' + data.message);
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





            $('.customer-id').on('click', function(event) {
                event.preventDefault();
                var customerId = $(this).data('id');

                $.ajax({
                    url: '/customers/' + customerId + '/sales',
                    method: 'GET',
                    success: function(response) {
                        var customer = response.customer;
                        var sales = response.sales;
                        $('#customer-name').text(customer.name);
                        $('#customer-total-spent').text(response.total_spent.toFixed(2));

                        var customerSalesDetails = '';
                        sales.forEach(function(sale) {
                            customerSalesDetails += `
                            <tr>
                                <td>${sale.id}</td>
                                <td>${sale.total}</td>
                                <td>${sale.sale_date}</td>
                            </tr>
                        `;
                        });

                        $('#customer-sales-details').html(customerSalesDetails);
                        $('#customerSalesModal').modal('show');
                    }
                });
            });

            $('#addSaleButton').on('click', function() {
                $('#addSaleModalLabel').text('Add Sale');
                $('#addSaleModal form').attr('action', '{{ url('/sales/add') }}');
                $('#addSaleModal form').trigger("reset");
                $('#products-container').empty(); // Clear any existing product rows
                $('#addSaleModal form input[name="_method"]').remove(); // Remove any existing _method input
                $('#addSaleModal').modal('show');
            });

            $('.edit-btn').on('click', function() {
                var saleId = $(this).data('id');
                var productsData = JSON.parse('@json($products)');

                $.get('/sales/' + saleId + '/edit', function(response) {
                    var sale = response.sale;
                    var customer = response.customer;
                    var items = response.items;

                    $('#addSaleModalLabel').text('Edit Sale');
                    $('#addSaleModal form').attr('action', '/sales/' + sale
                        .id); // Update form action to edit URL
                    if (!$('#addSaleModal form input[name="_method"]').length) {
                        $('#addSaleModal form').append(
                            '<input type="hidden" name="_method" value="PUT">'
                        ); // Add _method input if not already present
                    }

                    $('#saleType').val(sale.sale_type);
                    //$('#datetimepicker3').data("DateTimePicker").date(new Date(sale.sale_date));
                    $('#customer_id').val(sale.customer_id).trigger('change');
                    $('#inputDiscounts').val(sale.discount);
                    $('#inputTax').val(sale.tax);
                    $('#inputShipping').val(sale.shipping_fee);
                    $('#inputTotal').val(sale.total);
                    $('#addSaleModal input[name="notes"]').val(sale.notes);

                    $('#products-container').empty();
                    items.forEach(function(item) {
                        var selectedProduct = productsData.find(product => product.id ==
                            item.product_id);

                        var newRow = document.createElement('div');
                        newRow.className = 'row product-row mt-3';
                        newRow.innerHTML = `
                                <div class="col-md-3">
                                    <label>Product</label>
                                    <input class="form-control unit-input" name="product_id[]" type="text"
                                    value="${selectedProduct.id}"
                                    hidden />
                                    <input class="form-control unit-input" name="product_name" type="text"
                                    value="${selectedProduct.name}"
                                    readonly />
                                </div>
                                <div class="col-md-2">
                                    <label>Unit Price</label>
                                    <input class="form-control unit-input" name="unit_price[]" type="text"
                                    value="${selectedProduct.price}" />
                                </div>
                                <div class="col-md-2">
                                    <label>Quantity</label>
                                    <input class="form-control" name="product_quantity[]" type="text" value="${item.quantity}" required />
                                </div>
                                <div class="col-md-2">
                                    <label>Unit Type</label>
                                    <input class="form-control unit-input" name="unit" type="text"
                                    value="${selectedProduct.unit.name}"
                                    readonly />
                                </div>
                                <div class="col-md-2">
                                    <label>Subtotal</label>
                                    <input class="form-control unit-input" name="sub_total[]" type="text"
                                    value="${(item.quantity * selectedProduct.price).toFixed(2)}"
                                    readonly />
                                </div>
                                <div class="col-md-1 d-flex align-items-center" style="margin-top: 30px">
                                    <button type="button" class="btn btn-secondary btn-sm remove-product-row">
                                        × 
                                    </button>
                                </div>
                            `;
                        $('#products-container').append(newRow);

                        const quantityInput = newRow.querySelector(
                            '[name="product_quantity[]"]');
                        const unitPriceInput = newRow.querySelector(
                            '[name="unit_price[]"]');
                        const subtotalInput = newRow.querySelector('[name="sub_total[]"]');

                        function calculateSubtotal() {
                            const quantity = parseFloat(quantityInput.value);
                            const unitPrice = parseFloat(unitPriceInput.value.replace(
                                /[^\d.-]/g, ''));
                            const subtotal = isNaN(quantity) || isNaN(unitPrice) ? 0 : (
                                quantity * unitPrice);
                            subtotalInput.value = subtotal.toFixed(2);
                            calculateTotal();
                        }

                        quantityInput.addEventListener('input', calculateSubtotal);
                        calculateSubtotal();
                    });

                    calculateTotal();

                    $('#addSaleModal').modal('show');
                });
            });

            $('#addSaleModal').on('hidden.bs.modal', function() {
                var modal = $(this);
                var form = modal.find('form');

                modal.find('.modal-title').text('Add Sale');
                form.attr('action', '{{ url('/sales/add') }}');
                form.trigger("reset");
                $('#products-container').empty();
                form.find('input[name="_method"]').remove();
            });

            // $('.delete-btn').on('click', function() {
            //     var saleId = $(this).data('id');

            //     if (confirm('Are you sure you want to delete this sale?')) {
            //         $.ajax({
            //             url: '/sales/' + saleId,
            //             method: 'DELETE',
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             },
            //             success: function(response) {
            //                 if (response.success) {
            //                     location.reload();
            //                 } else {
            //                     alert(response.message);
            //                 }
            //             }
            //         });
            //     }
            // });


        });
    </script>
@endsection
