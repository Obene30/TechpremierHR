@extends('layouts.app')
@section('title', 'Employees')
@section('content')
<div class="space-y-6" x-data="{ inviteOpen: false }">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Employees</h1>
            <p class="text-sm text-gray-500">Manage your workforce</p>
        </div>
        <button @click="inviteOpen = true" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-paper-plane"></i> Invite Employee
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="stat-card">
            <div class="text-2xl font-bold text-gray-900">{{ $employees->total() }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Employees</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-green-600">{{ $totalActive }}</div>
            <div class="text-xs text-gray-500 mt-1">Active</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-gray-400">{{ $totalInactive }}</div>
            <div class="text-xs text-gray-500 mt-1">Inactive</div>
        </div>
    </div>

    {{-- Filters + Table --}}
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-5 border-b border-gray-100 flex flex-wrap gap-3 items-center">
            <form method="GET" class="flex flex-wrap gap-2 w-full">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..."
                        class="pl-8 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 w-full sm:w-56">
                </div>
                <select name="department" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status')=='on_leave' ? 'selected' : '' }}>On Leave</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="w-full" style="min-width:700px">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-500 font-medium">
                        <th class="text-left px-6 py-3">Employee</th>
                        <th class="text-left px-6 py-3">Department</th>
                        <th class="text-left px-6 py-3">Designation</th>
                        <th class="text-left px-6 py-3">Email</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Joined</th>
                        <th class="text-left px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $emp)
                    <tr class="table-row">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $emp->avatarUrl() }}" class="w-9 h-9 rounded-full object-cover">
                                <span class="font-medium text-gray-800 text-sm">{{ $emp->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->department->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $emp->designation ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $emp->email }}</td>
                        <td class="px-6 py-4"><span class="badge badge-{{ $emp->status }}">{{ ucfirst(str_replace('_',' ',$emp->status)) }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $emp->date_joined?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.employees.show', $emp) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">No employees found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $employees->withQueryString()->links() }}
        </div>
    </div>

{{-- Invite Modal --}}
<div x-show="inviteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Invite Employee</h3>
            <button @click="inviteOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.employees.invite') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="employee@company.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name (optional)</label>
                <input type="text" name="name" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Employee name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">Select department</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="inviteOpen = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">Send Invitation</button>
            </div>
        </form>
    </div>
</div>{{-- closes modal --}}
</div>{{-- closes x-data wrapper --}}
@endsection
