@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Contacts</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    
    <form action="{{ route('shipping.contacts.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row g-2 align-items-center">
            <div class="col-auto"><strong>Import CSV</strong></div>
            <div class="col-4">
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-warning">Import</button>
            </div>
        </div>
        <small class="text-muted">example: name,company,email,phone,street,city,state,zip,country</small>
    </form>

    
    <form action="{{ route('shipping.contacts.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-2">
            <div class="col"><input type="text" name="name" class="form-control" placeholder="Name *" required></div>
            <div class="col"><input type="text" name="company" class="form-control" placeholder="Company"></div>
            <div class="col"><input type="email" name="email" class="form-control" placeholder="Email"></div>
            <div class="col"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col"><input type="text" name="street" class="form-control" placeholder="Street"></div>
            <div class="col"><input type="text" name="city" class="form-control" placeholder="City"></div>
            <div class="col"><input type="text" name="state" class="form-control" placeholder="State"></div>
            <div class="col"><input type="text" name="zip" class="form-control" placeholder="ZIP"></div>
            <div class="col"><input type="text" name="country" class="form-control" placeholder="Country"></div>
        </div>
        <div class="mt-2">
            <button class="btn btn-primary">Add</button>
        </div>
    </form>

    
    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>ZIP</th>
                <th>Country</th>
                <th style="width:100px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->company }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->street }}</td>
                    <td>{{ $c->city }}</td>
                    <td>{{ $c->state }}</td>
                    <td>{{ $c->zip }}</td>
                    <td>{{ $c->country }}</td>
                    <td>
                        <form action="{{ route('shipping.contacts.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Delete this contact?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted">No contacts.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
