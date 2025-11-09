@extends('layouts.admin_master')
@section('content')
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        🧠 Trace Logs
    </div>
    <div class="card-body">
        @if($logs->isEmpty())
            <div class="alert alert-info">No Trace logs yet.</div>
        @else
            <table class="table table-bordered table-striped bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->performed_by }}</td>
                        <td>{{ $log->action_type }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection