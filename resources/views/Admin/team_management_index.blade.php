@extends('layouts.admin_master')
@section('content')
<style>
.ajax-load {
    background: #e9e9e9;
    padding: 10px 0px;
    width: 100%;
}

.daterangepicker.time-only .calendar-table {
    display: none;
}

.daterangepicker.time-only .calendar-time {
    border-top: none;
}
</style>
<script>
var allTrackData = @json($allTrackData);
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        Team Management
    </div>
    <div class="card-dashboard-sub-header">
        <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
            data-target="#inviteTeamMemberModal" style="margin-right: 20px;">
            <i class="fas fa-plus"></i> Invite Team Member
        </button>
        <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
            data-target="#archivedEmployeesModal" style="margin-right: 10px;">
            <i class="fas fa-archive"></i> View Archived Employees
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Unpaid Tracked Hours</th>
                        <th>Paid Hours</th>
                        <th>Hourly Rate ($)</th>
                        <th>Total Paid ($)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="post-data">
                    @foreach ($employees as $index => $employee)
                    @php
                    $totalTrackedHours = $unpaidTracks[$employee->id] ?? 0;
                    $totalPaidHours = $paidTracks[$employee->id] ?? 0;
                    $totalPaid = $totalPaidHours * $employee->hourly_rate;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="#" class="user-name" data-id="{{ $employee->id }}">{{ $employee->name }}</a></td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $totalTrackedHours }}</td>
                        <td>{{ $totalPaidHours }}</td>
                        <td>{{ $employee->hourly_rate }}</td>
                        <td>{{ number_format($totalPaid, 2) }}</td>
                        <td>
                            <!-- Add Time Track Button -->
                            <a href="#" class="btn btn-sm btn-info add-btn" data-toggle="modal"
                                data-target="#editTrackingModal" data-id="{{ $employee->id }}" title="Add Time Track">
                                <i class="fa fa-plus"></i>
                            </a>
                            
                            <!-- Edit Rate Button -->
                            <a href="#" class="btn btn-sm btn-info edit-btn" data-toggle="modal"
                                data-target="#editRateModal" data-id="{{ $employee->id }}"
                                data-rate="{{ $employee->hourly_rate }}" title="Edit Rate">
                                <i class="fa fa-edit"></i>
                            </a>
                            
                            <!-- Pay Button -->
                            <a href="#" class="btn btn-sm btn-success pay-btn" data-toggle="modal"
                                data-target="#payModal" data-id="{{ $employee->id }}" title="Pay">
                                <i class="fa fa-money-bill-wave"></i>
                            </a>
                            
                            <!-- NEW: Export Data Button -->
                            <a href="#" class="btn btn-sm btn-warning export-btn" data-toggle="modal"
                                data-target="#exportModal" data-id="{{ $employee->id }}" title="Export Data">
                                <i class="fa fa-file-export"></i>
                            </a>
                            
                            <!-- NEW: Archive Button -->
                            <a href="#" class="btn btn-sm btn-danger archive-btn" data-toggle="modal"
                                data-target="#archiveModal" data-id="{{ $employee->id }}" 
                                data-name="{{ $employee->name }}" title="Archive Employee">
                                <i class="fa fa-archive"></i>
                            </a>
                        </td>
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
<!-- Modal -->
<div class="modal fade" id="editTrackingModal" tabindex="-1" role="dialog" aria-labelledby="editTrackingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTrackingModalLabel">Add New Time Track</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/team/time_tracking/addByUserId') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    <input type="hidden" id="arrivalTime" name="arrival_time">
                    <input type="hidden" id="departureTime" name="departure_time">

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Date</label>
                                <input class="form-control" name="date" type="date" required />
                            </div>
                        </div>

                        <!-- Arrival Time -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Arrival Time</label>
                                <div class="d-flex">
                                    <input type="number" class="form-control mr-1" id="arrivalHour" placeholder="HH" min="1" max="12" required oninput="calculateHours()">
                                    <span class="mr-1">:</span>
                                    <input type="number" class="form-control mr-1" id="arrivalMinute" placeholder="MM" min="0" max="59" required oninput="calculateHours()">
                                    <select class="form-control" id="arrivalAmPm" onchange="calculateHours()">
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Departure Time -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Departure Time</label>
                                <div class="d-flex">
                                    <input type="number" class="form-control mr-1" id="departureHour" placeholder="HH" min="1" max="12" required oninput="calculateHours()">
                                    <span class="mr-1">:</span>
                                    <input type="number" class="form-control mr-1" id="departureMinute" placeholder="MM" min="0" max="59" required oninput="calculateHours()">
                                    <select class="form-control" id="departureAmPm" onchange="calculateHours()">
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Calculated Hours -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Calculated Hours</label>
                                <input class="form-control" id="calculatedHours" type="number" name="hours" readonly />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4 mb-0">
                        <button class="btn btn-primary btn-block">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


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
                            <th>Owed</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="modal-track-data">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="mr-auto" style="font-size: 1.1em;">Total Tracked Hours: <span id="total-hours"
                        style="font-weight: bold; font-size: 1.2em;">0</span></div>
                <div class="mr-auto" style="font-size: 1.1em;">Total Owed Amount: <span id="total-amount"
                        style="font-weight: bold; font-size: 1.2em;">0</span></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-button">Print</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="inviteTeamMemberModal" tabindex="-1" role="dialog" aria-labelledby="addTrackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTrackingModalLabel">Invite New Team Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Success/Error message placeholder -->
                <div id="responseMessage" class="alert" style="display:none;"></div>

                <!-- Invite form -->
                <form id="inviteForm">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="small mb-1" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter email">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4 mb-0">
                        <button type="button" class="btn btn-primary btn-block" id="inviteButton">Send Invitation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1" role="dialog" aria-labelledby="editRateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRateModalLabel">Edit Hourly Rate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editRateForm">
                    <div class="form-group">
                        <label for="hourly-rate">Hourly Rate ($)</label>
                        <input type="number" class="form-control" id="hourly-rate" name="hourly-rate" required>
                    </div>
                    <input type="hidden" id="user-id" name="user-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-rate-button">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Pay Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="pay-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Arrival Time</th>
                            <th>Departure Time</th>
                            <th>Hours</th>
                            <th>Owed Amount($)</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="modal-pay-data">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
                <input type="hidden" id="track-ids">
            </div>
            <div class="modal-footer">
                <div class="mr-auto" style="font-size: 1.1em;">Total Owed Amount: <span id="pay-total-amount"
                        style="font-weight: bold; font-size: 1.2em;">0</span></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirm-pay-button">Pay</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal to your HTML (place it with other modals) -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exportModalLabel">Export Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Data to Export:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportUnpaid" checked>
                        <label class="form-check-label" for="exportUnpaid">Unpaid Time Tracks</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportPaid" checked>
                        <label class="form-check-label" for="exportPaid">Paid Time Tracks</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Date Range:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="exportStartDate">
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" id="exportEndDate">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExport">Export CSV</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Employee Confirmation Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveModalLabel">Archive Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive <strong id="archiveEmployeeName"></strong>?</p>
                <p class="text-muted">This will:</p>
                <ul class="text-muted">
                    <li>Remove their access to the platform</li>
                    <li>Keep all their time tracking data for payment purposes</li>
                    <li>Hide them from the calendar and team management</li>
                    <li>Allow you to restore them later if needed</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmArchive">Archive Employee</button>
            </div>
        </div>
    </div>
