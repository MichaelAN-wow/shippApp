@extends('layouts.admin_master')
@section('content')
    
    <link href="{{ asset('backend/css/reports.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="card-dashboard mb-4">
        <div class="card-dashboard-header">
            Reports
        </div>
        <div class="card-body">
            <div class="row">
                @include('Admin.reports_sales')
                @include('Admin.reports_materials')
                @include('Admin.reports_products')
                @include('Admin.reports_inventory_breakdown')

            </div>
        </div>
    </div>
@endsection
@section('script')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script type="text/javascript" src="{{ asset('libs/jquery-ui/1.12.1/popper.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/4.5.2/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        // Function to update the chart with new data

        var ctx_totalSales = document.getElementById('totalSalesChart').getContext('2d');
        var totalSalesChart = new Chart(ctx_totalSales, {
            type: 'line', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Sales',
                    data: [],
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            suggestedMin: 0
                        }
                    }]
                }
            }
        });

        var ctxNumberOfSales = document.getElementById('numberOfSalesChart').getContext('2d');
        var numberofSalesChart = new Chart(ctxNumberOfSales, {
            type: 'line', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Sales',
                    data: [],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxMaterialInventoryValues = document.getElementById('materialInventoryValueChart').getContext('2d');
        var materialInvnetoryValuesChart = new Chart(ctxMaterialInventoryValues, {
            type: 'line', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Inventory Value',
                    data: [],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxMaterialsPurchased = document.getElementById('numberOfMaterialPurchasedChart').getContext('2d');
        var materialsPurchasedChart = new Chart(ctxMaterialsPurchased, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Purchase Materials',
                    data: [],
                    fill: false,
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Purchase',
                    data: [],
                    type: 'line',
                    order: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxMaterialsProduced = document.getElementById('totalMaterialProducedChart').getContext('2d');
        var materialsProducedChart = new Chart(ctxMaterialsProduced, {
            type: 'bar', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Produced Materials',
                    data: [],
                    fill: false,
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Produced',
                    data: [],
                    type: 'line',
                    order: 1,
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxMaterialsUsed = document.getElementById('numberOfMaterialUsedChart').getContext('2d');
        var materialsUsedChart = new Chart(ctxMaterialsUsed, {
            type: 'bar', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Used Materials',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Used',
                    data: [],
                    type: 'line',
                    order: 1,
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });


        var ctxIProductInventoryValues = document.getElementById('productInventoryValueChart').getContext('2d');
        var productInventoryValuesChart = new Chart(ctxIProductInventoryValues, {
            type: 'line', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Inventory Value',
                    data: [],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxProductsPurchased = document.getElementById('numberOfProductPurchasedChart').getContext('2d');
        var productsPurchasedChart = new Chart(ctxProductsPurchased, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Purchase Products',
                    data: [],
                    fill: false,
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Purchase',
                    data: [],
                    type: 'line',
                    order: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxProductsProduced = document.getElementById('totalProductProducedChart').getContext('2d');
        var productsProducedChart = new Chart(ctxProductsProduced, {
            type: 'bar', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Produced Products',
                    data: [],
                    fill: false,
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Produced',
                    data: [],
                    type: 'line',
                    order: 1,
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctxProductsUsed = document.getElementById('numberOfProductUsedChart').getContext('2d');
        var productsUsedChart = new Chart(ctxProductsUsed, {
            type: 'bar', // Change to 'bar', 'pie', etc.
            data: {
                labels: [],
                datasets: [{
                    label: 'Used Products',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Number of Used',
                    data: [],
                    type: 'line',
                    order: 1,
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        $(function() {


            var start = moment().subtract(29, 'days');
            var end = moment();

            function sales_daterange_change(start, end) {
                $('#sales_daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                fetchSalesData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            }

            $('#sales_daterange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, sales_daterange_change);

            sales_daterange_change(start, end);

            //material report
            function materials_daterange_change(start, end) {
                $('#materials_daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                    'MMMM D, YYYY'));
                fetchMaterialsData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            }

            $('#materials_daterange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, materials_daterange_change);

            materials_daterange_change(start, end);

            //product report
            function products_daterange_change(start, end) {
                $('#products_daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                    'MMMM D, YYYY'));
                fetchProductsData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            }


            $('#products_daterange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, products_daterange_change);

            products_daterange_change(start, end);

            //Inventory Breakdown
            function inventory_breakdown_date_picker_change(start) {
                $('#inventory_breakdown_date_picker span').html(start.format('M/DD/YYYY hh:mm A'));
                fetchInventoryBreakdown(start.format('YYYY-MM-DD HH:mm:ss'));
            }

            $('#inventory_breakdown_date_picker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: true,
                startDate: moment().startOf('hour'),
            }, inventory_breakdown_date_picker_change);

            inventory_breakdown_date_picker_change(moment().startOf('hour'));

        });

        function fetchSalesData(startDate, endDate) {
            // Show loading indicator
            $('#loadingIndicator').show();

            $.ajax({
                url: '/reports/sales-data', // The URL to your ReportsController endpoint
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Hide loading indicator
                    $('#loadingIndicator').hide();

                    // Update the chart's labels and data
                    totalSalesChart.data.labels = response.table_data.map(item => item.date);
                    totalSalesChart.data.datasets[0].data = response.table_data.map(item => item.sales_total);
                    totalSalesChart.update();
                    let totalSum = response.table_data.reduce((sum, item) => sum + Number(item.sales_total), 0);
                    let formattedTotalSum = totalSum.toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });
                    document.querySelector('#totalSaleSum').innerHTML = formattedTotalSum;

                    numberofSalesChart.data.labels = response.table_data.map(item => item.date);
                    numberofSalesChart.data.datasets[0].data = response.table_data.map(item => item
                        .sales_count);
                    numberofSalesChart.update();
                    let numberOfSales = response.table_data.reduce((sum, item) => sum + Number(item
                        .sales_count), 0);
                    document.querySelector('#nuumberOfSales').innerHTML = numberOfSales;

                    const tbody = document.getElementById('tb_mostActiveCustomers');

                    // Clear the current contents of the table body
                    tbody.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.active_customers.forEach(customer => {
                        // Create a new row
                        const tr = document.createElement('tr');

                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.textContent = customer.customer ? customer.customer.first_name : '-';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = `${customer.number_of_sales} orders`;
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(customer.total_sales).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbody.appendChild(tr);
                    });

                    const tbodyPopularProducts = document.getElementById('tb_mostPopularProducts');

                    // Clear the current contents of the table body
                    tbodyPopularProducts.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.most_popular_products.forEach(products => {
                        // Create a new row
                        const tr = document.createElement('tr');
                        console.log(products);
                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.innerHTML =
                            '<a href="https://{{ auth()->user()->company->shopify_domain }}/admin/products/' + products
                            .shopify_id + "/variants/" + products.shopify_variants_id + '" target="_blank" rel="nofollow noreferrer">' + products
                            .product_name + '</a>';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = parseToDecimal(products.total_quantity).toString();
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(products.total_sales).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbodyPopularProducts.appendChild(tr);
                    });

                },
                error: function(xhr, status, error) {
                    // Hide loading indicator and show an error message
                    $('#loadingIndicator').hide();
                    console.error('An error occurred fetching the sales data:', error);
                }
            });
        }

        function fetchMaterialsData(startDate, endDate) {
            // Show loading indicator
            $('#loadingIndicator').show();

            $.ajax({
                url: '/reports/materials-data', // The URL to your ReportsController endpoint
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Hide loading indicator
                    $('#loadingIndicator').hide();

                    // Update the chart's labels and data

                    inventoryVolume = Number(response.inventoryVolume);
                    materialInvnetoryValuesChart.data.labels = response.inventory_value.map(item => item.date);

                    materialInvnetoryValuesChart.data.datasets[0].data = response.inventory_value.map(item => {
                        inventoryVolume += Number(item.material_total);
                        return inventoryVolume;
                    });
                    materialInvnetoryValuesChart.update();

                    let formattedTotalSum = Number(inventoryVolume).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });
                    document.querySelector('#inventoryValue').innerHTML = formattedTotalSum;

                    materialsPurchasedChart.data.labels = response.material_purchased.map(item => item.date);
                    materialsPurchasedChart.data.datasets[0].data = response.material_purchased.map(item => item
                        .material_total);
                    materialsPurchasedChart.data.datasets[1].data = response.material_purchased.map(item => item
                        .material_count);
                    materialsPurchasedChart.update();
                    let materialsPurchased = response.material_purchased.reduce((sum, item) => sum + Number(item
                        .material_total), 0);

                    let formattedMaterialsPurchased = Number(materialsPurchased).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#materialsPurchased').innerHTML = formattedMaterialsPurchased;


                    materialsProducedChart.data.labels = response.material_produced.map(item => item.date);
                    materialsProducedChart.data.datasets[0].data = response.material_produced.map(item => item
                        .material_total);
                    materialsProducedChart.data.datasets[1].data = response.material_produced.map(item => item
                        .material_count);
                    materialsProducedChart.update();
                    let materialsProduced = response.material_produced.reduce((sum, item) => sum + Number(item
                        .material_total), 0);

                    let formattedMaterialsProduced = Number(materialsProduced).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#materialsProduced').innerHTML = formattedMaterialsProduced;

                    materialsUsedChart.data.labels = response.material_used.map(item => item.date);
                    materialsUsedChart.data.datasets[0].data = response.material_used.map(item => item
                        .material_total);
                    materialsUsedChart.data.datasets[1].data = response.material_used.map(item => item
                        .material_count);
                    materialsUsedChart.update();
                    let materialsUsed = response.material_used.reduce((sum, item) => sum + Number(item
                        .material_total), 0);

                    let formattedMaterialUsed = Number(materialsUsed).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#materialsUsed').innerHTML = formattedMaterialUsed;

                    const tbody = document.getElementById('tb_TopMaterialsAdded');

                    // Clear the current contents of the table body
                    tbody.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.top_materials.forEach(material => {
                        // Create a new row
                        const tr = document.createElement('tr');

                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.textContent = material.material_name ? material.material_name : '-';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = parseToDecimal(material.total_quantity).toString() + ' ' +
                            material.unit_name;
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(material.total_costs).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbody.appendChild(tr);
                    });

                    const tbodySlowMovingMaterials = document.getElementById('tb_SlowMovingMaterials');

                    // Clear the current contents of the table body
                    tbodySlowMovingMaterials.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.slow_materials.forEach(material => {
                        // Create a new row
                        const tr = document.createElement('tr');

                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.textContent = material.material_name ? material.material_name : '-';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = parseToDecimal(material.total_quantity).toString() + ' ' +
                            material.unit_name;
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(products.total_costs).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbodySlowMovingMaterials.appendChild(tr);
                    });

                },
                error: function(xhr, status, error) {
                    // Hide loading indicator and show an error message
                    $('#loadingIndicator').hide();
                    console.error('An error occurred fetching the sales data:', error);
                }
            });
        }

        function fetchProductsData(startDate, endDate) {
            // Show loading indicator
            $('#loadingIndicator').show();

            $.ajax({
                url: '/reports/products-data', // The URL to your ReportsController endpoint
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    // Hide loading indicator
                    $('#loadingIndicator').hide();

                    // Update the chart's labels and data

                    inventoryVolume = Number(response.inventoryVolume);
                    productInventoryValuesChart.data.labels = response.inventory_value.map(item => item.date);

                    productInventoryValuesChart.data.datasets[0].data = response.inventory_value.map(item => {
                        inventoryVolume += Number(item.product_total);
                        return inventoryVolume;
                    });
                    productInventoryValuesChart.update();
                    //console.log(inventoryVolume);
                    let formattedTotalSum = Number(inventoryVolume).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });
                    document.querySelector('#productInventoryValue').innerHTML = formattedTotalSum;

                    productsPurchasedChart.data.labels = response.product_purchased.map(item => item.date);
                    productsPurchasedChart.data.datasets[0].data = response.product_purchased.map(item => item
                        .product_total);
                    productsPurchasedChart.data.datasets[1].data = response.product_purchased.map(item => item
                        .product_count);
                    productsPurchasedChart.update();
                    let productsPurchased = response.product_purchased.reduce((sum, item) => sum + Number(item
                        .product_total), 0);

                    let formattedProductsPurchased = Number(productsPurchased).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#productsPurchased').innerHTML = formattedProductsPurchased;


                    productsProducedChart.data.labels = response.product_produced.map(item => item.date);
                    productsProducedChart.data.datasets[0].data = response.product_produced.map(item => item
                        .product_total);
                    productsProducedChart.data.datasets[1].data = response.product_produced.map(item => item
                        .product_count);
                    productsProducedChart.update();
                    let productsProduced = response.product_produced.reduce((sum, item) => sum + Number(item
                        .product_total), 0);

                    let formattedProductsProduced = Number(productsProduced).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#productsProduced').innerHTML = formattedProductsProduced;

                    productsUsedChart.data.labels = response.product_used.map(item => item.date);
                    productsUsedChart.data.datasets[0].data = response.product_used.map(item => item
                        .product_total);
                    productsUsedChart.data.datasets[1].data = response.product_used.map(item => item
                        .product_count);
                    productsUsedChart.update();
                    let productsUsed = response.product_used.reduce((sum, item) => sum + Number(item
                        .product_total), 0);

                    let formattedProductUsed = Number(productsUsed).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    document.querySelector('#productsUsed').innerHTML = formattedProductUsed;

                    const tbody = document.getElementById('tb_TopProductsAdded');

                    // Clear the current contents of the table body
                    tbody.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.top_products.forEach(product => {
                        // Create a new row
                        const tr = document.createElement('tr');

                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.textContent = product.product_name ? product.product_name : '-';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = parseToDecimal(product.total_quantity).toString() + ' ' +
                            product.unit_name;
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(product.total_costs).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbody.appendChild(tr);
                    });

                    const tbodySlowMovingProducts = document.getElementById('tb_SlowMovingProducts');

                    // Clear the current contents of the table body
                    tbodySlowMovingProducts.innerHTML = '';

                    // Iterate over the active_customers array and create new rows
                    response.slow_products.forEach(product => {
                        // Create a new row
                        const tr = document.createElement('tr');

                        // Create and append the name cell
                        const nameTd = document.createElement('td');
                        nameTd.textContent = product.product_name ? product.product_name : '-';
                        tr.appendChild(nameTd);

                        // Create and append the number of sales cell
                        const numberTd = document.createElement('td');
                        numberTd.textContent = parseToDecimal(product.total_quantity).toString() + ' ' +
                            product.unit_name;
                        tr.appendChild(numberTd);

                        // Create and append the total sales cell
                        const totalTd = document.createElement('td');
                        let formattedTotal = Number(products.total_costs).toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        });
                        totalTd.textContent = formattedTotal;
                        tr.appendChild(totalTd);

                        // Append the new row to the table body
                        tbodySlowMovingProducts.appendChild(tr);
                    });

                },
                error: function(xhr, status, error) {
                    // Hide loading indicator and show an error message
                    $('#loadingIndicator').hide();
                    console.error('An error occurred fetching the sales data:', error);
                }
            });
        }


        function fetchInventoryBreakdown(startDate) {
            // Show loading indicator
            $('#loadingIndicator').show();
            console.log(startDate);
            $.ajax({
                url: '/reports/inventory-breakdown', // The URL to your ReportsController endpoint
                type: 'GET',
                data: {
                    start_date: startDate
                },
                success: function(response) {
                    // Hide loading indicator
                    $('#loadingIndicator').hide();

                    $('#inventoryBreakdownMaterials').empty();

                    var totalSum = 0;
                    // Update the chart's labels and data
                    $.each(response.materials, function(categoryName, items) {
                        // Calculate the total cost for the category
                        var categoryTotal = items.reduce(function(acc, item) {
                            totalSum += item.total_cost;
                            return acc + Number(item.total_cost);
                        }, 0).toFixed(2);

                        // Create the category row
                        var categoryRow = $(
                            '<tr data-toggle="collapse" data-target="#collapse' + categoryName
                            .replace(/\s+/g, '') +
                            '" aria-expanded="false" aria-controls="collapse' + categoryName
                            .replace(/\s+/g, '') + '" style="cursor: pointer">' +
                            '<td><span class="category-name" style="background-color: ' +
                            getColorForCategory(categoryName) + '">' +
                            categoryName + '</span></td>' +
                            '<td>$' + categoryTotal + '</td>' +
                            '</tr>'
                        );

                        // Append the category row to the table
                        $('#inventoryBreakdownMaterials').append(categoryRow);

                        // Create the subitem table for the category
                        var subitemTable = $(
                            '<tr id="collapse' + categoryName.replace(/\s+/g, '') +
                            '" class="collapse">' +
                            '<td colspan="2" style="padding: 0px; border-top: 0px;">' +
                            '<table class="table" style="margin-bottom: 0px;">' +
                            '<tbody>'
                        );

                        // Iterate over each item in the category
                        $.each(items, function(index, item) {
                            // Create the item row
                            var itemRow = $(
                                '<tr>' +
                                '<td><p class="resource-name"><a href="/reports/materials/' +
                                item.id + '/details">' + item.name + '</a></p></td>' +
                                '<td>$' + item.total_cost.toFixed(2) + '</td>' +
                                '</tr>'
                            );

                            // Append the item row to the subitem table
                            subitemTable.find('tbody').append(itemRow);
                        });

                        // Close the subitem table
                        subitemTable.append('</tbody></table></td></tr>');

                        // Append the subitem table to the main table
                        $('#inventoryBreakdownMaterials').append(subitemTable);
                    });

                    totalSum = totalSum.toFixed(2);

                    let formattedTotalSum = Number(totalSum).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });

                    // Update the total value in the div
                    $('#inventoryBreakdownMaterials_total').html(
                        '<p class="font-weight-bold" style="text-align: right; font-size: 1.5em;">Total Value:' +
                        formattedTotalSum + '</p>'
                    );

                    //products
                    $('#inventoryBreakdownProducts').empty();

                    totalSum = 0;
                    // Update the chart's labels and data
                    $.each(response.products, function(categoryName, items) {
                        // Calculate the total cost for the category
                        var categoryTotal = items.reduce(function(acc, item) {
                            totalSum += item.total_cost;
                            return acc + Number(item.total_cost);
                        }, 0).toFixed(2);

                        // Create the category row
                        var categoryRow = $(
                            '<tr data-toggle="collapse" data-target="#collapse' + categoryName
                            .replace(/\s+/g, '') +
                            '" aria-expanded="false" aria-controls="collapse' + categoryName
                            .replace(/\s+/g, '') + '" style="cursor: pointer">' +
                            '<td><span class="category-name" style="background-color: ' +
                            getColorForCategory(categoryName) + '">' +
                            categoryName + '</span></td>' +
                            '<td>$' + categoryTotal + '</td>' +
                            '</tr>'
                        );

                        // Append the category row to the table
                        $('#inventoryBreakdownProducts').append(categoryRow);

                        // Create the subitem table for the category
                        var subitemTable = $(
                            '<tr id="collapse' + categoryName.replace(/\s+/g, '') +
                            '" class="collapse">' +
                            '<td colspan="2" style="padding: 0px; border-top: 0px;">' +
                            '<table class="table" style="margin-bottom: 0px;">' +
                            '<tbody>'
                        );

                        // Iterate over each item in the category
                        $.each(items, function(index, item) {
                            // Create the item row
                            var itemRow = $(
                                '<tr>' +
                                '<td><p class="resource-name"><a href="/reports/products/' +
                                item.id + '/details">' + item.name + '</a></p></td>' +
                                '<td>$' + item.total_cost.toFixed(2) + '</td>' +
                                '</tr>'
                            );

                            // Append the item row to the subitem table
                            subitemTable.find('tbody').append(itemRow);
                        });

                        // Close the subitem table
                        subitemTable.append('</tbody></table></td></tr>');

                        // Append the subitem table to the main table
                        $('#inventoryBreakdownProducts').append(subitemTable);
                    });

                    totalSum = totalSum.toFixed(2);

                    formattedTotalSum = Number(totalSum).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });
                    // Update the total value in the div
                    $('#inventoryBreakdownProducts_total').html(
                        '<p class="font-weight-bold" style="text-align: right; font-size: 1.5em;">Total Value:' +
                        formattedTotalSum + '</p>'
                    );

                },
                error: function(xhr, status, error) {
                    // Hide loading indicator and show an error message
                    $('#loadingIndicator').hide();
                    console.error('An error occurred fetching the sales data:', error);
                }
            });
        }
    </script>
@endsection
