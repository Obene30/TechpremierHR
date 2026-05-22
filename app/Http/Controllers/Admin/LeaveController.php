<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'leaveType'])->latest();
        if ($request->status && $request->status !== 'all') $query->where('status', $request->status);
        if ($request->leave_type) $query->where('leave_type_id', $request->leave_type);
        $leaves = $query->paginate(10);
        $leaveTypes = LeaveType::all();
        $pending = Leave::where('status','pending')->count();
        $approved = Leave::where('status','approved')->count();
        $rejected = Leave::where('status','rejected')->count();
        return view('admin.leave.index', compact('leaves','leaveTypes','pending','approved','rejected'));
    }

    public function approve(Leave $leave)
    {
        $leave->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        if ($leave->user) $leave->user->update(['status' => 'on_leave']);
        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $leave->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'comments' => $request->comments]);
        return back()->with('success', 'Leave rejected.');
    }
}