</div>

<!-- Archived Employees Modal -->
<div class="modal fade" id="archivedEmployeesModal" tabindex="-1" role="dialog" aria-labelledby="archivedEmployeesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archivedEmployeesModalLabel">Archived Employees</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="archivedEmployeesList">
                    <p class="text-center text-muted">Loading archived employees...</p>
                </div>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
function to24Hour(hour, amPm) {
    if (amPm === 'PM' && hour < 12) return hour + 12;
    if (amPm === 'AM' && hour === 12) return 0;
    return hour;
}

function calculateHours() {
    const arrivalHour = parseInt(document.getElementById('arrivalHour').value, 10) || 0;
    const arrivalMinute = parseInt(document.getElementById('arrivalMinute').value, 10) || 0;
    const arrivalAmPm = document.getElementById('arrivalAmPm').value;

    const departureHour = parseInt(document.getElementById('departureHour').value, 10) || 0;
    const departureMinute = parseInt(document.getElementById('departureMinute').value, 10) || 0;
    const departureAmPm = document.getElementById('departureAmPm').value;

    const arrH24 = to24Hour(arrivalHour, arrivalAmPm);
    const depH24 = to24Hour(departureHour, departureAmPm);

    const arrivalTotalMinutes = arrH24 * 60 + arrivalMinute;
    const departureTotalMinutes = depH24 * 60 + departureMinute;

    let minutesDifference = departureTotalMinutes - arrivalTotalMinutes;
    if (minutesDifference < 0) {
        minutesDifference += 24 * 60;
    }

    const hoursDifference = minutesDifference / 60;

    document.getElementById('calculatedHours').value = hoursDifference.toFixed(2);
    document.getElementById('arrivalTime').value =
        `${String(arrH24).padStart(2, '0')}:${String(arrivalMinute).padStart(2, '0')}:00`;
    document.getElementById('departureTime').value =
        `${String(depH24).padStart(2, '0')}:${String(departureMinute).padStart(2, '0')}:00`;
}

function validateForm() {
    const arrivalHour = parseInt(document.getElementById('arrivalHour').value, 10) || 0;
    const arrivalMinute = parseInt(document.getElementById('arrivalMinute').value, 10) || 0;
    const arrivalAmPm = document.getElementById('arrivalAmPm').value;

    const departureHour = parseInt(document.getElementById('departureHour').value, 10) || 0;
    const departureMinute = parseInt(document.getElementById('departureMinute').value, 10) || 0;
    const departureAmPm = document.getElementById('departureAmPm').value;

    const arrH24 = to24Hour(arrivalHour, arrivalAmPm);
    const depH24 = to24Hour(departureHour, departureAmPm);

    const arrivalTime = arrH24 + arrivalMinute / 60;
    const departureTime = depH24 + departureMinute / 60;

    if (departureTime <= arrivalTime) {
        alert('Departure time must be after arrival time.');
        return false;
    }

    calculateHours();
    return true;
}
$(document).ready(function() {
    // Initialize date picker
    function add_date_picker_change(start) {
        $('#add_date_picker span').html(start.format('MMMM D, YYYY'));
        $('#add_date_picker input').val(start.format('YYYY-MM-DD'));
    }

    $('#add_date_picker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: moment(),
    }, add_date_picker_change);
    add_date_picker_change(moment());

    // Clear notes field on modal show
    $('#editTrackingModal').on('show.bs.modal', function() {
        $('#textarea_notes').val('');
    });

    $('.add-btn').on('click', function() {
        var userId = $(this).data('id');
        $('#user_id').val(userId);
    });

});

// Add this JavaScript to your existing code
document.addEventListener('DOMContentLoaded', function() {
    // Handle export button click
    document.querySelectorAll('.export-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-id');
            const userName = this.closest('tr').querySelector('.user-name').innerText;
            
            // Store the current user ID for export
            document.getElementById('exportModal').setAttribute('data-user-id', userId);
            document.getElementById('exportModalLabel').innerText = `Export Data - ${userName}`;
            
            // Set default dates (last 30 days)
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 30);
            
            document.getElementById('exportStartDate').valueAsDate = startDate;
            document.getElementById('exportEndDate').valueAsDate = endDate;
            
            $('#exportModal').modal('show');
        });
    });

    // Handle export confirmation
    document.getElementById('confirmExport').addEventListener('click', function() {
        const userId = document.getElementById('exportModal').getAttribute('data-user-id');
        const exportUnpaid = document.getElementById('exportUnpaid').checked;
        const exportPaid = document.getElementById('exportPaid').checked;
        const startDate = document.getElementById('exportStartDate').value;
        const endDate = document.getElementById('exportEndDate').value;

        // Get the user's tracks
        const userTracks = allTrackData[userId] || [];
        
        // Filter tracks based on selection
        let tracksToExport = [];
        
        if (exportUnpaid) {
            tracksToExport = tracksToExport.concat(userTracks.filter(track => !track.paid));
        }
        
        if (exportPaid) {
            tracksToExport = tracksToExport.concat(userTracks.filter(track => track.paid));
        }
        
        // Filter by date range if specified
        if (startDate && endDate) {
            tracksToExport = tracksToExport.filter(track => {
                const trackDate = new Date(track.date);
                const start = new Date(startDate);
                const end = new Date(endDate);
                return trackDate >= start && trackDate <= end;
            });
        }
        
        // Export to CSV
        exportToCSV(tracksToExport, userId);
        
        // Close modal
        $('#exportModal').modal('hide');
    });

    // Function to export data to CSV
    function exportToCSV(tracks, userId) {
        if (tracks.length === 0) {
            alert('No data to export!');
            return;
        }

        // CSV header
        let csv = 'Date,Arrival Time,Departure Time,Hours,Hourly Rate,Amount,Notes,Status\n';
        
        // Add rows
        tracks.forEach(track => {
            const arrival = track.arrival_time ? track.arrival_time.substring(0, 5) : '';
            const departure = track.departure_time ? track.departure_time.substring(0, 5) : '';
            const amount = (track.hourly_rate * track.hours).toFixed(2);
            const status = track.paid ? 'Paid' : 'Unpaid';
            
            // Handle null notes
            const notes = track.notes ? track.notes.replace(/"/g, '""') : '';
            
            csv += `"${track.date}","${arrival}","${departure}","${track.hours}","${track.hourly_rate}","${amount}","${notes}","${status}"\n`;
        });

        // Create download link
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        // Get user name for filename
        const userName = document.querySelector(`.user-name[data-id="${userId}"]`).innerText;
        const sanitizedName = userName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        const dateStr = new Date().toISOString().slice(0, 10);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `time_tracks_${sanitizedName}_${dateStr}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});

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
                var totalAmount = 0;

                userTracks.forEach(function(track, index) {
                    if (!track.paid) {
                        var row = document.createElement('tr');

                        var cellId = document.createElement('td');
                        cellId.innerText = index +
                            1; // Serial number starting from 1
                        row.appendChild(cellId);

                        var cellDate = document.createElement('td');
                        cellDate.innerText = track.date;
                        row.appendChild(cellDate);

                        var cellArrival = document.createElement('td');
                        cellArrival.innerText = track.arrival_time ? track.arrival_time.substring(0, 5) : '';
                        row.appendChild(cellArrival);

                        var cellDeparture = document.createElement('td');
                        cellDeparture.innerText = track.departure_time ? track.departure_time.substring(0, 5) : '';
                        row.appendChild(cellDeparture);

                        var cellHours = document.createElement('td');
                        cellHours.innerText = track.hours;
                        row.appendChild(cellHours);

                        var cellAmount = document.createElement('td');
                        var hourlyRate = track.hourly_rate;
                        var amount = hourlyRate * track.hours;
                        cellAmount.innerText = '$' + amount.toFixed(2);
                        row.appendChild(cellAmount);

                        var cellNotes = document.createElement('td');
                        cellNotes.innerText = track.notes;
                        row.appendChild(cellNotes);

                        // Add Edit and Delete buttons
                        var cellActions = document.createElement('td');

                        var editButton = document.createElement('button');
                        editButton.className = 'btn btn-sm btn-primary edit-track';
                        editButton.setAttribute('title', 'Edit');
                        editButton.innerHTML = '<i class="fa fa-edit"></i>';
                        editButton.setAttribute('data-id', track.id);
                        editButton.setAttribute('data-date', track.date);
                        editButton.setAttribute('data-arrival', track.arrival_time);
                        editButton.setAttribute('data-departure', track.departure_time);
                        editButton.setAttribute('data-hours', track.hours);
                        editButton.setAttribute('data-notes', track.notes);
                        cellActions.appendChild(editButton);

                        var deleteButton = document.createElement('button');
                        deleteButton.className = 'btn btn-sm btn-danger delete-track';
                        deleteButton.setAttribute('title', 'Delete');
                        deleteButton.innerHTML = '<i class="fa fa-trash"></i>';
                        deleteButton.setAttribute('data-id', track.id);
                        cellActions.appendChild(deleteButton);

                        row.appendChild(cellActions);

                        modalTrackData.appendChild(row);

                        // Calculate total hours
                        totalHours += parseFloat(track.hours);
                        totalAmount += amount;
                    }
                });


                document.getElementById('total-hours').innerText = totalHours.toFixed(2) +
                    ' hrs';
                document.getElementById('total-amount').innerText = totalAmount.toFixed(2) +
                    ' $';

            }
            document.getElementById('trackModalLabel').innerText = userName +
                ' - Track Details';
            $('#trackModal').modal('show');

            // Add event listeners for edit and delete buttons
            document.querySelectorAll('.edit-track').forEach(function(button) {
                button.addEventListener('click', function() {
                    var trackId = this.getAttribute('data-id');
                    var date = this.getAttribute('data-date');
                    var arrivalTime = this.getAttribute('data-arrival');
                    var departureTime = this.getAttribute('data-departure');
                    var hours = this.getAttribute('data-hours');
                    var notes = this.getAttribute('data-notes');

                    var formattedArrivalTime = moment(arrivalTime, 'HH:mm:ss')
                        .format('HH:mm');
                    var formattedDepartureTime = moment(departureTime,
                        'HH:mm:ss').format('HH:mm');

                    var arrivalHour = formattedArrivalTime.split(':')[0];
                    var arrivalMinute = formattedArrivalTime.split(':')[1];
                    var departureHour = formattedDepartureTime.split(':')[0];
                    var departureMinute = formattedDepartureTime.split(':')[1];

                    var formattedDate = moment(date, 'YYYY-MM-DD').format(
                        'MMMM D, YYYY');
                    $('#add_date_picker span').html(formattedDate);
                    $('#editTrackingModal input[name="date"]').val(date);
                    $('#editTrackingModal input[name="arrival_hour"]').val(
                        arrivalHour);
                    $('#editTrackingModal input[name="arrival_minute"]').val(
                        arrivalMinute);
                    $('#editTrackingModal input[name="departure_hour"]').val(
                        departureHour);
                    $('#editTrackingModal input[name="departure_minute"]').val(
                        departureMinute);
                    $('#editTrackingModal input[name="hours"]').val(hours);
                    $('#editTrackingModal textarea[name="notes"]').val(notes);
                    $('#editTrackingModalLabel').text('Edit Time Track');
                    $('#editTrackingModal form').attr('action',
                        '/team/time_tracking/edit/' + trackId);
                    $('#trackModal').modal('hide'); // Close the track modal
                    setTimeout(function() {
                        $('#editTrackingModal').modal(
                            'show'); // Show the edit modal
                    }, 500);
                });
            });

            document.querySelectorAll('.delete-track').forEach(function(button) {
                button.addEventListener('click', function() {
                    var trackId = this.getAttribute('data-id');
                    if (confirm(
                            'Are you sure you want to delete this track?')) {
                        var token = '{{ csrf_token() }}';
                        $.ajax({
                            url: '/team/time_tracking/' + trackId,
                            type: 'DELETE',
                            data: {
                                '_token': token,
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response
                                        .success);
                                    location.reload();
                                } else {
                                    toastr.error(
                                        'Failed to delete the item.'
                                    );
                                }
                            },
                            error: function(response) {
                                toastr.error(
                                    'An error occurred while deleting the item.'
                                );
                            }
                        });
                    }
                });
            });
        });
    });

    // Event listener to reopen the track modal if the edit modal is closed without saving
    $('#editTrackingModal').on('hidden.bs.modal', function() {
        $('#editTrackingModal input[name="arrival_hour"]').val('');
        $('#editTrackingModal input[name="arrival_minute"]').val('');
        $('#editTrackingModal input[name="departure_hour"]').val('');
        $('#editTrackingModal input[name="departure_minute"]').val('');
        $('#editTrackingModal input[name="hours"]').val('');
        $('#editTrackingModal textarea[name="notes"]').val('');
        $('#add_date_picker span').html('');

        $('#editTrackingModal form').attr('action','/team/time_tracking/addByUserId');

        if ($('#editTrackingModalLabel').text() === 'Edit Time Track') {
            $('#trackModal').modal('show');
        }
    });

    $('#editTrackingModal form').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success('Track updated successfully!');
                    setTimeout(function() {
                        location.reload(); // Reload the page after a delay
                    }, 2000); // Delay of 2 seconds before reloading
                } else {
                    toastr.error('Failed to update track.');
                }
            },
            error: function() {
                toastr.error('An error occurred while updating the track.');
            }
        });
    });



    // Populate modal with user data on edit button click
    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = this.getAttribute('data-id');
            var hourlyRate = this.getAttribute('data-rate');

            document.getElementById('user-id').value = userId;
            document.getElementById('hourly-rate').value = hourlyRate;
        });
    });

    // Save updated hourly rate
    document.getElementById('save-rate-button').addEventListener('click', function() {
        var userId = document.getElementById('user-id').value;
        var hourlyRate = document.getElementById('hourly-rate').value;

        fetch('/team/update-hourly-rate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId,
                hourly_rate: hourlyRate
            })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('Hourly rate updated successfully!');
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Failed to update hourly rate.');
            }
        }).catch(error => {
            alert('Error updating hourly rate.');
        });
    });

    // Populate modal with user data on pay button click
    document.querySelectorAll('.pay-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = this.getAttribute('data-id');
            var userTracks = allTrackData[userId];
            var userName = this.innerText; // Get the user's name

            if (userTracks && userTracks.length > 0) {

                var modalPayData = document.getElementById('modal-pay-data');
                modalPayData.innerHTML = ''; // Clear existing rows
                var totalAmount = 0;
                var trackIds = []; // To store track IDs
                console.log(userTracks);
                userTracks.forEach(function(track, index) {
                    if (!track.paid) {
                        var row = document.createElement('tr');

                        var cellId = document.createElement('td');
                        cellId.innerText = index + 1;
                        row.appendChild(cellId);

                        var cellDate = document.createElement('td');
                        cellDate.innerText = track.date;
                        row.appendChild(cellDate);

                        var formattedArrivalTime = moment(track.arrival_time, 'HH:mm:ss').format('h:mm A');
                        var cellArrival = document.createElement('td');
                        cellArrival.innerText = formattedArrivalTime;
                        row.appendChild(cellArrival);

                        var formattedDepartureTime = moment(track.departure_time, 'HH:mm:ss').format('h:mm A');
                        var cellDeparture = document.createElement('td');
                        cellDeparture.innerText = formattedDepartureTime;
                        row.appendChild(cellDeparture);

                        var cellHours = document.createElement('td');
                        cellHours.innerText = track.hours;
                        row.appendChild(cellHours);

                        var cellAmount = document.createElement('td');
                        var hourlyRate = track.hourly_rate;
                        var amount = hourlyRate * track.hours;
                        cellAmount.innerText = amount.toFixed(
                            2); // Show amount to 2 decimal places
                        row.appendChild(cellAmount);

                        var cellNotes = document.createElement('td');
                        cellNotes.innerText = track.notes;
                        row.appendChild(cellNotes);

                        modalPayData.appendChild(row);

                        totalAmount += amount;
                        trackIds.push(track.id); // Add track ID to the list
                    }
                });

                document.getElementById('payModalLabel').innerText = userName +
                    ' - Pay Details';
                document.getElementById('pay-total-amount').innerText = totalAmount.toFixed(
                    2) + ' $';
                document.getElementById('track-ids').value = JSON.stringify(
                    trackIds); // Store track IDs
                $('#payModal').modal('show');
            }
        });
    });

    // Confirm pay button click event
    document.getElementById('confirm-pay-button').addEventListener('click', function() {
        var trackIds = JSON.parse(document.getElementById('track-ids').value);

        fetch('/team/pay-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({
                track_ids: trackIds
            })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('Payment status updated successfully!');
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Failed to update payment status.');
            }
        }).catch(error => {
            alert('Error updating payment status.');
        });
    });
    // Print button click event
    document.getElementById('print-button').addEventListener('click', function() {
        var {
            jsPDF
        } = window.jspdf;
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
            head: [
                ['ID', 'Date', 'Arrival Time', 'Departure Time', 'Hours',
                    'Owed Amount($)', 'Notes'
                ]
            ],
            body: tableData
        });

        // Add total hours and total amount at the bottom
        var totalHours = document.getElementById('total-hours').innerText;
        var totalAmount = document.getElementById('total-amount').innerText;
        doc.text('Total Tracked Hours: ' + totalHours, 14, doc.lastAutoTable.finalY + 10);
        doc.text('Total Owed Amount: ' + totalAmount, 14, doc.lastAutoTable.finalY + 20);

        // Save the PDF
        doc.save('track-details.pdf');
    });

    document.getElementById('inviteButton').addEventListener('click', function () {
        // Get the form data
        const email = document.getElementById('email').value;
        
        var token = '{{ csrf_token() }}';
        // Perform Ajax request to send the invite
        fetch('/team/invite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            // Display success or error message
            if (data.success) {
                toastr.success(data.msg);
            } else {
                toastr.error(data.msg);
            }
        })
        .catch(error => {
            toastr.error(error);
        });
    });

    // Archive employee functionality
    document.querySelectorAll('.archive-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');
            
            document.getElementById('archiveEmployeeName').textContent = userName;
            document.getElementById('confirmArchive').setAttribute('data-user-id', userId);
        });
    });

    // Confirm archive
    document.getElementById('confirmArchive').addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        
        fetch('/team/archive-employee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                $('#archiveModal').modal('hide');
                location.reload(); // Reload to remove the archived employee from the list
            } else {
                toastr.error(data.message || 'Failed to archive employee');
            }
        })
        .catch(error => {
            toastr.error('Error archiving employee');
        });
    });

    // Load archived employees - using jQuery modal event
    $('#archivedEmployeesModal').on('shown.bs.modal', function() {
        console.log('Modal shown - loading archived employees...');
        loadArchivedEmployees();
    });

    // Also add click handler to the button as backup
    document.querySelector('[data-target="#archivedEmployeesModal"]').addEventListener('click', function() {
        console.log('Button clicked - will load archived employees when modal opens');
    });

    // Function to load archived employees
    function loadArchivedEmployees() {
        console.log('Loading archived employees...');
        const container = document.getElementById('archivedEmployeesList');
        container.innerHTML = '<p class="text-center text-muted">Loading archived employees...</p>';
        
        fetch('/team/archived-employees', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(employees => {
            console.log('Archived employees:', employees);
            
            if (employees.length === 0) {
                container.innerHTML = '<p class="text-center text-muted">No archived employees found</p>';
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-striped">';
            html += '<thead><tr><th>Name</th><th>Email</th><th>Archived Date</th><th>Action</th></tr></thead><tbody>';
            
            employees.forEach(employee => {
                const archivedDate = new Date(employee.deleted_at).toLocaleDateString();
                html += `
                    <tr>
                        <td>${employee.name}</td>
                        <td>${employee.email}</td>
                        <td>${archivedDate}</td>
                        <td>
                            <button class="btn btn-sm btn-success restore-btn" data-user-id="${employee.id}" data-name="${employee.name}">
                                <i class="fa fa-undo"></i> Restore
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
            
            // Add restore functionality
            document.querySelectorAll('.restore-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-name');
                    
                    if (confirm(`Are you sure you want to restore ${userName}?`)) {
                        fetch('/team/restore-employee', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ user_id: userId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                $('#archivedEmployeesModal').modal('hide');
                                location.reload(); // Reload to show the restored employee
                            } else {
                                toastr.error(data.message || 'Failed to restore employee');
                            }
                        })
                        .catch(error => {
                            toastr.error('Error restoring employee');
                        });
                    }
                });
            });
        })
        .catch(error => {
            console.error('Error loading archived employees:', error);
            container.innerHTML = `<p class="text-center text-danger">Error loading archived employees: ${error.message}</p>`;
        });
    }
});
</script>
@endsection