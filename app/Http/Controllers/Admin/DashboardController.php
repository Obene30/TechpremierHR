<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $onLeaveToday = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->distinct('user_id')->count('user_id');
        $timesheetSubmitted = Timesheet::where('status', 'submitted')
            ->whereMonth('date', now()->month)->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $newHiresThisMonth = User::whereMonth('date_joined', now()->month)
            ->whereYear('date_joined', now()->year)->count();

        $recentEmployees = User::where('role', 'employee')
            ->with('department')->latest()->take(5)->get();

        $pendingLeaveRequests = Leave::with(['user', 'leaveType'])
            ->where('status', 'pending')->latest()->take(5)->get();

        $pendingTimesheets = Timesheet::with('user')
            ->where('status', 'submitted')->latest()->take(4)->get();

        return view('admin.dashboard', compact(
            'totalEmployees', 'onLeaveToday', 'timesheetSubmitted',
            'pendingLeaves', 'newHiresThisMonth', 'recentEmployees',
            'pendingLeaveRequests', 'pendingTimesheets'
        ));
    }
}
