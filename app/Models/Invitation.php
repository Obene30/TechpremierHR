<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['email', 'name', 'token', 'invited_by', 'department_id', 'role', 'expires_at', 'used_at'];
    protected function casts(): array {
        return ['expires_at' => 'datetime', 'used_at' => 'datetime'];
    }
    public function inviter() { return $this->belongsTo(User::class, 'invited_by'); }
    public function department() { return $this->belongsTo(Department::class); }
    public function isExpired(): bool {
        return $this->expires_at && $this->expires_at->isPast();
    }
    public function isUsed(): bool { return !is_null($this->used_at); }
}
