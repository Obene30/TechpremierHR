<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $query = Timesheet::with('user')->latest();
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->user_id) $query->where('user_id', $request->user_id);
        $timesheets = $query->paginate(15);
        $pending = Timesheet::where('status','submitted')->count();
        $approved = Timesheet::where('status','approved')->count();
        return view('admin.timesheet.index', compact('timesheets','pending','approved'));
    }

    public function approve(Timesheet $timesheet)
    {
        $timesheet->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Timesheet approved.');
    }

    public function reject(Timesheet $timesheet)
    {
        $timesheet->update(['status' => 'rejected', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Timesheet rejected.');
    }
}
