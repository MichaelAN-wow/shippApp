@extends('layouts.admin_master')

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.product-table {
    width: 100%;
}

.product-table th,
.product-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.product-table th {
    background-color: #f2f2f2;
}

.product-table th:nth-child(1),
.product-table td:nth-child(1) {
    width: 30%;
}

.product-table th:nth-child(2),
.product-table td:nth-child(2) {
    width: 20%;
}

.product-table th:nth-child(3),
.product-table td:nth-child(3) {
    width: 30%;
}

.product-table th:nth-child(4),
.product-table td:nth-child(4) {
    width: 20%;
}

.loading-pane {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.matched {
    background-color: #e0e0e0;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination button {
    margin: 0 5px;
}
</style>
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Linking products with Shopify
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="loading-pane" id="loading-pane">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Shopify Product</th>
                            <th>Stock Level</th>
                            <th>Product</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="product-matching-table">
                        <!-- Product rows will be appended here -->
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination">
                <!-- Pagination controls will be appended here -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        let localProducts = [];
        const localProductDict = {};

        let isFetching = false;
        let nextCursor = null;

        let localProductOptions = null;

        // Show loading pane
        $('#loading-pane').show();

        function createLocalProductOptions() {
            const options = $('<div>');
            options.append($('<option>').text('Select Product').val(''));

            // Sort local products by name
            const sortedLocalProducts = Object.values(localProductDict).sort((a, b) => a.name.localeCompare(b
                .name));

            sortedLocalProducts.forEach(function(localProduct) {
                options.append(
                    $('<option>').text(localProduct.name).val(localProduct.id)
                );
            });

            return options.children();
        }

        // Fetch local products
        $.get('/local-products', function(data) {
            localProducts = data;

            // Populate localProductDict for quick lookup
            localProducts.forEach(function(localProduct) {
                localProductDict[localProduct.id] = localProduct;
            });

            // Fetch Shopify products and populate the table
            fetchAllShopifyProducts();
            localProductOptions = createLocalProductOptions();
        });


        function fetchAllShopifyProducts(cursor = null) {
            if (isFetching) return;
            isFetching = true;

            const url = cursor ? `/shopify-products?cursor=${cursor}` : `/shopify-products`;
            $.get(url, function(data) {

                appendProducts(data.products);
                nextCursor = data.next_cursor;
                isFetching = false;

                if (nextCursor) {
                    fetchAllShopifyProducts(nextCursor); // Continue fetching next batch
                } else {
                    // Hide loading pane when done
                    $('#loading-pane').hide();
                }

            }).fail(function() {
                isFetching = false; // Reset fetching status on failure
            });
        }

        function extractNumericId(gid) {
            const parts = gid.split('/');
            return parts[parts.length - 1];
        }


        function appendProducts(products) {
            const rows = products.map(function(product) {
                const shopify_id = extractNumericId(product.id);
                const variants_id = extractNumericId(product.variants_id);

                const selectElement = $('<select>')
                    .addClass('form-control local-product-select')
                    .append(localProductOptions.clone())
                    .data('shopify-id', shopify_id)
                    .data('variants-id', variants_id);

                const row = $('<tr>').append(
                    $('<td>').text(product.name),
                    $('<td>').text(product.stock_level + ' pieces'),
                    $('<td>').append(selectElement),
                    $('<td>').append(
                        $('<button>').addClass('btn btn-primary save-button').text('Save')
                        .data('shopify-id', shopify_id).data('variants-id', variants_id)
                    )
                );

                // Mark matched products and set row as grey
                const shopifyProductId = selectElement.data('shopify-id');

                const matchedProduct = localProducts.find(localProduct => localProduct.shopify_id ==
                    shopifyProductId);

                if (matchedProduct) {
                    selectElement.val(matchedProduct.id).trigger('change');
                    row.addClass('matched');
                }

                return row;
            });

            // Append all rows at once to minimize DOM manipulation
            $('#product-matching-table').append(rows);

            // Initialize Select2 for the newly added selects
            $('.local-product-select').select2({
                placeholder: 'Select Product',
                width: '100%'
            });
        }

        // Handle save button click
        $(document).on('click', '.save-button', function() {
            const shopifyProductId = $(this).data('shopify-id');
            const shopifyVariantsId = $(this).data('variants-id');
            const localProductId = $(this).closest('tr').find('select').val();

            if (!localProductId) {
                alert('Please select a product to link.');
                return;
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/save-product-match',
                type: 'POST',
                data: {
                    shopify_product_id: shopifyProductId,
                    shopify_variants_id: shopifyVariantsId,
                    local_product_id: localProductId,
                    _token: csrfToken
                },
                success: function(response) {
                    alert(response.message);
                }
            });
        });
    });
</script>
@endsection
