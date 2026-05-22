@extends('layouts.app')
@section('title', 'My Dashboard')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-sm text-gray-500">Here's what's happening in your workspace today.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $pendingLeaves }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pending Leaves</div>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-amber-500"></i>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $approvedLeaves }}</div>
                    <div class="text-xs text-gray-500 mt-1">Approved Leaves</div>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($totalHoursThisMonth, 1) }}h</div>
                    <div class="text-xs text-gray-500 mt-1">Hours This Month</div>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->documents->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">My Documents</div>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Leave Balance --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Leave Balance</h3>
                <a href="{{ route('employee.leave.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @foreach($leaveTypes as $lt)
                @php $used = auth()->user()->approvedLeaveDays($lt->id); $remaining = max(0, $lt->days_entitlement - $used); $pct = $lt->days_entitlement > 0 ? round($used / $lt->days_entitlement * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">{{ $lt->name }}</span>
                        <span class="text-gray-500">{{ $remaining }} / {{ $lt->days_entitlement }} days left</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full">
                        <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%;background:{{ $lt->color }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <a href="{{ route('employee.leave.index') }}" class="mt-4 block w-full text-center py-2 text-sm bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                Request Leave
            </a>
        </div>

        {{-- Recent Leave Requests --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Recent Leave Requests</h3>
                <a href="{{ route('employee.leave.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentLeaves as $leave)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $leave->leaveType->name }}</div>
                        <div class="text-xs text-gray-500">{{ $leave->start_date->format('d M') }} – {{ $leave->end_date->format('d M Y') }} · {{ $leave->days }} days</div>
                    </div>
                    <span class="badge badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                </div>
                @empty
                <div class="text-center text-gray-400 text-sm py-4">No leave requests yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Timesheets --}}
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Recent Timesheets</h3>
            <a href="{{ route('employee.timesheet.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="table-responsive">
            <table class="w-full text-sm" style="min-width:500px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500">
                        <th class="text-left px-4 py-2">Date</th>
                        <th class="text-left px-4 py-2">Task</th>
                        <th class="text-left px-4 py-2">Time In</th>
                        <th class="text-left px-4 py-2">Time Out</th>
                        <th class="text-left px-4 py-2">Hours</th>
                        <th class="text-left px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentTimesheets as $ts)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-gray-600">{{ $ts->date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $ts->project_task ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ts->time_in ? \Carbon\Carbon::parse($ts->time_in)->format('H:i') : '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $ts->time_out ? \Carbon\Carbon::parse($ts->time_out)->format('H:i') : '—' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ number_format($ts->total_hours, 1) }}h</td>
                        <td class="px-4 py-3"><span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No timesheet entries yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
