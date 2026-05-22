<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Timesheet;
use App\Models\User;

class ReportController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role','employee')->where('status','active')->count();
        $totalLeaveDays = Leave::where('status','approved')->sum('days');
        $totalHours = Timesheet::where('status','approved')->sum('total_hours');
        $departments = Department::withCount('users')->get();
        $leaveByType = LeaveType::withCount('leaves')->get();
        $employeesByDept = Department::withCount(['users' => fn($q) => $q->where('role','employee')])->get();
        return view('admin.reports.index', compact(
            'totalEmployees','activeEmployees','totalLeaveDays','totalHours',
            'departments','leaveByType','employeesByDept'
        ));
    }
}
