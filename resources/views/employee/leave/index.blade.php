@extends('layouts.app')
@section('title', 'My Leave')
@section('content')
<div class="space-y-6" x-data="{ requestOpen: false }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Leave Management</h1>
            <p class="text-sm text-gray-500">Track and manage your leave requests</p>
        </div>
        <button @click="requestOpen = true" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium">
            <i class="fas fa-plus"></i> New Leave Request
        </button>
    </div>

    {{-- Leave Balance Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($leaveBalances as $balance)
        <div class="stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($balance['remaining'], 1) }}</div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $balance['type']->color }}22">
                    <i class="fas fa-calendar text-sm" style="color:{{ $balance['type']->color }}"></i>
                </div>
            </div>
            <div class="text-sm font-medium text-gray-700">{{ $balance['type']->name }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $balance['type']->days_entitlement }} Days Entitlement</div>
            <div class="mt-2 h-1.5 bg-gray-100 rounded-full">
                @php $pct = $balance['type']->days_entitlement > 0 ? min(100, round($balance['used'] / $balance['type']->days_entitlement * 100)) : 0; @endphp
                <div class="h-1.5 rounded-full" style="width:{{ $pct }}%;background:{{ $balance['type']->color }}"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Leave Requests Table --}}
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">My Leave Requests</h3>
        </div>
        <div class="table-responsive">
            <table class="w-full" style="min-width:600px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500 font-medium">
                        <th class="text-left px-6 py-3">Leave Type</th>
                        <th class="text-left px-6 py-3">Start Date</th>
                        <th class="text-left px-6 py-3">End Date</th>
                        <th class="text-left px-6 py-3">Days</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Reason</th>
                        <th class="text-left px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $leave)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full" style="background:{{ $leave->leaveType->color }}22;color:{{ $leave->leaveType->color }}">
                                {{ $leave->leaveType->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->start_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $leave->end_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $leave->days }}</td>
                        <td class="px-6 py-4"><span class="badge badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $leave->reason ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($leave->status === 'pending')
                            <form method="POST" action="{{ route('employee.leave.cancel', $leave) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Cancel this leave request?')" class="text-xs text-red-500 hover:text-red-700">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">No leave requests yet. Click "New Leave Request" to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
    </div>
</div>

{{-- New Leave Request Modal --}}
<div x-show="requestOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">New Leave Request</h3>
            <button @click="requestOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        @if($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('employee.leave.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
                <select name="leave_type_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Select leave type</option>
                    @foreach($leaveTypes as $lt)
                    <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->days_entitlement }} days entitlement)</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" name="start_date" required min="{{ today()->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                    <input type="date" name="end_date" required min="{{ today()->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                <textarea name="reason" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none" placeholder="Brief reason for your leave..."></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="requestOpen = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
