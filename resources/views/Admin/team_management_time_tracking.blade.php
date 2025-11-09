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

.unpaid-amount {
    color: red;
}

.calendar-widget {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 20px;
}

.mini-calendar-event {
    padding: 5px 8px;
    margin: 2px 0;
    border-radius: 4px;
    font-size: 12px;
}

.event-holiday { background-color: #dc3545; }
.event-market { background-color: #28a745; }
.event-popup { background-color: #ffc107; color: #000; }
.event-general { background-color: #007bff; }
.event-timeoff { background-color: #6c757d; }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">


<div class="card-dashboard mb-4">


    <!-- Calendar Widget for Employee -->
    <div class="calendar-widget">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Upcoming Events & Schedule</h6>
            <div>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#timeOffModal">
                    <i class="fas fa-calendar-times"></i> Request Time Off
                </button>
                <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-calendar"></i> Full Calendar
                </a>
            </div>
        </div>
        <div id="upcomingEvents">
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin"></i> Loading events...
            </div>
        </div>
    </div>
    <div class="card-dashboard-header">
        Time Tracking
    </div>
    <div class="card-dashboard-sub-header">
        <span class="ml-3">Hourly Rate: <strong>${{ $hourlyRate }}</strong></span>
        <span class="ml-3 unpaid-amount">Total Unpaid Amount:
            <strong>${{ number_format($totalUnpaidAmount, 2) }}</strong></span>

        <span class="ml-3">Total Paid Hours: <strong>{{ $totalPaidHours }}</strong></span>
        <span class="ml-3">Total Paid Amount: <strong>${{ number_format($totalPaidAmount, 2) }}</strong></span>

        <button id="clockInButton" type="button" class="btn btn-sm btn-success float-right" style="margin-right: 10px;">
            Clock In
        </button>
        <button id="clockOutButton" type="button" class="btn btn-sm btn-danger float-right" style="margin-right: 10px;">
            Clock Out
        </button>
<!-- 
        <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
            data-target="#addTrackingModal" style="margin-right: 20px;">
            <i class="fas fa-plus"></i> Add
        </button> -->
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Arrival Time</th>
                        <th>Departure Time</th>
                        <th>Hours</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="post-data">
                    @include('Admin.time_tracking_table')
                </tbody>
            </table>
            <div class="ajax-load text-center" style="display:none">
                <span class="spinner-border text-primary" role="status"></span>
            </div>
            {{-- <x-pagination :fechtedData="$materials" /> --}}
        </div>
    </div>
    <div class="modal fade" id="addTrackingModal" tabindex="-1" role="dialog" aria-labelledby="addTrackingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTrackingModalLabel">Add New Time Track</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ url('/team/time_tracking/add') }}" enctype="multipart/form-data"
                        onsubmit="return validateForm()">
                        @csrf
                        <input type="hidden" id="arrivalTime" name="arrival_time">
                        <input type="hidden" id="departureTime" name="departure_time">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="inputFirstName">Select Date</label>
                                    <div id="add_date_picker" class="calendar-daterange">
                                        <span></span>
                                        <img src="{{ asset('images/svg/down_arrow.svg') }}" alt="Arrow Icon" class="arrow-icon">
                                        <img src="{{ asset('images/svg/calendar.svg') }}" alt="Calendar Icon" class="calendar-icon">
                                        <input class="form-control" name="date" type="date" hidden />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="arrivalHour">Arrival Time</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-1" id="arrivalHour"
                                            name="arrival_hour" placeholder="HH" min="0" max="23"
                                            oninput="validateTime(this); calculateHours()">
                                        <span class="mr-1">:</span>
                                        <input type="number" class="form-control" id="arrivalMinute"
                                            name="arrival_minute" placeholder="MM" min="0" max="59"
                                            oninput="validateTime(this); calculateHours()">
                                    </div>
                                </div>
                            </div>
                            <!-- Departure Time Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="departureHour">Departure Time</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-1" id="departureHour"
                                            name="departure_hour" placeholder="HH" min="0" max="23"
                                            oninput="validateTime(this); calculateHours()">
                                        <span class="mr-1">:</span>
                                        <input type="number" class="form-control" id="departureMinute"
                                            name="departure_minute" placeholder="MM" min="0" max="59"
                                            oninput="validateTime(this); calculateHours()">
                                    </div>
                                </div>
                            </div>
                            <!-- Calculated Hours Display -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small mb-1" for="calculatedHours">Calculated Hours</label>
                                    <input class="form-control" id="calculatedHours" type="number" name="hours"
                                        placeholder="Calculated Hours" value="0" readonly />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1">Notes</label>
                                    <textarea id="textarea_notes" class="form-control left-aligned-textarea"
                                        name="notes" placeholder="">
                                        </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 mb-0"><button class="btn btn-primary btn-block">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Time Off Request Modal -->
<div class="modal fade" id="timeOffModal" tabindex="-1" role="dialog" aria-labelledby="timeOffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timeOffModalLabel">Request Time Off</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="timeOffForm">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="timeoff_start_date">Start Date *</label>
                                <input type="date" class="form-control" id="timeoff_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="timeoff_end_date">End Date *</label>
                                <input type="date" class="form-control" id="timeoff_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason *</label>
                        <select class="form-control" id="reason" name="reason" required>
                            <option value="">Select a reason</option>
                            <option value="Vacation">Vacation</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Personal">Personal</option>
                            <option value="Family Emergency">Family Emergency</option>
                            <option value="Medical Appointment">Medical Appointment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="timeoff_notes">Additional Notes</label>
                        <textarea class="form-control" id="timeoff_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
function validateTime(input) {
    let value = parseInt(input.value, 10);

    if (input.id.includes('Hour') && (value < 0 || value > 23)) {
        input.value = value < 0 ? 0 : 23;
    }

    if (input.id.includes('Minute') && (value < 0 || value > 59)) {
        input.value = value < 0 ? 0 : 59;
    }
}

function getCurrentLocalDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function add_date_picker_change(start) {
    $('#add_date_picker span').html(start.format('MMMM D, YYYY'));
    $('#add_date_picker input').val(start.format('YYYY-MM-DD'));
    //fetchSalesData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
}

$('#add_date_picker').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    startDate: moment(),
}, add_date_picker_change);


function calculateHours() {
    let arrivalHour = parseInt(document.getElementById('arrivalHour').value || 0, 10);
    let arrivalMinute = parseInt(document.getElementById('arrivalMinute').value || 0, 10);
    let departureHour = parseInt(document.getElementById('departureHour').value || arrivalHour, 10);
    let departureMinute = parseInt(document.getElementById('departureMinute').value || arrivalMinute, 10);

    let arrivalTime = `${arrivalHour.toString().padStart(2, '0')}:${arrivalMinute.toString().padStart(2, '0')}`;
    let departureTime =
        `${departureHour.toString().padStart(2, '0')}:${departureMinute.toString().padStart(2, '0')}`;

    let arrivalTotalMinutes = arrivalHour * 60 + arrivalMinute;
    let departureTotalMinutes = departureHour * 60 + departureMinute;

    let minutesDifference = departureTotalMinutes - arrivalTotalMinutes;
    if (minutesDifference < 0) {
        minutesDifference += 24 * 60; // account for crossing midnight
    }

    let hoursDifference = minutesDifference / 60;

    document.getElementById('calculatedHours').value = hoursDifference.toFixed(2);

    document.getElementById('arrivalTime').value = arrivalTime;
    document.getElementById('departureTime').value = departureTime;
}

function updateHiddenFields() {
    let arrivalHour = $('#arrivalHour').val().padStart(2, '0');
    let arrivalMinute = $('#arrivalMinute').val().padStart(2, '0');
    let departureHour = $('#departureHour').val().padStart(2, '0');
    let departureMinute = $('#departureMinute').val().padStart(2, '0');

    $('#arrivalTime').val(`${arrivalHour}:${arrivalMinute}`);
    $('#departureTime').val(`${departureHour}:${departureMinute}`);
}


$(document).ready(function() {

    const currentDate = getCurrentLocalDate();

    console.log(currentDate);

    fetch('/team/time_tracking/get_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            date: currentDate // Send the current date to the server
        })
    }).then(response => response.json())
    .then(data => {
        // Assuming the response contains 'arrival_time' and 'departure_time'
        const arrivalTime = data.arrival_time || null;
        const departureTime = data.departure_time || null;

        // Function to set the button states based on arrival and departure status
        function setButtonStates(arrival, departure) {
            if (!arrival && !departure) {
                // No times set: enable clock in, disable clock out
                clockInButton.disabled = false;
                clockOutButton.disabled = true;
            } else if (arrival && !departure) {
                // Arrival set, but no departure: disable clock in, enable clock out
                clockInButton.disabled = true;
                clockOutButton.disabled = false;
            } else if (arrival && departure) {
                // Both times set: disable both buttons
                clockInButton.disabled = true;
                clockOutButton.disabled = true;
            }
        }

        // Call the function to set button states
        setButtonStates(arrivalTime, departureTime);
    })
    .catch(error => {
        console.error('Error fetching status:', error);
        // Optionally, handle error by disabling both buttons
        clockInButton.disabled = true;
        clockOutButton.disabled = true;
    });




    // Initialize daterangepicker for time selection only

    $('#textarea_notes').val('');

    // When the 'Add' button is clicked
    $('button[data-target="#addTrackingModal"]').click(function() {
        // Reset the form inside the modal to its default values
        $('#addTrackingModal form')[0].reset();

        // Set the default values
        var start = moment(); // or use moment with a specific date
        $('#add_date_picker span').html(start.format('MMMM D, YYYY'));

        $('#addTrackingModal input[name="date"]').val(start.format(
            'YYYY-MM-DD')); // Set to current date
        $('#addTrackingModal input[name="hours"]').val('0');
        $('#addTrackingModal textarea[name="notes"]').val('');


        // Update the modal title to 'Add New Tracking'
        $('#addTrackingModalLabel').text('Add New Tracking');

        // Clear any previous action attribute from the form
        $('#addTrackingModal form').attr('action', "{{ url('/team/time_tracking/add') }}");

    });

    // When the 'Edit' button is clicked
    $('.edit-btn').click(function() {
        // Get data attributes from the button
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var date = row.find('td:eq(2)').text();
        var arrival_time = row.find('td:eq(3)').text();
        var departure_time = row.find('td:eq(4)').text();
        var hours = row.find('td:eq(5)').text();
        var notes = row.find('td:eq(6)').text();

        var formattedArrivalTime = moment(arrival_time, 'HH:mm:ss').format('h:mm A');
        var formattedDepartureTime = moment(departure_time, 'HH:mm:ss').format('h:mm A');

        var arrivalHour = formattedArrivalTime.split(':')[0];
        var arrivalMinute = formattedArrivalTime.split(':')[1];
        var departureHour = formattedDepartureTime.split(':')[0];
        var departureMinute = formattedDepartureTime.split(':')[1];

        // Format the date and set the value of the modal inputs
        var formattedDate = moment(date, 'YYYY-MM-DD').format('MMMM D, YYYY');
        $('#add_date_picker span').html(formattedDate);
        $('#addTrackingModal input[name="date"]').val(date);
        $('#addTrackingModal input[name="arrival_hour"]').val(arrivalHour);
        $('#addTrackingModal input[name="arrival_minute"]').val(arrivalMinute);
        $('#addTrackingModal input[name="departure_hour"]').val(departureHour);
        $('#addTrackingModal input[name="departure_minute"]').val(departureMinute);
        $('#addTrackingModal input[name="hours"]').val(hours);
        $('#addTrackingModal textarea[name="notes"]').val(notes);

        // Set the form action to include the id
        $('#addTrackingModal form').attr('action', '/team/time_tracking/edit/' + id);

        // Update the modal title to 'Edit Time Track'
        $('#addTrackingModalLabel').text('Edit Time Track');

        // Show the modal
        $('#addTrackingModal').modal('show');
    });

    // Update hidden fields when time inputs change
    $('input[name="arrival_hour"], input[name="arrival_minute"], input[name="departure_hour"], input[name="departure_minute"]')
        .on('input', function() {
            updateHiddenFields();
            calculateHours();
        });

    $('.delete-btn').click(function() {
        if (confirm('Are you sure you want to delete this item?')) {
            var itemId = $(this).data('id');
            var token = '{{ csrf_token() }}';

            $.ajax({
                url: '/team/time_tracking/' + itemId,
                type: 'DELETE',
                data: {
                    '_token': token,
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success);
                        location.reload();
                    } else {
                        toastr.error('Failed to delete the item.');
                    }
                },
                error: function(response) {
                    toastr.error('An error occurred while deleting the item.');
                }
            });
        }
    });
});

$('#addTrackingModal form').on('submit', function(event) {
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
                toastr.success('Success!');
                setTimeout(function() {
                    location.reload(); // Reload the page after a delay
                }, 2000); // Delay of 2 seconds before reloading
            } else {
                toastr.error('Failed!');
            }
        },
        error: function() {
            toastr.error('An error occurred!');
        }
    });
});

function validateForm() {
    let arrivalHour = parseInt($('#arrivalHour').val() || 0, 10);
    let arrivalMinute = parseInt($('#arrivalMinute').val() || 0, 10);
    let departureHour = parseInt($('#departureHour').val() || 0, 10);
    let departureMinute = parseInt($('#departureMinute').val() || 0, 10);

    let arrivalTime = arrivalHour + arrivalMinute / 60;
    let departureTime = departureHour + departureMinute / 60;

    // if (departureTime <= arrivalTime) {
    //     toastr.error('Departure time must be after arrival time.');
    //     return false;
    // }

    updateHiddenFields();
    return true;
}

function getCurrentLocalTime() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    // Return in 'YYYY-MM-DD HH:MM:SS' format
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

document.addEventListener('DOMContentLoaded', function () {
    const clockInButton = document.getElementById('clockInButton');
    const clockOutButton = document.getElementById('clockOutButton');

    clockInButton.addEventListener('click', () => {
        if (confirm('Are you sure you want to Clock In?')) {
            const arrivalTime = getCurrentLocalTime();
            saveTime('clock_in', arrivalTime);
        }
    });

    clockOutButton.addEventListener('click', () => {
        if (confirm('Are you sure you want to Clock Out?')) {
            const departureTime = getCurrentLocalTime();
            saveTime('clock_out', departureTime);
        }
    });

    function saveTime(action, time) {
        console.log(time);
        fetch('/team/time_tracking/record_time', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                time: time
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(`${action} time saved: ${time}`);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => toastr.error('Error:', error));
    }
});

// Load upcoming events for employee
function loadUpcomingEvents() {
    const today = new Date();
    const nextWeek = new Date();
    nextWeek.setDate(today.getDate() + 7);
    
    fetch('/calendar/events?' + new URLSearchParams({
        start: today.toISOString().split('T')[0],
        end: nextWeek.toISOString().split('T')[0]
    }), {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(events => {
        const container = document.getElementById('upcomingEvents');
        
        if (events.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No upcoming events this week</p>';
            return;
        }
        
        let html = '';
        events.forEach(event => {
            const eventDate = new Date(event.start);
            const eventClass = `event-${event.extendedProps.type || 'general'}`;
            
            html += `
                <div class="mini-calendar-event ${eventClass}" title="${event.extendedProps.description || ''}">
                    <strong>${eventDate.toLocaleDateString()}</strong> - ${event.title}
                    ${event.extendedProps.status ? `<span class="badge badge-${event.extendedProps.status === 'approved' ? 'success' : event.extendedProps.status === 'denied' ? 'danger' : 'warning'}">${event.extendedProps.status}</span>` : ''}
                </div>
            `;
        });
        
        container.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading events:', error);
        document.getElementById('upcomingEvents').innerHTML = '<p class="text-danger text-center">Error loading events</p>';
    });
}

// Time off form submission
document.getElementById('timeOffForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/calendar/time-off-requests', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#timeOffModal').modal('hide');
            toastr.success(data.message);
            this.reset();
            loadUpcomingEvents(); // Refresh events
        } else {
            toastr.error('Error submitting request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Error submitting request');
    });
});

// Set minimum date for time off requests
const today = new Date().toISOString().split('T')[0];
document.getElementById('timeoff_start_date')?.setAttribute('min', today);
document.getElementById('timeoff_end_date')?.setAttribute('min', today);

// Update end date minimum when start date changes
document.getElementById('timeoff_start_date')?.addEventListener('change', function() {
    document.getElementById('timeoff_end_date').setAttribute('min', this.value);
});

// Load events when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadUpcomingEvents();
});
</script>
@endsection