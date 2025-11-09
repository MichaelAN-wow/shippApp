@extends('layouts.admin_master')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Materials
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-title">
            @php
            $selectedCategory = $categories->firstWhere('id', $categoryId);
            @endphp
            {{ $selectedCategory ? $selectedCategory->name : 'All Materials' }}
        </div>
        <div class="card-dashboard-sub-header-controls">
            <div class="float-left search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search Materials" id="customSearchInput">
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importCSVModal">
                    <i class="fas fa-upload"></i> Import CSV
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="materialsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Price / Unit</th>
                    <th>SKU</th>
                    <th>Supplier</th>
                    <th>Category</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $material)
                <tr>
                    <td>{{ $material->name }}</td>
                    <td>{{ $material->current_stock_level }}</td>
                    <td>{{ $material->min_stock_level }}</td>
                    <td>${{ number_format($material->price_per_unit, 2) }}</td>
                    <td>{{ $material->material_code }}</td>
                    <td>{{ $material->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $material->category->name ?? 'Uncategorized' }}</td>
                    <td>{{ $material->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CSV Import Modal -->
<div class="modal fade" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
<form action="{{ route('materials.upload_csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select CSV File</label>
                        <input type="file" class="form-control-file" name="csv_file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection