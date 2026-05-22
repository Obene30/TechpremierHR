<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $timesheets = $user->timesheets()
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->orderBy('date')->get();
        $totalHoursThisWeek = $timesheets->sum('total_hours');
        $submittedHours = $timesheets->where('status','submitted')->sum('total_hours');
        $pendingHours = $timesheets->where('status','pending')->sum('total_hours');
        $recentRequests = $user->timesheets()->where('status','!=','pending')->latest()->take(3)->get();
        return view('employee.timesheet.index', compact(
            'timesheets','totalHoursThisWeek','submittedHours','pendingHours','weekStart','weekEnd','recentRequests'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'project_task' => 'nullable|string|max:255',
            'time_in' => 'required',
            'time_out' => 'required',
            'break_minutes' => 'nullable|integer|min:0',
        ]);
        $timeIn = Carbon::parse($data['time_in']);
        $timeOut = Carbon::parse($data['time_out']);
        $break = $data['break_minutes'] ?? 0;
        $totalMinutes = max(0, $timeIn->diffInMinutes($timeOut) - $break);
        $totalHours = round($totalMinutes / 60, 2);
        $date = Carbon::parse($data['date']);
        Timesheet::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $data['date']],
            [
                'project_task' => $data['project_task'],
                'time_in' => $data['time_in'],
                'time_out' => $data['time_out'],
                'break_minutes' => $break,
                'total_hours' => $totalHours,
                'week_start' => $date->startOfWeek()->toDateString(),
                'week_end' => $date->copy()->endOfWeek()->toDateString(),
                'status' => 'pending',
            ]
        );
        return back()->with('success', 'Timesheet entry saved.');
    }

    public function submit(Request $request)
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        Auth::user()->timesheets()
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->where('status', 'pending')
            ->update(['status' => 'submitted', 'submitted_at' => now()]);
        return back()->with('success', 'Timesheet submitted for approval.');
    }
}
