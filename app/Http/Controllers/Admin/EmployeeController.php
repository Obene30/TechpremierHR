<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')->with('department');
        if ($request->search) $query->where('name', 'like', "%{$request->search}%")
            ->orWhere('email', 'like', "%{$request->search}%");
        if ($request->department) $query->where('department_id', $request->department);
        if ($request->status) $query->where('status', $request->status);
        $employees = $query->latest()->paginate(15);
        $departments = Department::all();
        $totalActive = User::where('role','employee')->where('status','active')->count();
        $totalInactive = User::where('role','employee')->where('status','inactive')->count();
        return view('admin.employees.index', compact('employees', 'departments', 'totalActive', 'totalInactive'));
    }

    public function show(User $employee)
    {
        $employee->load(['department', 'documents', 'leaves.leaveType', 'timesheets']);
        return view('admin.employees.show', compact('employee'));
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email|unique:invitations,email',
            'name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:admin,employee',
        ]);

        $invitation = Invitation::create([
            'email' => $data['email'],
            'name' => $data['name'] ?? null,
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'department_id' => $data['department_id'] ?? null,
            'role' => $data['role'],
            'expires_at' => now()->addDays(7),
        ]);

        // In production, send email. For now, return the setup link.
        $setupLink = route('invitation.setup', $invitation->token);
        return back()->with('invite_link', $setupLink)->with('success', "Invitation created for {$data['email']}");
    }

    public function updateStatus(Request $request, User $employee)
    {
        $request->validate(['status' => 'required|in:active,inactive,on_leave']);
        $employee->update(['status' => $request->status]);
        return back()->with('success', 'Employee status updated.');
    }
}
