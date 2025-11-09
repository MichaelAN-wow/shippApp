<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeOffRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'notes',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'company_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function approve($adminId, $adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $adminNotes
        ]);
    }

    public function deny($adminId, $adminNotes = null)
    {
        $this->update([
            'status' => 'denied',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $adminNotes
        ]);
    }

    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => '#ffc107',
            'approved' => '#28a745',
            'denied' => '#dc3545',
            default => '#6c757d'
        };
    }

    public function getFullCalendarEventAttribute()
    {
        return [
            'id' => 'timeoff-' . $this->id,
            'title' => $this->user->name . ' - Time Off (' . ucfirst($this->status) . ')',
            'start' => $this->start_date->format('Y-m-d'),
            'end' => $this->end_date->addDay()->format('Y-m-d'),
            'backgroundColor' => $this->status_color,
            'borderColor' => $this->status_color,
            'allDay' => true,
            'extendedProps' => [
                'type' => 'time_off',
                'status' => $this->status,
                'reason' => $this->reason,
                'notes' => $this->notes,
                'admin_notes' => $this->admin_notes,
                'user_name' => $this->user->name,
                'duration' => $this->duration_in_days
            ]
        ];
    }
} 