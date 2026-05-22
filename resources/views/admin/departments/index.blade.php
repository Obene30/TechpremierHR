@extends('layouts.app')
@section('title', 'Departments')
@section('content')
<div class="space-y-6" x-data="{ createOpen: false, editOpen: false, editDept: {} }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Departments</h1>
            <p class="text-sm text-gray-500">Manage organisational departments</p>
        </div>
        <button @click="createOpen = true"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-colors">
            <i class="fas fa-plus"></i> New Department
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="stat-card">
            <div class="text-2xl font-bold text-gray-900">{{ $departments->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Departments</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-indigo-600">{{ $departments->sum('users_count') }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Employees</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-green-600">{{ $departments->where('users_count', 0)->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Empty Departments</div>
        </div>
    </div>

    {{-- Department Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($departments as $dept)
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-indigo-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $dept->name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ $dept->users_count }} {{ Str::plural('employee', $dept->users_count) }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button
                        @click="editDept = { id: {{ $dept->id }}, name: '{{ addslashes($dept->name) }}', description: '{{ addslashes($dept->description ?? '') }}' }; editOpen = true"
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    @if($dept->users_count === 0)
                    <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete {{ $dept->name }}?')"
                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                    @else
                    <button disabled title="Cannot delete — has employees"
                        class="w-8 h-8 flex items-center justify-center text-gray-200 rounded-lg cursor-not-allowed">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                    @endif
                </div>
            </div>
            @if($dept->description)
            <p class="text-xs text-gray-500 mt-3 leading-relaxed">{{ $dept->description }}</p>
            @endif
            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.employees.index', ['department' => $dept->id]) }}"
                    class="text-xs text-indigo-600 hover:underline font-medium">
                    View employees <i class="fas fa-arrow-right text-xs ml-1"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-2xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-building text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-gray-700 font-semibold mb-1">No departments yet</h3>
            <p class="text-gray-400 text-sm mb-4">Create your first department to organise your workforce.</p>
            <button @click="createOpen = true"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Create Department
            </button>
        </div>
        @endforelse
    </div>

{{-- Create Modal --}}
<div x-show="createOpen" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">New Department</h3>
            <button @click="createOpen = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @if($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 px-3 py-2 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Name *</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                    placeholder="e.g. Engineering">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                    placeholder="Brief description of this department...">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="createOpen = false"
                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div x-show="editOpen" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Edit Department</h3>
            <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" :action="'/admin/departments/' + editDept.id" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Name *</label>
                <input type="text" name="name" required x-model="editDept.name"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea name="description" rows="3" x-model="editDept.description"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="editOpen = false"
                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>{{-- edit modal --}}
</div>{{-- x-data wrapper --}}
@endsection
