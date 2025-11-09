@extends('layouts.admin_master')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card-dashboard mb-4">
    <div class="card-dashboard-header">
        <i class="fas fa-calendar-alt"></i> Calendar
    </div>
    <div class="card-dashboard-sub-header">
        <div class="card-dashboard-sub-header-title">
            Company Events & Schedule
        </div>
        <div class="card-dashboard-sub-header-controls">
            @if(in_array(Auth::user()->type, ['admin', 'super_admin']))
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="showShifts">
                    <label class="form-check-label" for="showShifts">Show Employee Shifts</label>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#addEventModal">
                        <i class="fas fa-plus"></i> Add Event
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="exportCalendar()">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="importHolidays()">
                        <i class="fas fa-calendar-plus"></i> Import Holidays
                    </button>
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#pendingRequestsModal">
                        <i class="fas fa-clock"></i> Pending Requests
                    </button>
                </div>
            @else
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#timeOffModal">
                    <i class="fas fa-calendar-times"></i> Request Time Off
                </button>
                <button type="button" class="btn btn-sm btn-info" onclick="loadMyRequests()">
                    <i class="fas fa-list"></i> My Requests
                </button>
            @endif
        </div>
    </div>

    <div class="card-body">
        <!-- Event Type Color Legend -->
        <div class="mb-3">
            <small class="text-muted">Event Types: </small>
            <span class="badge mr-2" style="background-color: #2D2D2D; color: white;">Shifts</span>
            <span class="badge mr-2" style="background-color: #FFCD29; color: black;">Holidays</span>
            <span class="badge mr-2" style="background-color: #570AA0; color: white;">Markets</span>
            <span class="badge mr-2" style="background-color: #96BF48; color: white;">Meetings</span>
            <span class="badge mr-2" style="background-color: #6dabe4; color: white;">Events</span>
        </div>
        <div id="calendar"></div>
    </div>
</div>

@if(in_array(Auth::user()->type, ['admin', 'super_admin']))
<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eventForm">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Event Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_type">Event Type *</label>
                                <select class="form-control" id="event_type" name="type" required onchange="updateEventColor()">
                                    <option value="event">General Event</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="holiday">Holiday</option>
                                    <option value="market">Market</option>
                                    <option value="shift">Shift</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date *</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date *</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="all_day" name="all_day" checked>
                        <label class="form-check-label" for="all_day">All Day Event</label>
                    </div>
                    
                    <div id="timeFields" style="display: none;">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time">Start Time</label>
                                    <input type="time" class="form-control" id="start_time" name="start_time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input type="time" class="form-control" id="end_time" name="end_time">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Location <small class="text-muted">(Start typing to search)</small></label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Search for a location...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_color">Event Color</label>
                                <input type="color" class="form-control" id="event_color" name="color" value="#6dabe4">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="staff_tags">Staff Members & Attendees</label>
                        <select class="form-control" id="staff_tags" name="staff_tags[]" multiple>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ ucfirst($employee->type) }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select staff members to tag and mark as attendees</small>
                    </div>

                    <!-- Recurring Event Section -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring">
                        <label class="form-check-label" for="is_recurring">
                            <strong>Recurring Event</strong>
                        </label>
                    </div>

                    <div id="recurringFields" style="display: none;">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_pattern">Repeat Pattern</label>
                                    <select class="form-control" id="recurrence_pattern" name="recurrence_pattern">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_interval">Every</label>
                                    <input type="number" class="form-control" id="recurrence_interval" name="recurrence_interval" min="1" value="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_end_date">End Date (Optional)</label>
                                    <input type="date" class="form-control" id="recurrence_end_date" name="recurrence_end_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recurrence_count">Number of Occurrences (Optional)</label>
                                    <input type="number" class="form-control" id="recurrence_count" name="recurrence_count" min="1" max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pending Time Off Requests Modal -->
<div class="modal fade" id="pendingRequestsModal" tabindex="-1" role="dialog" aria-labelledby="pendingRequestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingRequestsModalLabel">Pending Time-Off Requests</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pendingRequestsList"></div>
            </div>
        </div>
    </div>
</div>

@else
<!-- Employee Time Off Request Modal -->
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
@endif

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                @if(in_array(Auth::user()->type, ['admin', 'super_admin']))
                <button type="button" class="btn btn-warning" id="editEventBtn">Edit</button>
                <button type="button" class="btn btn-danger" id="deleteEventBtn">Delete</button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- FullCalendar CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
/* Fix Select2 dropdown width and styling */
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--multiple {
    min-height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border: 1px solid #007bff;
    color: white;
    border-radius: 0.25rem;
    padding: 2px 8px;
    margin: 2px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #ffcccc;
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    z-index: 9999;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #007bff;
}

/* Modal z-index fix */
.modal .select2-container {
    z-index: 1060;
}

/* Location Autocomplete Dropdown */
.location-autocomplete {
    position: relative;
}

.location-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1070;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    display: none;
}

.location-suggestion {
    padding: 10px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.location-suggestion:hover {
    background-color: #f8f9fa;
}

.location-suggestion:last-child {
    border-bottom: none;
}

.location-suggestion.selected {
    background-color: #007bff;
    color: white;
}

.location-suggestion .place-name {
    font-weight: 500;
    display: block;
}

.location-suggestion .place-address {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
}

.location-suggestion.selected .place-address {
    color: #e3f2fd;
}

.location-loading {
    padding: 10px 12px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
}
</style>

<script>
let locationSearchTimeout = null;
let currentSuggestions = [];
let selectedSuggestionIndex = -1;

// Free location search using OpenStreetMap Nominatim
function initLocationAutocomplete() {
    const locationInput = document.getElementById('location');
    if (!locationInput) return;

    // Create suggestions container
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'location-suggestions';
    suggestionsContainer.id = 'locationSuggestions';
    
    // Wrap input in container
    const wrapper = document.createElement('div');
    wrapper.className = 'location-autocomplete';
    locationInput.parentNode.insertBefore(wrapper, locationInput);
    wrapper.appendChild(locationInput);
    wrapper.appendChild(suggestionsContainer);

    // Search for locations
    function searchLocations(query) {
        if (query.length < 3) {
            hideSuggestions();
            return;
        }

        showLoading();

        // Using Nominatim (OpenStreetMap) - completely free!
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=us&limit=5&addressdetails=1`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                currentSuggestions = data.map(item => ({
                    name: item.display_name.split(',')[0],
                    address: item.display_name,
                    lat: item.lat,
                    lon: item.lon
                }));
                showSuggestions(currentSuggestions);
            })
            .catch(error => {
                console.warn('Location search error:', error);
                hideSuggestions();
            });
    }

    function showLoading() {
        suggestionsContainer.innerHTML = '<div class="location-loading"><i class="fas fa-spinner fa-spin"></i> Searching locations...</div>';
        suggestionsContainer.style.display = 'block';
    }

    function showSuggestions(suggestions) {
        if (suggestions.length === 0) {
            hideSuggestions();
            return;
        }

        suggestionsContainer.innerHTML = '';
        suggestions.forEach((suggestion, index) => {
            const div = document.createElement('div');
            div.className = 'location-suggestion';
            div.innerHTML = `
                <div class="place-name">${suggestion.name}</div>
                <div class="place-address">${suggestion.address}</div>
            `;
            
            div.addEventListener('click', () => {
                selectSuggestion(suggestion);
            });
            
            suggestionsContainer.appendChild(div);
        });
        
        suggestionsContainer.style.display = 'block';
        selectedSuggestionIndex = -1;
    }

    function hideSuggestions() {
        suggestionsContainer.style.display = 'none';
        selectedSuggestionIndex = -1;
    }

    function selectSuggestion(suggestion) {
        locationInput.value = suggestion.address;
        hideSuggestions();
        
        // Visual feedback
        locationInput.style.borderColor = '#28a745';
        setTimeout(() => {
            locationInput.style.borderColor = '';
        }, 2000);
    }

    function updateSelectedSuggestion(direction) {
        const suggestions = suggestionsContainer.querySelectorAll('.location-suggestion');
        if (suggestions.length === 0) return;

        // Remove previous selection
        suggestions.forEach(s => s.classList.remove('selected'));

        // Update index
        if (direction === 'down') {
            selectedSuggestionIndex = (selectedSuggestionIndex + 1) % suggestions.length;
        } else if (direction === 'up') {
            selectedSuggestionIndex = selectedSuggestionIndex <= 0 ? suggestions.length - 1 : selectedSuggestionIndex - 1;
        }

        // Add new selection
        if (selectedSuggestionIndex >= 0) {
            suggestions[selectedSuggestionIndex].classList.add('selected');
        }
    }

    // Event listeners
    locationInput.addEventListener('input', function() {
        clearTimeout(locationSearchTimeout);
        locationSearchTimeout = setTimeout(() => {
            searchLocations(this.value);
        }, 300); // Debounce for 300ms
    });

    locationInput.addEventListener('keydown', function(e) {
        const suggestions = suggestionsContainer.querySelectorAll('.location-suggestion');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                updateSelectedSuggestion('down');
                break;
            case 'ArrowUp':
                e.preventDefault();
                updateSelectedSuggestion('up');
                break;
            case 'Enter':
                if (selectedSuggestionIndex >= 0 && suggestions[selectedSuggestionIndex]) {
                    e.preventDefault();
                    selectSuggestion(currentSuggestions[selectedSuggestionIndex]);
                }
                break;
            case 'Escape':
                hideSuggestions();
                break;
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            hideSuggestions();
        }
    });

    console.log('Free location autocomplete initialized (powered by OpenStreetMap)');
}

document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    
    let currentEvent = null;
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        events: function(info, successCallback, failureCallback) {
            const showShifts = document.getElementById('showShifts')?.checked || false;
            
            fetch('/calendar/events?' + new URLSearchParams({
                start: info.startStr,
                end: info.endStr,
                show_shifts: showShifts ? '1' : '0' 
            }), {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => successCallback(data))
            .catch(error => {
                failureCallback(error);
            });
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            // Only admins can create events by clicking dates
            @if(in_array(Auth::user()->type, ['admin', 'super_admin']))
            // Pre-fill the form with the clicked date
            document.getElementById('start_date').value = info.dateStr;
            document.getElementById('end_date').value = info.dateStr;
            
            // Reset form
            document.getElementById('eventForm').reset();
            document.getElementById('start_date').value = info.dateStr;
            document.getElementById('end_date').value = info.dateStr;
            
            // Show modal
            $('#addEventModal').modal('show');
            @endif
        },
        height: 'auto',
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });
    
    calendar.render();
    
    // Refresh calendar when show shifts checkbox changes
    const showShiftsCheckbox = document.getElementById('showShifts');
    if (showShiftsCheckbox) {
        showShiftsCheckbox.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    }
    
    // Initialize Select2 for staff tags with proper configuration
    if ($('#staff_tags').length > 0) {
        $('#staff_tags').select2({
            placeholder: 'Select staff members to tag',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addEventModal')
        });
    }

    // Initialize location autocomplete when modal opens
    $('#addEventModal').on('shown.bs.modal', function() {
        initLocationAutocomplete();
    });
    
    // Handle all day checkbox
    document.getElementById('all_day')?.addEventListener('change', function() {
        const timeFields = document.getElementById('timeFields');
        if (this.checked) {
            timeFields.style.display = 'none';
        } else {
            timeFields.style.display = 'block';
        }
    });

    // Handle recurring event checkbox
    document.getElementById('is_recurring')?.addEventListener('change', function() {
        const recurringFields = document.getElementById('recurringFields');
        if (this.checked) {
            recurringFields.style.display = 'block';
        } else {
            recurringFields.style.display = 'none';
        }
    });
    
    // Set minimum date for time off requests
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('timeoff_start_date')?.setAttribute('min', today);
    document.getElementById('timeoff_end_date')?.setAttribute('min', today);
    
    // Update end date minimum when start date changes
    document.getElementById('timeoff_start_date')?.addEventListener('change', function() {
        document.getElementById('timeoff_end_date').setAttribute('min', this.value);
    });
    
    // Event form submission
    document.getElementById('eventForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Handle staff_tags array
        data.staff_tags = Array.from(formData.getAll('staff_tags[]'));
        
        // Set attendees to be the same as staff_tags (combined functionality)
        data.attendees = Array.from(formData.getAll('staff_tags[]'));

        // Handle recurring event fields
        data.is_recurring = document.getElementById('is_recurring').checked;
        if (data.is_recurring) {
            data.recurrence_pattern = document.getElementById('recurrence_pattern').value;
            data.recurrence_interval = parseInt(document.getElementById('recurrence_interval').value);
            data.recurrence_end_date = document.getElementById('recurrence_end_date').value;
            data.recurrence_count = parseInt(document.getElementById('recurrence_count').value);
        }
        
        // Explicitly handle all_day checkbox (always send boolean value)
        data.all_day = document.getElementById('all_day').checked;
        
        // Check if this is an edit operation
        const eventId = this.getAttribute('data-event-id');
        const isEdit = !!eventId;
        const url = isEdit ? `/calendar/events/${eventId}` : '/calendar/events';
        const method = isEdit ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                $('#addEventModal').modal('hide');
                calendar.refetchEvents();
                toastr.success(data.message || (isEdit ? 'Event updated successfully!' : 'Event created successfully!'));
                this.reset();
                $('#staff_tags').val(null).trigger('change');
                document.getElementById('is_recurring').checked = false;
                document.getElementById('recurringFields').style.display = 'none';
            } else {
                // Handle validation errors
                if (data.errors) {
                    let errorMessage = 'Validation errors:<br>';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `${field}: ${data.errors[field][0]}<br>`;
                    });
                    toastr.error(errorMessage);
                } else {
                    toastr.error(data.message || (isEdit ? 'Error updating event' : 'Error creating event'));
                }
            }
        })
        .catch(error => {
            toastr.error((isEdit ? 'Error updating event: ' : 'Error creating event: ') + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    
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
                calendar.refetchEvents();
                toastr.success(data.message);
                this.reset();
            } else {
                toastr.error('Error submitting request');
            }
        })
        .catch(error => {
            toastr.error('Error submitting request');
        });
    });
    
    function showEventDetails(event) {
        const props = event.extendedProps;
        let content = `
            <div class="event-details">
                <h6><strong>${event.title}</strong></h6>`;
        
        // Show if this is a recurring event instance
        if (props.is_recurring_instance) {
            content += `<div class="alert alert-info mb-2">
                <i class="fas fa-sync-alt"></i> This is a recurring event instance
            </div>`;
        }
        
        content += `<p><strong>Date:</strong> ${event.start.toLocaleDateString()}`;
        
        if (event.end && event.end.toDateString() !== event.start.toDateString()) {
            content += ` - ${event.end.toLocaleDateString()}`;
        }
        content += `</p>`;
        
        if (!event.allDay && event.start) {
            content += `<p><strong>Time:</strong> ${event.start.toLocaleTimeString()}`;
            if (event.end) {
                content += ` - ${event.end.toLocaleTimeString()}`;
            }
            content += `</p>`;
        }
        
        if (props.type) {
            content += `<p><strong>Type:</strong> ${props.type.charAt(0).toUpperCase() + props.type.slice(1)}</p>`;
        }
        
        if (props.description) {
            content += `<p><strong>Description:</strong> ${props.description}</p>`;
        }
        
        if (props.location) {
            content += `<p><strong>Location:</strong> ${props.location}</p>`;
        }
        
        if (props.reason) {
            content += `<p><strong>Reason:</strong> ${props.reason}</p>`;
        }
        
        if (props.status) {
            const statusClass = props.status === 'approved' ? 'text-success' : 
                              props.status === 'denied' ? 'text-danger' : 'text-warning';
            content += `<p><strong>Status:</strong> <span class="${statusClass}">${props.status.toUpperCase()}</span></p>`;
        }
        
        if (props.notes) {
            content += `<p><strong>Notes:</strong> ${props.notes}</p>`;
        }
        
        // Show staff members and attendees (combined)
        if (props.staff_tags && props.staff_tags.length > 0) {
            content += `<p><strong>Staff Members & Attendees:</strong></p><ul>`;
            props.staff_tags.forEach(staffId => {
                const staffMember = @json($employees)?.find(emp => emp.id == staffId);
                if (staffMember) {
                    content += `<li>${staffMember.name} (${staffMember.type})</li>`;
                }
            });
            content += `</ul>`;
        }
        
        content += `</div>`;
        
        document.getElementById('eventDetailsContent').innerHTML = content;
        currentEvent = event;
        
        // Update edit and delete button visibility/behavior for recurring events
        const editBtn = document.getElementById('editEventBtn');
        const deleteBtn = document.getElementById('deleteEventBtn');
        
        if (props.is_recurring_instance) {
            // For recurring instances, change button text to indicate special behavior
            if (editBtn) editBtn.textContent = 'Edit Series';
            if (deleteBtn) deleteBtn.textContent = 'Delete Series';
        } else {
            // Regular events
            if (editBtn) editBtn.textContent = 'Edit';
            if (deleteBtn) deleteBtn.textContent = 'Delete';
        }
        
        $('#eventDetailsModal').modal('show');
    }
    
    // Load pending requests for admin
    window.loadPendingRequests = function() {
        fetch('/calendar/time-off-requests/pending', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(requests => {
            let content = '';
            if (requests.length === 0) {
                content = '<p class="text-center text-muted">No pending requests</p>';
            } else {
                requests.forEach(request => {
                    // Format dates to show date and time in AM/PM format
                    const startDate = new Date(request.start_date).toLocaleString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    const endDate = new Date(request.end_date).toLocaleString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    
                    content += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6><strong>${request.user.name}</strong></h6>
                                <p><strong>Dates:</strong> ${startDate} to ${endDate}</p>
                                <p><strong>Reason:</strong> ${request.reason}</p>
                                ${request.notes ? `<p><strong>Notes:</strong> ${request.notes}</p>` : ''}
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-success" onclick="reviewRequest(${request.id}, 'approve')">Approve</button>
                                    <button class="btn btn-sm btn-danger" onclick="reviewRequest(${request.id}, 'deny')">Deny</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            document.getElementById('pendingRequestsList').innerHTML = content;
        });
    };
    
    // Review time off request
    window.reviewRequest = function(requestId, action) {
        const adminNotes = prompt(`${action === 'approve' ? 'Approval' : 'Denial'} notes (optional):`);
        
        fetch(`/calendar/time-off-requests/${requestId}/review`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                admin_notes: adminNotes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                loadPendingRequests();
                calendar.refetchEvents();
            } else {
                toastr.error('Error processing request');
            }
        });
    };
    
    // Load my requests for employees
    window.loadMyRequests = function() {
        fetch('/calendar/my-time-off-requests', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(requests => {
            let content = '<div class="modal fade" id="myRequestsModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">';
            content += '<div class="modal-header"><h5>My Time-Off Requests</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>';
            content += '<div class="modal-body">';
            
            if (requests.length === 0) {
                content += '<p class="text-center text-muted">No requests found</p>';
            } else {
                requests.forEach(request => {
                    const statusClass = request.status === 'approved' ? 'text-success' : 
                                      request.status === 'denied' ? 'text-danger' : 'text-warning';
                    
                    // Format dates to show date and time in AM/PM format
                    const startDate = new Date(request.start_date).toLocaleString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    const endDate = new Date(request.end_date).toLocaleString('en-US', {
                        month: 'numeric',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    
                    content += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <p><strong>Dates:</strong> ${startDate} to ${endDate}</p>
                                <p><strong>Reason:</strong> ${request.reason}</p>
                                <p><strong>Status:</strong> <span class="${statusClass}">${request.status.toUpperCase()}</span></p>
                                ${request.notes ? `<p><strong>Notes:</strong> ${request.notes}</p>` : ''}
                                ${request.admin_notes ? `<p><strong>Admin Notes:</strong> ${request.admin_notes}</p>` : ''}
                            </div>
                        </div>
                    `;
                });
            }
            
            content += '</div></div></div></div>';
            
            // Remove existing modal if any
            $('#myRequestsModal').remove();
            
            // Add new modal
            $('body').append(content);
            $('#myRequestsModal').modal('show');
        });
    };
    
    // Load pending requests when modal opens
    $('#pendingRequestsModal').on('show.bs.modal', function() {
        loadPendingRequests();
    });

    // Export calendar function
    window.exportCalendar = function() {
        const startDate = new Date();
        const endDate = new Date();
        endDate.setMonth(endDate.getMonth() + 3);
        
        const params = new URLSearchParams({
            start_date: startDate.toISOString().split('T')[0],
            end_date: endDate.toISOString().split('T')[0]
        });
        
        window.location.href = '/calendar/export?' + params.toString();
    };

    // Import holidays function
    window.importHolidays = function() {
        const year = new Date().getFullYear();
        const confirmImport = confirm(`Import US holidays for ${year}? This will add all major federal holidays to your calendar.`);
        
        if (confirmImport) {
            fetch('/calendar/import-holidays', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ year: year })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                    calendar.refetchEvents();
                } else {
                    toastr.error(data.message || 'Failed to import holidays');
                }
            })
            .catch(error => {
                console.error('Error importing holidays:', error);
                toastr.error('Error importing holidays');
            });
        }
    };

    // Update event color based on type
    window.updateEventColor = function() {
        const typeSelect = document.getElementById('event_type');
        const colorInput = document.getElementById('event_color');
        
        const colorMap = {
            'shift': '#2D2D2D',
            'holiday': '#FFCD29',
            'market': '#570AA0',
            'meeting': '#96BF48',
            'event': '#6dabe4'
        };
        
        const selectedType = typeSelect.value;
        if (colorMap[selectedType]) {
            colorInput.value = colorMap[selectedType];
        }
    };
    
    // Handle Edit Event button click
    document.getElementById('editEventBtn')?.addEventListener('click', function() {
        if (!currentEvent) return;
        
        const props = currentEvent.extendedProps;
        
        // If this is a recurring instance, we need to edit the parent event instead
        let eventIdToEdit = currentEvent.id;
        if (props.is_recurring_instance && props.parent_event_id) {
            eventIdToEdit = props.parent_event_id;
            
            // Fetch the parent event details to populate the form
            fetch(`/calendar/events/${props.parent_event_id}/details`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(parentEvent => {
                populateEditForm(parentEvent, eventIdToEdit);
            })
            .catch(error => {
                console.error('Error fetching parent event:', error);
                // Fallback to current event data
                populateEditFormFromCurrentEvent();
            });
        } else {
            // Regular event
            populateEditFormFromCurrentEvent();
        }
    });
    
    function populateEditForm(eventData, eventId) {
        // Close event details modal
        $('#eventDetailsModal').modal('hide');
        
        // Populate the form with event data
        document.getElementById('title').value = eventData.title || '';
        document.getElementById('event_type').value = eventData.type || 'event';
        document.getElementById('description').value = eventData.description || '';
        document.getElementById('start_date').value = eventData.start_date || '';
        document.getElementById('end_date').value = eventData.end_date || '';
        document.getElementById('location').value = eventData.location || '';
        document.getElementById('notes').value = eventData.notes || '';
        document.getElementById('event_color').value = eventData.color || '#6dabe4';
        document.getElementById('all_day').checked = eventData.all_day || false;
        
        // Handle recurring fields
        document.getElementById('is_recurring').checked = eventData.is_recurring || false;
        if (eventData.is_recurring) {
            document.getElementById('recurringFields').style.display = 'block';
            document.getElementById('recurrence_pattern').value = eventData.recurrence_pattern || '';
            document.getElementById('recurrence_interval').value = eventData.recurrence_interval || 1;
            document.getElementById('recurrence_end_date').value = eventData.recurrence_end_date || '';
            document.getElementById('recurrence_count').value = eventData.recurrence_count || '';
        } else {
            document.getElementById('recurringFields').style.display = 'none';
        }
        
        // Handle time fields
        if (!eventData.allDay && eventData.start_time) {
            document.getElementById('start_time').value = eventData.start_time;
            if (eventData.end_time) {
                document.getElementById('end_time').value = eventData.end_time;
            }
            document.getElementById('timeFields').style.display = 'block';
        } else {
            document.getElementById('timeFields').style.display = 'none';
        }
        
        // Handle staff tags
        if (eventData.staff_tags && Array.isArray(eventData.staff_tags)) {
            $('#staff_tags').val(eventData.staff_tags).trigger('change');
        } else {
            $('#staff_tags').val([]).trigger('change');
        }

        // Handle attendees
        if (eventData.attendees && Array.isArray(eventData.attendees)) {
            $('#attendees').val(eventData.attendees).trigger('change');
        } else {
            $('#attendees').val([]).trigger('change');
        }
        
        // Change form to edit mode
        document.getElementById('addEventModalLabel').textContent = 'Edit Event';
        document.getElementById('eventForm').setAttribute('data-event-id', eventId);
        
        // Show the modal
        $('#addEventModal').modal('show');
    }
    
    function populateEditFormFromCurrentEvent() {
        const props = currentEvent.extendedProps;
        
        // Close event details modal
        $('#eventDetailsModal').modal('hide');
        
        // Populate the form with current event data
        document.getElementById('title').value = currentEvent.title || '';
        document.getElementById('event_type').value = props.type || 'event';
        document.getElementById('description').value = props.description || '';
        document.getElementById('start_date').value = currentEvent.start.toISOString().split('T')[0];
        document.getElementById('end_date').value = currentEvent.end ? 
            new Date(currentEvent.end.getTime() - 86400000).toISOString().split('T')[0] : // Subtract 1 day because FullCalendar adds 1 day for all-day events
            currentEvent.start.toISOString().split('T')[0];
        document.getElementById('location').value = props.location || '';
        document.getElementById('notes').value = props.notes || '';
        document.getElementById('event_color').value = currentEvent.backgroundColor || '#6dabe4';
        document.getElementById('all_day').checked = currentEvent.allDay;
        
        // Handle time fields
        if (!currentEvent.allDay && currentEvent.start) {
            document.getElementById('start_time').value = currentEvent.start.toTimeString().slice(0, 5);
            if (currentEvent.end) {
                document.getElementById('end_time').value = currentEvent.end.toTimeString().slice(0, 5);
            }
            document.getElementById('timeFields').style.display = 'block';
        } else {
            document.getElementById('timeFields').style.display = 'none';
        }
        
        // Handle staff tags
        if (props.staff_tags && Array.isArray(props.staff_tags)) {
            $('#staff_tags').val(props.staff_tags).trigger('change');
        } else {
            $('#staff_tags').val([]).trigger('change');
        }

        // Handle attendees
        if (props.attendees && Array.isArray(props.attendees)) {
            $('#attendees').val(props.attendees).trigger('change');
        } else {
            $('#attendees').val([]).trigger('change');
        }
        
        // Change form to edit mode
        document.getElementById('addEventModalLabel').textContent = 'Edit Event';
        document.getElementById('eventForm').setAttribute('data-event-id', currentEvent.id);
        
        // Show the modal
        $('#addEventModal').modal('show');
    }
    
    // Handle Delete Event button click
    document.getElementById('deleteEventBtn')?.addEventListener('click', function() {
        if (!currentEvent) return;

        const props = currentEvent.extendedProps;
        let confirmMessage = 'Are you sure you want to delete this event?';
        
        // For recurring instances, clarify that the whole series will be deleted
        if (props.is_recurring_instance) {
            confirmMessage = 'Are you sure you want to delete this recurring event series? This will remove all instances of this event.';
        }

        if (confirm(confirmMessage)) {
            const isTimeOffRequest = currentEvent.id.toString().startsWith('timeoff-');
            let deleteUrl;
            
            if (isTimeOffRequest) {
                // Extract the time-off request ID from the event ID
                const timeOffId = currentEvent.id.replace('timeoff-', '');
                deleteUrl = `/calendar/time-off-requests/${timeOffId}`;
            } else if (props.is_recurring_instance && props.parent_event_id) {
                // For recurring instances, delete the parent event
                deleteUrl = `/calendar/events/${props.parent_event_id}`;
            } else {
                // Regular calendar event
                deleteUrl = `/calendar/events/${currentEvent.id}`;
            }
            
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    $('#eventDetailsModal').modal('hide');
                    calendar.refetchEvents();
                    toastr.success(data.message || 'Event deleted successfully!');
                } else {
                    toastr.error(data.message || 'Error deleting event');
                }
            })
            .catch(error => {
                toastr.error('Error deleting event: ' + error.message);
            });
        }
    });
    
    // Reset form when Add Event modal is closed
    $('#addEventModal').on('hidden.bs.modal', function() {
        document.getElementById('addEventModalLabel').textContent = 'Add New Event';
        document.getElementById('eventForm').removeAttribute('data-event-id');
        document.getElementById('eventForm').reset();
        $('#staff_tags').val([]).trigger('change');
        document.getElementById('timeFields').style.display = 'none';
        document.getElementById('all_day').checked = true;
    });
    
    // Configure toastr options to allow HTML
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "allowHtml": true
    };

    // Check for success/error messages on page load
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
});
</script>
@endsection 