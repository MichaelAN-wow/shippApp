<div class="col-lg-12">
    <div class="card card-reports">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Sales Reports</h5>
                </div>
                <div class="col-md-4">
                    <div id="sales_daterange" class="float-right calendar-daterange">
                        <span></span>
                        <img src="{{ asset('images/svg/down_arrow.svg') }}" alt="Arrow Icon" class="arrow-icon">
                        <img src="{{ asset('images/svg/calendar.svg') }}" alt="Calendar Icon" class="calendar-icon">
                    </div>
                    {{-- <input type="text" class="form-control" id="sales_daterange" name="daterange"
                        value="{{ date('m/d/Y') }} - {{ date('m/d/Y') }}" /> --}}
                </div>
                <div id="loadingIndicator" style="display: none;">Loading...</div>
                {{-- <div class="col-md-1">
                    <button type="button" class="btn btn-success active" style="border-radius: 1.5rem;">Weekly</button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-toggle" style="border-radius: 1.5rem;">Monthly</button>
                </div> --}}
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Total Sales</div>
                        <div class="card-body">
                            <h5 id="totalSaleSum">$0</h5>
                            <canvas id="totalSalesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Number of Sales</div>
                        <div class="card-body">
                            <h5 id="nuumberOfSales">0</h5>
                            <canvas id="numberOfSalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Most Popular Products</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_mostPopularProducts">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Most Active Customers</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Number of Orders</th>
                                            <th>Total Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_mostActiveCustomers">
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
