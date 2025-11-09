<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'type',
        'location',
        'notes',
        'staff_tags',
        'attendees',
        'color',
        'all_day',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_interval',
        'recurrence_days',
        'recurrence_end_date',
        'recurrence_count',
        'parent_event_id',
        'created_by',
        'company_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'staff_tags' => 'array',
        'attendees' => 'array',
        'recurrence_days' => 'array',
        'all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_end_date' => 'date'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function taggedStaff()
    {
        if (!$this->staff_tags) {
            return collect();
        }
        return User::whereIn('id', $this->staff_tags)->get();
    }

    public function eventAttendees()
    {
        if (!$this->attendees) {
            return collect();
        }
        return User::whereIn('id', $this->attendees)->get();
    }

    public function parentEvent()
    {
        return $this->belongsTo(CalendarEvent::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'parent_event_id');
    }

    // Get default color for event type
    public static function getDefaultColorForType($type)
    {
        return match($type) {
            'shift' => '#2D2D2D',        // Dark Gray (landing page)
            'holiday' => '#FFCD29',      // Light Yellow (landing page)
            'market' => '#570AA0',       // Purple (landing page)
            'meeting' => '#96BF48',      // Green (Shopify highlight)
            'event' => '#6dabe4',        // Blue (frontend)
            default => '#2D2D2D'         // Default dark gray
        };
    }

    // Generate recurring event instances
    public function generateRecurringInstances()
    {
        if (!$this->is_recurring) {
            return collect();
        }

        $instances = collect();
        $currentDate = $this->start_date;
        $endDate = $this->recurrence_end_date ?? $this->start_date->addYear();
        $count = 0;
        $maxCount = $this->recurrence_count ?? 100;

        while ($currentDate <= $endDate && $count < $maxCount) {
            if ($currentDate != $this->start_date) {
                $daysDiff = $this->start_date->diffInDays($this->end_date);
                
                $instances->push([
                    'title' => $this->title,
                    'description' => $this->description,
                    'start_date' => $currentDate,
                    'end_date' => $currentDate->copy()->addDays($daysDiff),
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'type' => $this->type,
                    'location' => $this->location,
                    'notes' => $this->notes,
                    'staff_tags' => $this->staff_tags,
                    'color' => $this->color,
                    'all_day' => $this->all_day,
                    'is_recurring' => false,
                    'parent_event_id' => $this->id,
                    'created_by' => $this->created_by,
                    'company_id' => $this->company_id
                ]);
            }

            $currentDate = $this->getNextRecurrenceDate($currentDate);
            $count++;
        }

        return $instances;
    }

    private function getNextRecurrenceDate($currentDate)
    {
        return match($this->recurrence_pattern) {
            'daily' => $currentDate->addDays($this->recurrence_interval ?? 1),
            'weekly' => $currentDate->addWeeks($this->recurrence_interval ?? 1),
            'monthly' => $currentDate->addMonths($this->recurrence_interval ?? 1),
            'yearly' => $currentDate->addYears($this->recurrence_interval ?? 1),
            default => $currentDate->addDays(1)
        };
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhereBetween('end_date', [$start, $end])
              ->orWhere(function($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start)
                     ->where('end_date', '>=', $end);
              });
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('Y-m-d');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('Y-m-d');
    }

    public function getFullCalendarEventAttribute()
    {
        $event = [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start_date->format('Y-m-d'),
            'end' => $this->end_date->addDay()->format('Y-m-d'), // FullCalendar needs end+1 for all-day events
            'backgroundColor' => $this->color,
            'borderColor' => $this->color,
            'allDay' => $this->all_day,
            'extendedProps' => [
                'description' => $this->description,
                'type' => $this->type,
                'location' => $this->location,
                'notes' => $this->notes,
                'staff_tags' => $this->staff_tags,
                'created_by' => $this->creator->name ?? 'Unknown'
            ]
        ];

        if (!$this->all_day && $this->start_time && $this->end_time) {
            $event['start'] = $this->start_date->format('Y-m-d') . 'T' . $this->start_time->format('H:i:s');
            $event['end'] = $this->end_date->format('Y-m-d') . 'T' . $this->end_time->format('H:i:s');
            $event['allDay'] = false;
        }

        return $event;
    }
} 