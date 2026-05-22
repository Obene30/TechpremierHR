@extends('layouts.app')
@section('title', 'Timesheets')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Timesheet Management</h1>
        <p class="text-sm text-gray-500">Review and approve employee timesheets</p>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="stat-card">
            <div class="text-2xl font-bold text-blue-600">{{ $pending }}</div>
            <div class="text-xs text-gray-500 mt-1">Pending Approval</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-green-600">{{ $approved }}</div>
            <div class="text-xs text-gray-500 mt-1">Approved</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <form method="GET" class="flex gap-3">
                <select name="status" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:outline-none">
                    <option value="all">All Status</option>
                    <option value="submitted" {{ request('status')=='submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="w-full" style="min-width:750px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500 font-medium">
                        <th class="text-left px-6 py-3">Employee</th>
                        <th class="text-left px-6 py-3">Date</th>
                        <th class="text-left px-6 py-3">Task</th>
                        <th class="text-left px-6 py-3">Time In</th>
                        <th class="text-left px-6 py-3">Time Out</th>
                        <th class="text-left px-6 py-3">Hours</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($timesheets as $ts)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $ts->user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                                <span class="text-sm font-medium text-gray-800">{{ $ts->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $ts->project_task ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->time_in ? \Carbon\Carbon::parse($ts->time_in)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ts->time_out ? \Carbon\Carbon::parse($ts->time_out)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($ts->total_hours,1) }}h</td>
                        <td class="px-6 py-4"><span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span></td>
                        <td class="px-6 py-4">
                            @if($ts->status === 'submitted')
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.timesheet.approve', $ts) }}">@csrf
                                    <button type="submit" class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-lg hover:bg-green-100">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.timesheet.reject', $ts) }}">@csrf
                                    <button type="submit" class="text-xs bg-red-50 text-red-500 px-2 py-1 rounded-lg hover:bg-red-100">Reject</button>
                                </form>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-400 text-sm">No timesheets found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $timesheets->links() }}</div>
    </div>
</div>
@endsection
