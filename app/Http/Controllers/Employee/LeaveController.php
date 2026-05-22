<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaves = $user->leaves()->with('leaveType')->latest()->paginate(10);
        $leaveTypes = LeaveType::all();
        $leaveBalances = $leaveTypes->map(function($type) use ($user) {
            $used = $user->approvedLeaveDays($type->id);
            return ['type' => $type, 'used' => $used, 'remaining' => max(0, $type->days_entitlement - $used)];
        });
        return view('employee.leave.index', compact('leaves','leaveTypes','leaveBalances'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);
        $days = $start->diffInWeekdays($end) + 1;
        Leave::create([...$data, 'user_id' => Auth::id(), 'days' => $days, 'status' => 'pending']);
        return back()->with('success', 'Leave request submitted successfully.');
    }

    public function cancel(Leave $leave)
    {
        if ($leave->user_id !== Auth::id()) abort(403);
        if ($leave->status !== 'pending') return back()->with('error', 'Only pending leaves can be cancelled.');
        $leave->delete();
        return back()->with('success', 'Leave request cancelled.');
    }
}
