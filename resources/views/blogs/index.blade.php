@extends('layouts.admin_master')

@section('content')
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Blog Management
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-controls">
            <div class="float-right">
                <a href="{{ route('blogs.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i> Create New Blog
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            @if ($blogs->isEmpty())
                <!-- Show "No Data" message if there are no blogs -->
                <div class="alert alert-warning text-center">
                    No Blog Posts Available.
                </div>
            @else
                <!-- Show the table only if there are blogs -->
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Published On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blogs as $blog)
                            <tr>
                                <td>{{ $blog->title }}</td>
                                <td>{{ $blog->author_name }}</td>
                                <td>{{ $blog->published_on }}</td>
                                <td>{{ ucfirst($blog->status) }}</td>
                                <td>
                                    <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-info">Edit</a>
                                    <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this blog post?');
    }
</script>
@endsection
