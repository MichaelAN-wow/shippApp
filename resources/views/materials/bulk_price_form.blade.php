@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🔁 Bulk Price Update</h2>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ❌ Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 📤 Upload Form --}}
    <form method="POST" action="{{ route('bulk_price.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="price_file" class="form-label">Upload Excel (.xlsx) with F-Code + New Price</label>
            <input type="file" name="price_file" id="price_file" class="form-control" accept=".xlsx">
        </div>
        <button type="submit" class="btn btn-warning">Upload + Apply Prices</button>
    </form>

    {{-- 📝 Instructions --}}
    <p class="mt-3 text-muted">
        File must include columns: <strong>F-Code</strong> and <strong>New Price</strong><br>
        Accepted file type: <code>.xlsx</code>
    </p>
</div>
@endsection