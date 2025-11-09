@extends('layouts.admin_master')

@section('content')
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        🧠 Trace Log Report
    </div>
    <div class="card-dashboard-sub-header">
        All system activity by Trace (AI system logs)
    </div>
    <div class="card-body bg-white">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Model</th>
                    <th>Item Affected</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patches as $patch)
                    <tr>
                        <td>{{ $patch->id }}</td>
                        <td>{{ $patch->action }}</td>
                        <td>{{ $patch->model }}</td>
                        <td>{{ $patch->item_id ?? '—' }}</td>
                        <td>{{ $patch->old_value ?? '—' }}</td>
                        <td>{{ $patch->new_value ?? '—' }}</td>
                        <td>{{ $patch->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No Trace activity found yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $patches->links() }}
        </div>
    </div>
</div>
@endsection