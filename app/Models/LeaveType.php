<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['name', 'days_entitlement', 'color'];
    public function leaves() { return $this->hasMany(Leave::class); }
}
