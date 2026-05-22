<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'department_id', 'designation',
        'phone', 'avatar', 'status', 'date_of_birth', 'date_joined',
        'invitation_token', 'account_setup_at', 'invited_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'account_setup_at' => 'datetime',
            'date_of_birth' => 'date',
            'date_joined' => 'date',
            'password' => 'hashed',
        ];
    }

    public function department() { return $this->belongsTo(Department::class); }
    public function leaves() { return $this->hasMany(Leave::class); }
    public function timesheets() { return $this->hasMany(Timesheet::class); }
    public function documents() { return $this->hasMany(Document::class); }
    public function invitations() { return $this->hasMany(Invitation::class, 'invited_by'); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function avatarUrl(): string {
        if ($this->avatar) return asset('storage/' . $this->avatar);
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=128";
    }
    public function pendingLeaveDays(): float {
        return $this->leaves()->where('status', 'pending')->sum('days');
    }
    public function approvedLeaveDays(?int $leaveTypeId = null): float {
        $q = $this->leaves()->where('status', 'approved');
        if ($leaveTypeId) $q->where('leave_type_id', $leaveTypeId);
        return $q->sum('days');
    }
}
