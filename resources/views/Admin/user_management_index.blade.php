@extends('layouts.admin_master')
@section('content')
<style>
    .ajax-load {
        background: #e9e9e9;
        padding: 10px 0px;
        width: 100%;
    }
</style>
<script>
    var allTrackData = @json($allTrackData);
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        User Management
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody id="post-data">
                    @foreach ($tracks as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="#" class="user-name"
                                    data-id="{{ $row->user->id }}">{{ $row->user->name }}</a></td>
                            <td>{{ $row->user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="ajax-load text-center" style="display:none">
                <span class="spinner-border text-primary" role="status"></span>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="trackModal" tabindex="-1" role="dialog" aria-labelledby="trackModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackModalLabel">Track Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="track-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Arrival Time</th>
                            <th>Departure Time</th>
                            <th>Hours</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="modal-track-data">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="mr-auto" style="font-size: 1.1em;">Total Tracked Hours: <span id="total-hours" style="font-weight: bold; font-size: 1.2em;">0</span></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-button">Print</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- Include the latest jsPDF, html2canvas, and autoTable plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userNameElements = document.querySelectorAll('.user-name');

        userNameElements.forEach(function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
                var userId = this.getAttribute('data-id');
                var userTracks = allTrackData[userId];
                var userName = this.innerText; // Get the user's name

                if (userTracks && userTracks.length > 0) {
                    var modalTrackData = document.getElementById('modal-track-data');
                    modalTrackData.innerHTML = ''; // Clear existing rows
                    var totalHours = 0;

                    userTracks.forEach(function(track, index) {
                        var row = document.createElement('tr');

                        var cellId = document.createElement('td');
                        cellId.innerText = index + 1; // Serial number starting from 1
                        row.appendChild(cellId);

                        var cellDate = document.createElement('td');
                        cellDate.innerText = track.date;
                        row.appendChild(cellDate);

                        var cellDate = document.createElement('td');
                        cellDate.innerText = track.arrival_time;
                        row.appendChild(cellDate);

                        var cellDate = document.createElement('td');
                        cellDate.innerText = track.departure_time;
                        row.appendChild(cellDate);

                        var cellHours = document.createElement('td');
                        cellHours.innerText = track.hours;
                        row.appendChild(cellHours);

                        var cellNotes = document.createElement('td');
                        cellNotes.innerText = track.notes;
                        row.appendChild(cellNotes);

                        modalTrackData.appendChild(row);

                        // Calculate total hours
                        totalHours += parseFloat(track.hours);
                    });

                    document.getElementById('trackModalLabel').innerText = userName + ' - Track Details';
                    document.getElementById('total-hours').innerText = totalHours + '/hr';
                    $('#trackModal').modal('show');
                }
            });
        });

        // Print button click event
        document.getElementById('print-button').addEventListener('click', function() {
            var { jsPDF } = window.jspdf;
            var doc = new jsPDF();

            // Get the user name
            var userName = document.getElementById('trackModalLabel').innerText.split(' - ')[0];

            // Convert the HTML table to a JSON object for autoTable
            var tableData = [];
            var rows = document.querySelectorAll('#modal-track-data tr');
            rows.forEach(function(row) {
                var rowData = [];
                row.querySelectorAll('td').forEach(function(cell) {
                    rowData.push(cell.innerText);
                });
                tableData.push(rowData);
            });

            // Add the user's name at the top
            doc.text(userName, 14, 20);

            // Add the table to the PDF
            doc.autoTable({
                startY: 30,
                head: [['ID', 'Date', 'Hours', 'Notes']],
                body: tableData
            });

            // Add total hours at the bottom
            var totalHours = document.getElementById('total-hours').innerText;
            doc.text('Total Tracked Hours: ' + totalHours, 14, doc.lastAutoTable.finalY + 10);

            // Save the PDF
            doc.save('track-details.pdf');
        });
    });
</script>
@endsection
