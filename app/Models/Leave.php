<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'start_date', 'end_date', 'days',
        'reason', 'status', 'approved_by', 'comments',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'days' => 'float'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function leaveType() { return $this->belongsTo(LeaveType::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    }
}
