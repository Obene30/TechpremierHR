@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-sm text-gray-500">{{ now()->format('l, d M Y') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Employees</div>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
            <div class="text-xs text-green-600 mt-2"><i class="fas fa-arrow-up text-xs"></i> 3% from last month</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $onLeaveToday }}</div>
                    <div class="text-xs text-gray-500 mt-1">On Leave Today</div>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-minus text-amber-600"></i>
                </div>
            </div>
            <div class="text-xs text-red-500 mt-2"><i class="fas fa-arrow-up text-xs"></i> 8% from last month</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $timesheetSubmitted }}</div>
                    <div class="text-xs text-gray-500 mt-1">Timesheet Submitted</div>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
            </div>
            <div class="text-xs text-green-600 mt-2"><i class="fas fa-arrow-up text-xs"></i> 92% from last month</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $pendingLeaves }}</div>
                    <div class="text-xs text-gray-500 mt-1">Pending Requests</div>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-red-500"></i>
                </div>
            </div>
            <div class="text-xs text-gray-500 mt-2">Awaiting approval</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $newHiresThisMonth }}</div>
                    <div class="text-xs text-gray-500 mt-1">New Hires (Month)</div>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-green-600"></i>
                </div>
            </div>
            <div class="text-xs text-green-600 mt-2">This month</div>
        </div>
    </div>

    {{-- Row 2: Employees + Leave Requests --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Employees --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Employees</h3>
                <a href="{{ route('admin.employees.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="relative mb-4">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search employees..." class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-lg focus:outline-none">
            </div>
            <div class="space-y-3">
                @foreach($recentEmployees as $emp)
                <div class="flex items-center gap-3">
                    <img src="{{ $emp->avatarUrl() }}" class="w-9 h-9 rounded-full object-cover">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $emp->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $emp->designation ?? 'Employee' }}</div>
                    </div>
                    <span class="badge badge-{{ $emp->status }}">{{ ucfirst(str_replace('_',' ',$emp->status)) }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('admin.employees.index') }}" class="mt-4 block w-full text-center py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">
                View All Employees
            </a>
        </div>

        {{-- Leave Requests --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Leave Requests</h3>
                <a href="{{ route('admin.leave.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingLeaveRequests as $leave)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <img src="{{ $leave->user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <div class="text-sm font-medium text-gray-800">{{ $leave->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $leave->leaveType->name }} · {{ $leave->start_date->format('d M') }} – {{ $leave->end_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span>
                        @if($leave->status === 'pending')
                        <form method="POST" action="{{ route('admin.leave.approve', $leave) }}" class="inline">@csrf
                            <button type="submit" class="w-7 h-7 bg-green-100 text-green-600 rounded-full hover:bg-green-200 text-xs"><i class="fas fa-check"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.leave.reject', $leave) }}" class="inline">@csrf
                            <button type="submit" class="w-7 h-7 bg-red-100 text-red-500 rounded-full hover:bg-red-200 text-xs"><i class="fas fa-times"></i></button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-400 py-6 text-sm">No pending leave requests</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Row 3: Timesheet Requests --}}
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Timesheet Requests</h3>
            <a href="{{ route('admin.timesheet.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="table-responsive">
            <table class="w-full text-sm" style="min-width:550px">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-gray-100">
                        <th class="text-left pb-3 font-medium">Employee</th>
                        <th class="text-left pb-3 font-medium">Date</th>
                        <th class="text-left pb-3 font-medium">Hours</th>
                        <th class="text-left pb-3 font-medium">Status</th>
                        <th class="text-left pb-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingTimesheets as $ts)
                    <tr class="table-row border-b border-gray-50 last:border-0">
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <img src="{{ $ts->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover">
                                <span class="font-medium text-gray-800">{{ $ts->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-gray-600">{{ $ts->date->format('d M Y') }}</td>
                        <td class="py-3 font-medium text-gray-800">{{ number_format($ts->total_hours, 1) }}h</td>
                        <td class="py-3"><span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span></td>
                        <td class="py-3">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.timesheet.approve', $ts) }}">@csrf
                                    <button type="submit" class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-lg hover:bg-green-100">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.timesheet.reject', $ts) }}">@csrf
                                    <button type="submit" class="text-xs bg-red-50 text-red-500 px-2 py-1 rounded-lg hover:bg-red-100">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-400 text-sm">No pending timesheets</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
