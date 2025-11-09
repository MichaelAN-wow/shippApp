@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Reports Dashboard</h1>
    <p>Select a report to view details:</p>
    <ul>
        <li><a href="{{ route('all.invoices') }}">All Invoices</a></li>
        <li><a href="{{ route('sold.products') }}">Sold Products</a></li>
        <li><a href="{{ route('inventory-waste') }}">Inventory Waste</a></li>
        {{-- Add more links here as you build out report-specific views --}}
    </ul>
</div>
@endsection
