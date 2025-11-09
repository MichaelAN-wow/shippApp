@extends('layouts.admin_master')
@section('content')
<div class="container mt-5">
    <h3>🛠️ Trace Patch Registry</h3>
    <p class="text-muted mb-4">This page displays GPT-applied or suggested patches and actions. Trace uses this to manage fixes, log timestamps, and flag live errors.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered bg-white">
                <thead class="thead-dark">
                    <tr>
                        <th>Patch ID</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patches as $patch)
                        <tr>
                            <td>{{ $patch->id }}</td>
                            <td>{{ $patch->description }}</td>
                            <td>
                                @if ($patch->status == 'applied')
                                    <span class="badge badge-success">Applied</span>
                                @elseif ($patch->status == 'suggested')
                                    <span class="badge badge-warning">Suggested</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($patch->status) }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($patch->created_at)->toDayDateTimeString() }}</td>
                        </tr>
                    @endforeach
                    @if ($patches->isEmpty())
                        <tr><td colspan="4" class="text-center text-muted">No patches registered yet.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection