<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarEvent;
use App\Models\TimeOffRequest;
use App\Models\TeamManagement;
use App\Models\User;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the calendar view based on user type
     */
    public function index()
    {
        $user = Auth::user();
        $companyId = session('company_id');
        
        // Get company employees for admin views
        $employees = [];
        if (in_array($user->type, ['admin', 'super_admin'])) {
            $employees = User::where('company_id', $companyId)
                           ->whereNull('deleted_at')
                           ->select('id', 'name', 'email', 'type')
                           ->get();
        }

        return view('Admin.calendar', compact('employees'));
    }

    /**
     * Get calendar events for FullCalendar
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $companyId = session('company_id');
        $start = $request->input('start');
        $end = $request->input('end');
        $showShifts = filter_var($request->input('show_shifts', false), FILTER_VALIDATE_BOOLEAN);

        $events = collect();

        // Get regular calendar events (non-recurring) within date range
        $calendarEvents = CalendarEvent::forCompany($companyId)
            ->where('is_recurring', false)
            ->inDateRange($start, $end)
            ->with('creator')
            ->get();

        foreach ($calendarEvents as $event) {
            $events->push($event->full_calendar_event);
        }

        // Get all recurring events and generate instances within the date range
        $recurringEvents = CalendarEvent::forCompany($companyId)
            ->where('is_recurring', true)
            ->with('creator')
            ->get();

        foreach ($recurringEvents as $event) {
            // Generate instances for this recurring event within the requested date range
            $instances = $this->generateRecurringInstancesInRange($event, $start, $end);
            foreach ($instances as $instance) {
                $events->push($instance);
            }
        }

        // Admin/Super Admin can see time-off requests
        if (in_array($user->type, ['admin', 'super_admin'])) {
            $timeOffRequests = TimeOffRequest::forCompany($companyId)
                ->with('user')
                ->whereBetween('start_date', [$start, $end])
                ->get();

            foreach ($timeOffRequests as $request) {
                $events->push($request->full_calendar_event);
            }

            // Show employee shifts if toggled on
            if ($showShifts) {
                $shifts = TeamManagement::whereHas('user', function($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    })
                    ->with('user')
                    ->whereBetween('date', [$start, $end])
                    ->get();

                foreach ($shifts as $shift) {
                    $events->push([
                        'id' => 'shift-' . $shift->id,
                        'title' => $shift->user->name . ' - Shift',
                        'start' => $shift->date . 'T' . ($shift->arrival_time ?? '09:00:00'),
                        'end' => $shift->date . 'T' . ($shift->departure_time ?? '17:00:00'),
                        'backgroundColor' => '#17a2b8',
                        'borderColor' => '#17a2b8',
                        'extendedProps' => [
                            'type' => 'shift',
                            'user_name' => $shift->user->name,
                            'hours' => $shift->hours,
                            'notes' => $shift->notes,
                            'paid' => $shift->paid
                        ]
                    ]);
                }
            }
        } else {
            // Employees can see their own shifts and time-off requests
            $userShifts = TeamManagement::where('user_id', $user->id)
                ->whereBetween('date', [$start, $end])
                ->get();

            foreach ($userShifts as $shift) {
                $events->push([
                    'id' => 'shift-' . $shift->id,
                    'title' => 'My Shift',
                    'start' => $shift->date . 'T' . ($shift->arrival_time ?? '09:00:00'),
                    'end' => $shift->date . 'T' . ($shift->departure_time ?? '17:00:00'),
                    'backgroundColor' => '#17a2b8',
                    'borderColor' => '#17a2b8',
                    'extendedProps' => [
                        'type' => 'my_shift',
                        'hours' => $shift->hours,
                        'notes' => $shift->notes,
                        'paid' => $shift->paid
                    ]
                ]);
            }

            // Employee's own time-off requests
            $myTimeOffRequests = TimeOffRequest::forUser($user->id)
                ->whereBetween('start_date', [$start, $end])
                ->get();

            foreach ($myTimeOffRequests as $request) {
                $events->push($request->full_calendar_event);
            }
        }

        return response()->json($events->toArray());
    }

    /**
     * Store a new calendar event
     */
    public function storeEvent(Request $request)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can create events
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if company_id is available in session
        $companyId = session('company_id');
        if (!$companyId) {
            return response()->json([
                'success' => false, 
                'message' => 'Company session not found. Please login again.'
            ], 400);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i',
                'type' => 'required|in:event,holiday,market,shift,meeting',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'staff_tags' => 'nullable|array',
                'staff_tags.*' => 'exists:users,id',
                'attendees' => 'nullable|array',
                'attendees.*' => 'exists:users,id',
                'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
                'all_day' => 'boolean',
                'is_recurring' => 'boolean',
                'recurrence_pattern' => 'nullable|in:daily,weekly,monthly,yearly',
                'recurrence_interval' => 'nullable|integer|min:1',
                'recurrence_days' => 'nullable|array',
                'recurrence_end_date' => 'nullable|date|after:start_date',
                'recurrence_count' => 'nullable|integer|min:1|max:100'
            ]);

            $validated['created_by'] = $user->id;
            $validated['company_id'] = $companyId;
            
            // Set default color based on type if not provided
            if (!isset($validated['color']) || empty($validated['color'])) {
                $validated['color'] = CalendarEvent::getDefaultColorForType($validated['type']);
            }

            $event = CalendarEvent::create($validated);

            // Generate recurring instances if needed
            if ($event->is_recurring) {
                $instances = $event->generateRecurringInstances();
                foreach ($instances as $instance) {
                    CalendarEvent::create($instance);
                }
            }

            return response()->json([
                'success' => true,
                'event' => $event->fresh()->full_calendar_event,
                'message' => 'Event created successfully!' . ($event->is_recurring ? ' Recurring instances generated.' : '')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the event'
            ], 500);
        }
    }

    /**
     * Update an existing event
     */
    public function updateEvent(Request $request, CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can update events
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if event belongs to user's company
        if ($event->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'type' => 'required|in:event,holiday,market,shift,meeting',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'staff_tags' => 'nullable|array',
            'staff_tags.*' => 'exists:users,id',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'all_day' => 'boolean'
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'event' => $event->fresh()->full_calendar_event,
            'message' => 'Event updated successfully!'
        ]);
    }

    /**
     * Delete an event
     */
    public function deleteEvent(CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can delete events
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if event belongs to user's company
        if ($event->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully!'
        ]);
    }

    /**
     * Store a time-off request (for employees)
     */
    public function storeTimeOffRequest(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $validated['user_id'] = $user->id;
        $validated['company_id'] = session('company_id');

        $timeOffRequest = TimeOffRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Time-off request submitted successfully! Awaiting approval.',
            'request' => $timeOffRequest->fresh()->full_calendar_event
        ]);
    }

    /**
     * Get pending time-off requests for admin review
     */
    public function getPendingTimeOffRequests()
    {
        $user = Auth::user();
        
        // Only admin/super_admin can view pending requests
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requests = TimeOffRequest::forCompany(session('company_id'))
            ->pending()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    /**
     * Approve/Deny time-off request
     */
    public function reviewTimeOffRequest(Request $request, TimeOffRequest $timeOffRequest)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can review requests
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request belongs to user's company
        if ($timeOffRequest->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,deny',
            'admin_notes' => 'nullable|string'
        ]);

        if ($validated['action'] === 'approve') {
            $timeOffRequest->approve($user->id, $validated['admin_notes']);
            $message = 'Time-off request approved successfully!';
        } else {
            $timeOffRequest->deny($user->id, $validated['admin_notes']);
            $message = 'Time-off request denied.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'request' => $timeOffRequest->fresh()->full_calendar_event
        ]);
    }

    /**
     * Get user's time-off requests
     */
    public function getMyTimeOffRequests()
    {
        $user = Auth::user();
        
        $requests = TimeOffRequest::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    public function deleteTimeOffRequest(TimeOffRequest $timeOffRequest)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can delete time-off requests
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request belongs to user's company
        if ($timeOffRequest->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $timeOffRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time-off request deleted successfully!'
        ]);
    }

    /**
     * Get event details
     */
    public function getEvent(CalendarEvent $event)
    {
        // Check if event belongs to user's company
        if ($event->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'start_date' => $event->start_date->format('Y-m-d'),
            'end_date' => $event->end_date->format('Y-m-d'),
            'start_time' => $event->start_time ? $event->start_time->format('H:i') : null,
            'end_time' => $event->end_time ? $event->end_time->format('H:i') : null,
            'type' => $event->type,
            'location' => $event->location,
            'notes' => $event->notes,
            'staff_tags' => $event->staff_tags,
            'attendees' => $event->attendees,
            'color' => $event->color,
            'all_day' => $event->all_day,
            'is_recurring' => $event->is_recurring,
            'recurrence_pattern' => $event->recurrence_pattern,
            'recurrence_interval' => $event->recurrence_interval,
            'recurrence_days' => $event->recurrence_days,
            'recurrence_end_date' => $event->recurrence_end_date ? $event->recurrence_end_date->format('Y-m-d') : null,
            'recurrence_count' => $event->recurrence_count,
            'created_by' => $event->creator->name ?? 'Unknown'
        ]);
    }

    /**
     * Export calendar events to ICS format
     */
    public function exportCalendar(Request $request)
    {
        $user = Auth::user();
        $companyId = session('company_id');
        
        // Get date range for export
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->addMonths(3)->endOfMonth());
        
        $events = CalendarEvent::forCompany($companyId)
            ->inDateRange($startDate, $endDate)
            ->get();

        $icsContent = $this->generateICSContent($events);
        
        $filename = 'calendar_' . now()->format('Y-m-d') . '.ics';
        
        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import US holidays for the year
     */
    public function importHolidays(Request $request)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can import holidays
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $year = $request->input('year', now()->year);
        $companyId = session('company_id');
        
        $holidays = $this->getUSHolidays($year);
        $importedCount = 0;
        
        foreach ($holidays as $holiday) {
            // Check if holiday already exists
            $exists = CalendarEvent::forCompany($companyId)
                ->where('title', $holiday['title'])
                ->where('start_date', $holiday['date'])
                ->where('type', 'holiday')
                ->exists();
                
            if (!$exists) {
                CalendarEvent::create([
                    'title' => $holiday['title'],
                    'description' => $holiday['description'],
                    'start_date' => $holiday['date'],
                    'end_date' => $holiday['date'],
                    'type' => 'holiday',
                    'color' => \App\Models\CalendarEvent::getDefaultColorForType('holiday'), // Use correct yellow color
                    'all_day' => true,
                    'created_by' => $user->id,
                    'company_id' => $companyId
                ]);
                $importedCount++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Imported {$importedCount} holidays for {$year}",
            'imported_count' => $importedCount
        ]);
    }

    /**
     * Mark attendees for an event
     */
    public function markAttendees(Request $request, CalendarEvent $event)
    {
        $user = Auth::user();
        
        // Only admin/super_admin can mark attendees
        if (!in_array($user->type, ['admin', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if event belongs to user's company
        if ($event->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'attendees' => 'required|array',
            'attendees.*' => 'exists:users,id'
        ]);

        $event->update(['attendees' => $validated['attendees']]);

        return response()->json([
            'success' => true,
            'message' => 'Attendees marked successfully!',
            'event' => $event->fresh()->load(['eventAttendees'])
        ]);
    }

    /**
     * Generate ICS content for calendar export
     */
    private function generateICSContent($events)
    {
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//OnHand Solutions//Calendar//EN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        
        foreach ($events as $event) {
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:" . $event->id . "@onhandsolutions.com\r\n";
            $ics .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            
            if ($event->all_day) {
                $ics .= "DTSTART;VALUE=DATE:" . $event->start_date->format('Ymd') . "\r\n";
                $ics .= "DTEND;VALUE=DATE:" . $event->end_date->addDay()->format('Ymd') . "\r\n";
            } else {
                $startDateTime = $event->start_date->format('Ymd\T') . 
                    ($event->start_time ? $event->start_time->format('His') : '000000') . "\r\n";
                $endDateTime = $event->end_date->format('Ymd\T') . 
                    ($event->end_time ? $event->end_time->format('His') : '235959') . "\r\n";
                
                $ics .= "DTSTART:" . $startDateTime;
                $ics .= "DTEND:" . $endDateTime;
            }
            
            $ics .= "SUMMARY:" . $this->escapeICSString($event->title) . "\r\n";
            
            if ($event->description) {
                $ics .= "DESCRIPTION:" . $this->escapeICSString($event->description) . "\r\n";
            }
            
            if ($event->location) {
                $ics .= "LOCATION:" . $this->escapeICSString($event->location) . "\r\n";
            }
            
            $ics .= "END:VEVENT\r\n";
        }
        
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }

    /**
     * Escape special characters for ICS format
     */
    private function escapeICSString($string)
    {
        return str_replace([',', ';', '\\', "\n", "\r"], ['\,', '\;', '\\\\', '\n', '\r'], $string);
    }

    /**
     * Get US holidays for a given year
     */
    private function getUSHolidays($year)
    {
        return [
            [
                'title' => 'New Year\'s Day',
                'date' => "{$year}-01-01",
                'description' => 'Federal Holiday - New Year\'s Day'
            ],
            [
                'title' => 'Martin Luther King Jr. Day',
                'date' => $this->getNthWeekdayOfMonth($year, 1, 1, 3), // 3rd Monday of January
                'description' => 'Federal Holiday - Martin Luther King Jr. Day'
            ],
            [
                'title' => 'Presidents\' Day',
                'date' => $this->getNthWeekdayOfMonth($year, 2, 1, 3), // 3rd Monday of February
                'description' => 'Federal Holiday - Presidents\' Day'
            ],
            [
                'title' => 'Memorial Day',
                'date' => $this->getLastWeekdayOfMonth($year, 5, 1), // Last Monday of May
                'description' => 'Federal Holiday - Memorial Day'
            ],
            [
                'title' => 'Independence Day',
                'date' => "{$year}-07-04",
                'description' => 'Federal Holiday - Independence Day'
            ],
            [
                'title' => 'Labor Day',
                'date' => $this->getNthWeekdayOfMonth($year, 9, 1, 1), // 1st Monday of September
                'description' => 'Federal Holiday - Labor Day'
            ],
            [
                'title' => 'Columbus Day',
                'date' => $this->getNthWeekdayOfMonth($year, 10, 1, 2), // 2nd Monday of October
                'description' => 'Federal Holiday - Columbus Day'
            ],
            [
                'title' => 'Veterans Day',
                'date' => "{$year}-11-11",
                'description' => 'Federal Holiday - Veterans Day'
            ],
            [
                'title' => 'Thanksgiving',
                'date' => $this->getNthWeekdayOfMonth($year, 11, 4, 4), // 4th Thursday of November
                'description' => 'Federal Holiday - Thanksgiving Day'
            ],
            [
                'title' => 'Christmas Day',
                'date' => "{$year}-12-25",
                'description' => 'Federal Holiday - Christmas Day'
            ]
        ];
    }

    /**
     * Get nth weekday of month
     */
    private function getNthWeekdayOfMonth($year, $month, $weekday, $n)
    {
        $date = Carbon::create($year, $month, 1);
        $date->nthOfMonth($n, $weekday);
        return $date->format('Y-m-d');
    }

    /**
     * Get last weekday of month
     */
    private function getLastWeekdayOfMonth($year, $month, $weekday)
    {
        $date = Carbon::create($year, $month, 1)->endOfMonth();
        while ($date->dayOfWeek !== $weekday) {
            $date->subDay();
        }
        return $date->format('Y-m-d');
    }

    /**
     * Generate recurring event instances within a specific date range
     */
    private function generateRecurringInstancesInRange($event, $start, $end)
    {
        if (!$event->is_recurring) {
            return collect();
        }

        $instances = collect();
        $currentDate = Carbon::parse($event->start_date);
        $rangeStart = Carbon::parse($start);
        $rangeEnd = Carbon::parse($end);
        $maxIterations = 1000; // Safety limit to prevent infinite loops
        $iterations = 0;

        // If the event's original date is within range, include it
        if ($currentDate >= $rangeStart && $currentDate <= $rangeEnd) {
            $instances->push($this->createEventInstance($event, $currentDate));
        }

        // Generate future instances
        while ($iterations < $maxIterations) {
            $currentDate = $this->getNextRecurrenceDate($event, $currentDate);
            $iterations++;

            // Stop if we've gone beyond the range end
            if ($currentDate > $rangeEnd) {
                break;
            }

            // Stop if we've reached the recurrence end date
            if ($event->recurrence_end_date && $currentDate > Carbon::parse($event->recurrence_end_date)) {
                break;
            }

            // Stop if we've reached the recurrence count limit
            if ($event->recurrence_count && $iterations >= $event->recurrence_count) {
                break;
            }

            // If this instance is within our range, add it
            if ($currentDate >= $rangeStart && $currentDate <= $rangeEnd) {
                $instances->push($this->createEventInstance($event, $currentDate));
            }
        }

        return $instances;
    }

    /**
     * Create a single event instance for a specific date
     */
    private function createEventInstance($event, $date)
    {
        $daysDiff = Carbon::parse($event->start_date)->diffInDays(Carbon::parse($event->end_date));
        $endDate = $date->copy()->addDays($daysDiff);

        $instance = [
            'id' => 'recurring-' . $event->id . '-' . $date->format('Y-m-d'),
            'title' => $event->title,
            'start' => $date->format('Y-m-d'),
            'end' => $endDate->addDay()->format('Y-m-d'), // FullCalendar needs end+1 for all-day events
            'backgroundColor' => $event->color,
            'borderColor' => $event->color,
            'allDay' => $event->all_day,
            'extendedProps' => [
                'description' => $event->description,
                'type' => $event->type,
                'location' => $event->location,
                'notes' => $event->notes,
                'staff_tags' => $event->staff_tags,
                'created_by' => $event->creator->name ?? 'Unknown',
                'is_recurring_instance' => true,
                'parent_event_id' => $event->id
            ]
        ];

        if (!$event->all_day && $event->start_time && $event->end_time) {
            $instance['start'] = $date->format('Y-m-d') . 'T' . Carbon::parse($event->start_time)->format('H:i:s');
            $instance['end'] = $endDate->format('Y-m-d') . 'T' . Carbon::parse($event->end_time)->format('H:i:s');
            $instance['allDay'] = false;
        }

        return $instance;
    }

    /**
     * Get the next recurrence date for an event
     */
    private function getNextRecurrenceDate($event, $currentDate)
    {
        return match($event->recurrence_pattern) {
            'daily' => $currentDate->addDays($event->recurrence_interval ?? 1),
            'weekly' => $currentDate->addWeeks($event->recurrence_interval ?? 1),
            'monthly' => $currentDate->addMonths($event->recurrence_interval ?? 1),
            'yearly' => $currentDate->addYears($event->recurrence_interval ?? 1),
            default => $currentDate->addDays(1)
        };
    }
} 