@extends('layouts.admin_master')

@section('content')
    <main>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card shadow-lg border-0 rounded-lg mt-5">
                        <div class="card-header">
                            <h3 class="text-center font-weight-light my-4">Add New Material</h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ url('/insert-material') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputFirstName">Name</label>
                                            <input class="form-control" name="name" type="text" placeholder="" />
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
                                            <input class="form-control" name="current_stock" type="text" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Stock Unit Type</label>
                                            <select class="form-control" name="stock_unit_type">
                                                <optgroup label="Quantity">
                                                    <option value="pieces">Pieces</option>
                                                </optgroup>
                                                <optgroup label="Weight">
                                                    <option value="weight.oz">Ounces</option>
                                                    <option value="weight.lbs">Pounds</option>
                                                    <option value="weight.mg">Milligrams</option>
                                                    <option value="weight.grams">Grams</option>
                                                    <option value="weight.kg">Kilograms</option>
                                                    <option value="weight.ct">Carats</option>
                                                </optgroup>
                                                <optgroup label="Length">
                                                    <option value="length.in">Inches</option>
                                                    <option value="length.ft">Feet</option>
                                                    <option value="length.yd">Yards</option>
                                                    <option value="length.mm">Millimeters</option>
                                                    <option value="length.cm">Centimeters</option>
                                                    <option value="length.m">Meters</option>
                                                </optgroup>
                                                <optgroup label="Area">
                                                    <option value="area.sqin">Sq. Inches</option>
                                                    <option value="area.sqft">Sq. Feet</option>
                                                    <option value="area.sqcm">Sq. Centimeters</option>
                                                    <option value="area.sqm">Sq. Meters</option>
                                                </optgroup>
                                                <optgroup label="Volume">
                                                    <option value="volume.oz">Fluid Ounces</option>
                                                    <option value="volume.pt">Pints</option>
                                                    <option value="volume.qt">Quarts</option>
                                                    <option value="volume.ga">Gallons</option>
                                                    <option value="volume.ml">Milliliters</option>
                                                    <option value="volume.liters">Liters</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Minimum Stock Level</label>
                                            <input class="form-control" name="minimum_stock" type="text" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Cost per unit</label>
                                            <input class="form-control" name="cost_per_unit" type="text" placeholder="" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Supplier</label>
                                            <select class="form-control" name="supplier">
                                                <!-- Supplier options -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Category</label>
                                            <select class="form-control" name="category">
                                                <!-- Category options -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputLastName">Last Order Date</label>
                                            <input class="form-control" name="last_order_date" type="date" placeholder="" />
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
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_bulk_calc" name="is_bulk_calc">
                                        <label class="form-check-label" for="is_bulk_calc">Calculate cost per unit from total bulk price</label>
                                    </div>
                                </div>

                                <div class="form-group mt-4 mb-0">
                                    <button class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection