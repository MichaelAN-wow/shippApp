<div class="col-lg-12">
    <div class="card card-reports">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Product Reports</h5>
                </div>
                <div class="col-md-4">
                    <div id="products_daterange" class="float-right calendar-daterange">
                        <span></span>
                        <img src="{{ asset('images/svg/down_arrow.svg') }}" alt="Arrow Icon" class="arrow-icon">
                        <img src="{{ asset('images/svg/calendar.svg') }}" alt="Calendar Icon" class="calendar-icon">
                    </div>
                    {{-- <input type="text" class="form-control" id="material_daterange" name="daterange"
                        value="{{ date('m/d/Y') }} - {{ date('m/d/Y') }}" /> --}}
                </div>
                <div id="loadingIndicator" style="display: none;">Loading...</div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Inventory Value</div>
                        <div class="card-body">
                            <h5 id="productInventoryValue">$0</h5>
                            <canvas id="productInventoryValueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Products Purchased</div>
                        <div class="card-body">
                            <h5 id="productsPurchased">0</h5>
                            <canvas id="numberOfProductPurchasedChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Products Produced</div>
                        <div class="card-body">
                            <h5 id="productsProduced">$0</h5>
                            <canvas id="totalProductProducedChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Products Used</div>
                        <div class="card-body">
                            <h5 id="productsUsed">$0</h5>
                            <canvas id="numberOfProductUsedChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Top Products Added</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_TopProductsAdded">
                                        {{-- <tr>
                                            <td><a href="/reports/products/poC4KgCjj5Ama8dHJhZcq2/details">Eco
                                                    Soya CB Advanced</a></td>
                                            <td>396 lbs</td>
                                            <td>$1,576.54</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/kNwRQWtVjsuU9TcArL3248/details">Room
                                                    Spray Bottle</a></td>
                                            <td>800 pieces</td>
                                            <td>$770.00</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/1oFbQKpc2w3mz6bUxKEXGk/details">Single
                                                    Wick Jars</a></td>
                                            <td>355 pieces</td>
                                            <td>$323.05</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/noxJuHzhfH7GGQu5ZdbrSk/details">Soap
                                                    Bottle</a></td>
                                            <td>59 pieces</td>
                                            <td>$115.07</td>
                                        </tr>
                                        <tr>
                                            <td><a
                                                    href="/reports/products/mDDMaTnEPPCmLQN81BPh2c/details">Bobbing</a>
                                            </td>
                                            <td>120 oz</td>
                                            <td>$7.38</td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Slow Moving Products</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_SlowMovingProducts">
                                        <tr>
                                            <td><a href="/reports/products/poC4KgCjj5Ama8dHJhZcq2/details">Eco
                                                    Soya CB Advanced</a></td>
                                            <td>396 lbs</td>
                                            <td>$1,576.54</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/kNwRQWtVjsuU9TcArL3248/details">Room
                                                    Spray Bottle</a></td>
                                            <td>800 pieces</td>
                                            <td>$770.00</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/1oFbQKpc2w3mz6bUxKEXGk/details">Single
                                                    Wick Jars</a></td>
                                            <td>355 pieces</td>
                                            <td>$323.05</td>
                                        </tr>
                                        <tr>
                                            <td><a href="/reports/products/noxJuHzhfH7GGQu5ZdbrSk/details">Soap
                                                    Bottle</a></td>
                                            <td>59 pieces</td>
                                            <td>$115.07</td>
                                        </tr>
                                        <tr>
                                            <td><a
                                                    href="/reports/products/mDDMaTnEPPCmLQN81BPh2c/details">Bobbing</a>
                                            </td>
                                            <td>120 oz</td>
                                            <td>$7.38</td>
                                        </tr>
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