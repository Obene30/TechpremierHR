<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'user_id', 'date', 'project_task', 'time_in', 'time_out',
        'break_minutes', 'total_hours', 'status', 'week_start', 'week_end',
        'submitted_at', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'week_start' => 'date',
            'week_end' => 'date',
            'submitted_at' => 'datetime',
            'total_hours' => 'float',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'submitted' => 'bg-blue-100 text-blue-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    }
}
