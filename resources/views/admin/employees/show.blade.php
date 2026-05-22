@extends('layouts.app')
@section('title', $employee->name)
@section('content')
<div class="space-y-6">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.employees.index') }}" class="hover:text-indigo-600">Employees</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-800 font-medium">{{ $employee->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <img src="{{ $employee->avatarUrl() }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
            <h2 class="text-lg font-bold text-gray-900">{{ $employee->name }}</h2>
            <p class="text-sm text-gray-500">{{ $employee->designation ?? 'Employee' }}</p>
            <span class="badge badge-{{ $employee->status }} mt-2">{{ ucfirst(str_replace('_',' ',$employee->status)) }}</span>
            <div class="mt-4 space-y-2 text-left">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-envelope text-gray-400 w-4"></i> {{ $employee->email }}
                </div>
                @if($employee->phone)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-phone text-gray-400 w-4"></i> {{ $employee->phone }}
                </div>
                @endif
                @if($employee->department)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-building text-gray-400 w-4"></i> {{ $employee->department->name }}
                </div>
                @endif
                @if($employee->date_joined)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-calendar text-gray-400 w-4"></i> Joined {{ $employee->date_joined->format('d M Y') }}
                </div>
                @endif
                @if($employee->date_of_birth)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-birthday-cake text-gray-400 w-4"></i> {{ $employee->date_of_birth->format('d M Y') }}
                </div>
                @endif
            </div>
            <div class="mt-5 pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.employees.status', $employee) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="flex-1 text-sm border border-gray-200 rounded-lg px-2 py-1.5">
                        <option value="active" {{ $employee->status=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ $employee->status=='inactive'?'selected':'' }}>Inactive</option>
                        <option value="on_leave" {{ $employee->status=='on_leave'?'selected':'' }}>On Leave</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Update</button>
                </form>
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Leave History --}}
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Leave History</h3>
                <div class="table-responsive">
                    <table class="w-full text-sm" style="min-width:400px">
                        <thead class="bg-gray-50">
                            <tr class="text-xs text-gray-500">
                                <th class="text-left px-3 py-2">Type</th>
                                <th class="text-left px-3 py-2">From</th>
                                <th class="text-left px-3 py-2">To</th>
                                <th class="text-left px-3 py-2">Days</th>
                                <th class="text-left px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->leaves->take(5) as $leave)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $leave->leaveType->name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $leave->start_date->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $leave->end_date->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $leave->days }}</td>
                                <td class="px-3 py-2"><span class="badge badge-{{ $leave->status }}">{{ ucfirst($leave->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400 text-xs">No leave records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Documents</h3>
                @forelse($employee->documents as $doc)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-indigo-500 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-700">{{ $doc->name }}</div>
                            <div class="text-xs text-gray-400">{{ strtoupper($doc->file_type) }} · {{ $doc->sizeFormatted() }}</div>
                        </div>
                    </div>
                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">View</a>
                </div>
                @empty
                <div class="text-center text-gray-400 text-sm py-4">No documents uploaded</div>
                @endforelse
            </div>

            {{-- Recent Timesheets --}}
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Recent Timesheets</h3>
                <div class="table-responsive">
                    <table class="w-full text-sm" style="min-width:400px">
                        <thead class="bg-gray-50">
                            <tr class="text-xs text-gray-500">
                                <th class="text-left px-3 py-2">Date</th>
                                <th class="text-left px-3 py-2">Task</th>
                                <th class="text-left px-3 py-2">Hours</th>
                                <th class="text-left px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->timesheets->take(5) as $ts)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $ts->date->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $ts->project_task ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ number_format($ts->total_hours,1) }}h</td>
                                <td class="px-3 py-2"><span class="badge badge-{{ $ts->status }}">{{ ucfirst($ts->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 text-xs">No timesheet records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
