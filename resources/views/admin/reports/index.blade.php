@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="text-sm text-gray-500">Data-driven workforce insights</p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card">
            <div class="text-2xl font-bold text-gray-900">{{ $totalEmployees }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Employees</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-green-600">{{ $activeEmployees }}</div>
            <div class="text-xs text-gray-500 mt-1">Active Employees</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalLeaveDays, 1) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Leave Days</div>
        </div>
        <div class="stat-card">
            <div class="text-2xl font-bold text-amber-600">{{ number_format($totalHours, 1) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Hours Logged</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Employees by Department --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Employees by Department</h3>
            <canvas id="deptChart" height="200"></canvas>
        </div>

        {{-- Leave by Type --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Leave Requests by Type</h3>
            <canvas id="leaveChart" height="200"></canvas>
        </div>
    </div>

    {{-- Department Table --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Department Summary</h3>
        <div class="table-responsive">
        <table class="w-full text-sm" style="min-width:350px">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-500">
                    <th class="text-left px-4 py-3">Department</th>
                    <th class="text-left px-4 py-3">Employees</th>
                    <th class="text-left px-4 py-3">Headcount %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($employeesByDept as $dept)
                <tr class="table-row">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $dept->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $dept->users_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ $totalEmployees > 0 ? round($dept->users_count / $totalEmployees * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $totalEmployees > 0 ? round($dept->users_count / $totalEmployees * 100) : 0 }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@push('scripts')
<script>
new Chart(document.getElementById('deptChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($employeesByDept->pluck('name')) !!},
        datasets: [{ data: {!! json_encode($employeesByDept->pluck('users_count')) !!},
            backgroundColor: ['#6366f1','#f59e0b','#10b981','#ec4899','#3b82f6','#8b5cf6','#14b8a6','#f97316'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right' } } }
});
new Chart(document.getElementById('leaveChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($leaveByType->pluck('name')) !!},
        datasets: [{ label: 'Requests', data: {!! json_encode($leaveByType->pluck('leaves_count')) !!},
            backgroundColor: '#6366f1', borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
