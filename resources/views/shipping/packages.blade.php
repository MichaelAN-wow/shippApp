@extends('layouts.admin_master')

@section('content')
<div class="container">
    <h2>Box Inventory</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Box Name</th>
                <th>Dimensions (LxWxH)</th>
                <th>Empty Weight</th>
                <th>Supplier</th>
                <th>Cost</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($boxes as $box)
            <tr>
                <td>{{ $box->name }}</td>
                <td>{{ $box->length }} x {{ $box->width }} x {{ $box->height }}</td>
                <td>{{ $box->empty_weight }} kg</td>
                <td>{{ $box->supplier }}</td>
                <td>${{ $box->cost }}</td>
                <td>
                    <form action="{{ route('shipping.box_inventory.destroy', $box->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
