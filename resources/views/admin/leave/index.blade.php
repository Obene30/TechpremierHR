@extends('layouts.app')
@section('title', 'Leave Management')
@section('content')
<div class="space-y-6" x-data="{ rejectId: null, rejectOpen: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Leave Management</h1>
            <p class="text-sm text-gray-500">Review and manage employee leave requests</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="stat-card">
            <div class="text-2xl font-bold text-amber-500">{{ $pending }}</div>
            <div class="text-xs text-gray-500 mt-1">Pending</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-green-600">{{ $approved }}</div>
            <div class="text-xs text-gray-500 mt-1">Approved</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-red-500">{{ $rejected }}</div>
            <div class="text-xs text-gray-500 mt-1">Rejected</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="status" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:outline-none w-full sm:w-auto">
                    <option value="all" {{ request('status','all')=='all' ? 'selected' : '' }}>All Requests</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="leave_type" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:outline-none w-full sm:w-auto">
                    <option value="">All Leave Types</option>
                    @foreach($leaveTypes as $lt)
                    <option value="{{ $lt->id }}" {{ request('leave_type')==$lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="w-full" style="min-width:650px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500 font-medium">
                        <th class="text-left px-6 py-3">Employee</th>
                        <th class="text-left px-6 py-3">Leave Type</th>
                        <th class="text-left px-6 py-3">Duration</th>
                        <th class="text-left px-6 py-3">Days</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $leave)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $leave->user->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ $leave->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $leave->user->designation ?? 'Employee' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full" style="background:{{ $leave->leaveType->color }}22;color:{{ $leave->leaveType->color }}">
                                {{ $leave->leaveType->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $leave->start_date->format('d M Y') }} – {{ $leave->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $leave->days }}</td>
                        <td class="px-6 py-4"><span class="badge badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span></td>
                        <td class="px-6 py-4">
                            @if($leave->status === 'pending')
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.leave.approve', $leave) }}">@csrf
                                    <button type="submit" class="text-xs bg-green-50 text-green-600 px-3 py-1.5 rounded-lg hover:bg-green-100 font-medium">Approve</button>
                                </form>
                                <button @click="rejectId={{ $leave->id }}; rejectOpen=true" class="text-xs bg-red-50 text-red-500 px-3 py-1.5 rounded-lg hover:bg-red-100 font-medium">Reject</button>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">No leave requests found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
    </div>
</div>

{{-- Reject Modal --}}
<div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Leave Request</h3>
        <form method="POST" :action="'/admin/leave/' + rejectId + '/reject'" class="space-y-4">
            @csrf
            <textarea name="comments" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="Reason for rejection (optional)"></textarea>
            <div class="flex gap-3">
                <button type="button" @click="rejectOpen=false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-500 text-white text-sm rounded-xl hover:bg-red-600">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection
