@extends('layouts.admin_master')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">Labels</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('shipping.labels.create') }}" class="btn btn-primary">Create New Label</a>
    </div>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Created At</th>
                <th>Package</th>
                <th>From (Sender)</th>
                <th>To (Recipient)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($labels as $l)
                <tr>
                    <td>{{ $l->created_at }}</td>
                    <td>{{ $l->package }}</td>
                    <td>
                        {{ $l->sender?->name }}
                        <div class="text-muted small">{{ $l->sender?->address }}</div>
                    </td>
                    <td>
                        {{ $l->recipient?->name }}
                        <div class="text-muted small">{{ $l->recipient?->address }}</div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">No labels.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
