@extends('layouts.app')
@section('title', 'My Timesheet')
@section('content')
<div class="space-y-6" x-data="{ logOpen: false }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Timesheet</h1>
            <p class="text-sm text-gray-500">{{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="logOpen = true" class="flex items-center gap-2 border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-plus"></i> Log Time
            </button>
            <form method="POST" action="{{ route('employee.timesheet.submit') }}">
                @csrf
                <button type="submit" onclick="return confirm('Submit this week\'s timesheet for approval?')"
                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium">
                    <i class="fas fa-paper-plane"></i> Submit for Approval
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <div class="text-xl font-bold text-gray-900">{{ gmdate('H\h i\m', $totalHoursThisWeek * 3600) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Hours This Week</div>
        </div>
        <div class="stat-card">
            <div class="text-xl font-bold text-blue-600">{{ gmdate('H\h i\m', $submittedHours * 3600) }}</div>
            <div class="text-xs text-gray-500 mt-1">Submitted Hours</div>
        </div>
        <div class="stat-card">
            <div class="text-xl font-bold text-amber-600">{{ gmdate('H\h i\m', $pendingHours * 3600) }}</div>
            <div class="text-xs text-gray-500 mt-1">Pending</div>
        </div>
        <div class="stat-card">
            <div class="text-xl font-bold text-green-600">{{ $timesheets->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Days Logged</div>
        </div>
    </div>

    {{-- This Week's Timesheet --}}
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">This Week's Entries</h3>
        </div>
        <div class="table-responsive">
            <table class="w-full" style="min-width:650px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500 font-medium">
                        <th class="text-left px-6 py-3">Date</th>
                        <th class="text-left px-6 py-3">Day</th>
                        <th class="text-left px-6 py-3">Project / Task</th>
                        <th class="text-left px-6 py-3">Time In</th>
                        <th class="text-left px-6 py-3">Time Out</th>
                        <th class="text-left px-6 py-3">Break</th>
                        <th class="text-left px-6 py-3">Total Hours</th>
                        <th class="text-left px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $day = $weekStart->copy(); @endphp
                    @for($i = 0; $i < 7; $i++)
                    @php
                        $currentDay = $weekStart->copy()->addDays($i);
                        $ts = $timesheets->firstWhere('date', $currentDay->toDateString());
                    @endphp
                    <tr class="table-row {{ $currentDay->isWeekend() ? 'bg-gray-50/50' : '' }}">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $currentDay->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-sm {{ $currentDay->isWeekend() ? 'text-gray-400' : 'text-gray-700 font-medium' }}">{{ $currentDay->format('D') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $ts?->project_task ?? ($currentDay->isWeekend() ? '—' : '') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $ts && $ts->time_in ? \Carbon\Carbon::parse($ts->time_in)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $ts && $ts->time_out ? \Carbon\Carbon::parse($ts->time_out)->format('H:i') : '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $ts ? $ts->break_minutes . 'm' : '—' }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-800">{{ $ts ? number_format($ts->total_hours, 1) . 'h' : '—' }}</td>
                        <td class="px-6 py-3">
                            @if($ts)
                            <span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span>
                            @elseif(!$currentDay->isWeekend() && !$currentDay->isFuture())
                            <span class="text-xs text-gray-400">Not logged</span>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endfor
                    <tr class="bg-indigo-50">
                        <td colspan="6" class="px-6 py-3 text-sm font-semibold text-indigo-700">Total</td>
                        <td class="px-6 py-3 text-sm font-bold text-indigo-700">{{ number_format($totalHoursThisWeek, 1) }}h</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Submissions --}}
    @if($recentRequests->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Recent Timesheet Requests</h3>
        <div class="space-y-3">
            @foreach($recentRequests as $ts)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $ts->date->format('d M Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $ts->project_task ?? 'No task' }} · {{ number_format($ts->total_hours, 1) }}h</div>
                </div>
                <span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Log Time Modal --}}
<div x-show="logOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Log Time</h3>
            <button @click="logOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        @if($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('employee.timesheet.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                <input type="date" name="date" required value="{{ today()->toDateString() }}" max="{{ today()->toDateString() }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project / Task</label>
                <input type="text" name="project_task" placeholder="e.g. Website Redesign"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time In *</label>
                    <input type="time" name="time_in" required value="09:00"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Out *</label>
                    <input type="time" name="time_out" required value="17:00"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Break (minutes)</label>
                <input type="number" name="break_minutes" value="60" min="0" max="480"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="logOpen = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">Save Entry</button>
            </div>
        </form>
    </div>
</div>
@endsection
