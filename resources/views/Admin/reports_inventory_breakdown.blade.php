<div class="col-lg-12">
    <div class="card card-reports">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Inventory Breakdown</h5>
                </div>
                <div class="col-md-4">
                    <div id="inventory_breakdown_date_picker" class="float-right calendar-daterange">
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
                <div class="col-6">
                    <div class="stock-value-table">
                        <h5>Materials</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryBreakdownMaterials">
                                
                            </tbody>
                        </table>
                        <div id="inventoryBreakdownMaterials_total">
                            
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="stock-value-table">
                        <h5>Products</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryBreakdownProducts">
                               
                            </tbody>
                        </table>
                        <div id="inventoryBreakdownProducts_total">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
