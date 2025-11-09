@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Shipping Connections</h1>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    
    <form action="{{ route('shipping.connections.store') }}" method="POST" class="mb-3">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Carrier</th>
                        <th>Account #</th>
                        <th>API Key</th>
                        <th>API Secret</th>
                        <th>Sandbox</th>
                        <th style="width:90px;">Add</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="carrier" class="form-control" placeholder="UPS / FedEx / DHL" required></td>
                        <td><input name="account_number" class="form-control" required></td>
                        <td><input name="api_key" class="form-control"></td>
                        <td><input name="api_secret" class="form-control"></td>
                        <td>
                            <select name="sandbox" class="form-select">
                                <option value="1" selected>Sandbox</option>
                                <option value="0">Production</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm">Add</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>

    <!-- List -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Carrier</th>
                    <th>Account #</th>
                    <th>API Key</th>
                    <th>Sandbox</th>
                    <th style="width:170px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($connections as $c)
                    <tr>
                        <td>{{ $c->carrier }}</td>
                        <td>{{ $c->account_number }}</td>
                        <td class="text-truncate" style="max-width:220px;">{{ $c->api_key }}</td>
                        <td>{{ $c->sandbox ? 'Yes' : 'No' }}</td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#editConn{{ $c->id }}">Edit</button>
                            <form action="{{ route('shipping.connections.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Delete this connection?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="collapse" id="editConn{{ $c->id }}">
                        <td colspan="5">
                            <form action="{{ route('shipping.connections.update', $c->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-2"><input name="carrier" class="form-control" value="{{ $c->carrier }}" required></div>
                                    <div class="col-md-2"><input name="account_number" class="form-control" value="{{ $c->account_number }}" required></div>
                                    <div class="col-md-3"><input name="api_key" class="form-control" value="{{ $c->api_key }}"></div>
                                    <div class="col-md-3"><input name="api_secret" class="form-control" value="{{ $c->api_secret }}"></div>
                                    <div class="col-md-1">
                                        <select name="sandbox" class="form-select">
                                            <option value="1" @if($c->sandbox) selected @endif>Sandbox</option>
                                            <option value="0" @if(!$c->sandbox) selected @endif>Production</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-success w-100">Save</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No connections.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $connections->links() }}
</div>
@endsection
