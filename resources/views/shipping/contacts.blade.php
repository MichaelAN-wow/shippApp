@extends('layouts.admin_master')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-3">Contacts</h1>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Import / Export --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            {{-- Import --}}
            <form action="{{ route('shipping.contacts.import') }}" method="POST" enctype="multipart/form-data" 
                  class="d-flex align-items-center gap-2 flex-wrap">
                @csrf
                <label class="fw-bold mb-0">Import CSV:</label>
                <input type="file" name="csv_file" class="form-control form-control-md" accept=".csv" required style="max-width: 300px;">
                <button type="submit" class="btn btn-warning btn-sm">Import</button>
            </form>

            {{-- Export --}}
            <form id="exportContactsForm" action="javascript:void(0);" method="GET">
                <button type="submit" class="btn btn-success btn-sm">Export CSV</button>
            </form>
        </div>
    </div>

    {{-- Add New Contact --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('shipping.contacts.store') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <input type="text" name="name" class="form-control" placeholder="Name *" required>
                    </div>
                    <div class="col-md-2">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="address" class="form-control" placeholder="Address">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="notes" class="form-control" placeholder="Notes">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Contacts Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Saved Contacts</h5>

                {{-- Bulk Delete Form --}}
                <form id="bulkDeleteForm" action="{{ route('shipping.contacts.bulkDelete') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="bulkDeleteIds">
                </form>

                {{-- Merge Duplicates Form --}}
                <form id="mergeDuplicatesForm" action="{{ route('shipping.contacts.mergeDuplicates') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="ids" id="mergeDuplicatesIds">
                </form>

                {{-- Buttons --}}
                <div class="d-flex gap-2">
                    <button id="bulkDeleteBtn" class="btn btn-sm btn-danger" disabled>Delete Selected</button>
                    <button id="mergeDuplicatesBtn" class="btn btn-secondary btn-sm" disabled>Merge Duplicates</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="contactsTable">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="selectAllContacts"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Notes</th>
                            <th style="width:90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $c)
                        <tr>
                            <td><input type="checkbox" class="select-contact" value="{{ $c->id }}"></td>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->address }}</td>
                            <td>{{ $c->notes }}</td>
                            <td>
                                <form action="{{ route('shipping.contacts.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Delete this contact?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No contacts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // DataTable init
    const table = $('#contactsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        columnDefs: [
            { orderable: false, targets: [0, 5] }
        ]
    });

    // Select all & bulk buttons
    const selectAll = $('#selectAllContacts');
    const bulkDeleteBtn = $('#bulkDeleteBtn');
    const mergeBtn = $('#mergeDuplicatesBtn');

    function updateBulkButtons() {
        const selectedCount = $('.select-contact:checked').length;
        bulkDeleteBtn.prop('disabled', selectedCount === 0);
        mergeBtn.prop('disabled', selectedCount < 2);
    }

    selectAll.on('change', function() {
        $('.select-contact').prop('checked', this.checked);
        updateBulkButtons();
    });

    $(document).on('change', '.select-contact', updateBulkButtons);

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const selected = $('.select-contact:checked').map((_, el) => el.value).get();
        if (!selected.length) return;
        if (!confirm(`Delete ${selected.length} selected contacts?`)) return;
        $('#bulkDeleteIds').val(JSON.stringify(selected));
        $('#bulkDeleteForm').submit();
    });

    // Merge duplicates
    $('#mergeDuplicatesBtn').on('click', function() {
        const selected = $('.select-contact:checked').map((_, el) => el.value).get();
        if (selected.length < 2) return alert('Select at least two contacts to merge.');
        if (!confirm(`Merge ${selected.length} selected contacts? This cannot be undone.`)) return;
        $('#mergeDuplicatesIds').val(JSON.stringify(selected));
        $('#mergeDuplicatesForm').submit();
    });

    $('#exportContactsForm').on('submit', function() {
        debugger;
        const rows = $('#contactsTable tbody tr');
        if (!rows.length) return alert('No contacts to export!');

        // CSV headers
        const headers = ['Name', 'Email', 'Address', 'Notes'];
        let csv = headers.join(',') + '\n';

        // CSV rows
        rows.each(function() {
            const cols = $(this).find('td').not(':first').not(':last'); // skip checkbox and action column
            const rowData = cols.map(function() {
                const text = $(this).text().trim().replace(/"/g, '""');
                return `"${text}"`;
            }).get();
            csv += rowData.join(',') + '\n';
        });

        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `contacts_${new Date().toISOString().slice(0, 10)}.csv`;
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

});
</script>
@endpush
