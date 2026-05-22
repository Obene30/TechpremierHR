<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pendingLeaves = $user->leaves()->where('status','pending')->count();
        $approvedLeaves = $user->leaves()->where('status','approved')->count();
        $totalHoursThisMonth = $user->timesheets()
            ->whereMonth('date', now()->month)->sum('total_hours');
        $recentLeaves = $user->leaves()->with('leaveType')->latest()->take(3)->get();
        $leaveTypes = LeaveType::all();
        $recentTimesheets = $user->timesheets()->latest()->take(5)->get();
        return view('employee.dashboard', compact(
            'user','pendingLeaves','approvedLeaves','totalHoursThisMonth',
            'recentLeaves','leaveTypes','recentTimesheets'
        ));
    }
}
