<!DOCTYPE html>
<html lang="en" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    isMobile: window.innerWidth < 1024
}" @resize.window="isMobile = window.innerWidth < 1024; if(!isMobile) sidebarOpen = true;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fawthrite HR') - Fawthrite HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #1a1f3a;
            transition: transform 0.3s ease, width 0.3s ease;
        }
        .sidebar-collapsed { width: 72px; }

        /* Mobile: sidebar is an overlay */
        @media (max-width: 1023px) {
            .sidebar {
                position: fixed !important;
                top: 0; left: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(-100%);
                width: 260px !important;
            }
            .sidebar.open { transform: translateX(0); }
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 16px; border-radius: 8px;
            color: #94a3b8; transition: all 0.2s;
            cursor: pointer; text-decoration: none;
            white-space: nowrap; overflow: hidden;
        }
        .nav-item:hover { background: #252b4a; color: #e2e8f0; }
        .nav-item.active { background: #6366f1; color: white; }
        .nav-item .icon { width: 20px; min-width: 20px; text-align: center; }

        .stat-card { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-approved { background: #d1fae5; color: #059669; }
        .badge-rejected { background: #fee2e2; color: #dc2626; }
        .badge-active { background: #d1fae5; color: #059669; }
        .badge-inactive { background: #f1f5f9; color: #64748b; }
        .badge-on_leave { background: #ede9fe; color: #7c3aed; }
        .badge-submitted { background: #dbeafe; color: #2563eb; }
        .table-row:hover { background: #f8fafc; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    </style>
</head>
<body class="flex bg-slate-100 min-h-screen">

{{-- Mobile backdrop --}}
<div x-show="sidebarOpen && isMobile"
     @click="sidebarOpen = false"
     x-cloak
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

{{-- Sidebar --}}
<aside class="sidebar flex flex-col fixed left-0 top-0 z-50 h-screen flex-shrink-0"
       :class="{
           'open': sidebarOpen && isMobile,
           'sidebar-collapsed': !sidebarOpen && !isMobile
       }">

    {{-- Logo --}}
    <div class="flex items-center gap-3 p-5 border-b border-white/10 flex-shrink-0">
        <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-th text-white text-sm"></i>
        </div>
        <span class="text-white font-bold text-lg" x-show="sidebarOpen" x-cloak>
            <span class="text-indigo-300">Fawthrite</span> HR
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto mt-2">
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-th-large icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Dashboard</span>
        </a>
        <a href="{{ route('admin.employees.index') }}" class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-users icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Employees</span>
        </a>
        <a href="{{ route('admin.departments.index') }}" class="nav-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-building icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Departments</span>
        </a>
        <a href="{{ route('admin.leave.index') }}" class="nav-item {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-calendar-check icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Leave Management</span>
        </a>
        <a href="{{ route('admin.timesheet.index') }}" class="nav-item {{ request()->routeIs('admin.timesheet.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-clock icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Timesheet</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-chart-bar icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Reports</span>
        </a>
        <a href="{{ route('admin.profile.show') }}" class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-user-circle icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>My Profile</span>
        </a>
        @else
        <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-th-large icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Dashboard</span>
        </a>
        <a href="{{ route('employee.leave.index') }}" class="nav-item {{ request()->routeIs('employee.leave.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-calendar-check icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Leave</span>
        </a>
        <a href="{{ route('employee.timesheet.index') }}" class="nav-item {{ request()->routeIs('employee.timesheet.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-clock icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Timesheet</span>
        </a>
        <a href="{{ route('employee.documents.index') }}" class="nav-item {{ request()->routeIs('employee.documents.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-file-alt icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>Documents</span>
        </a>
        <a href="{{ route('employee.profile.show') }}" class="nav-item {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}" @click="if(isMobile) sidebarOpen=false">
            <i class="fas fa-user icon"></i>
            <span x-show="sidebarOpen || isMobile" x-cloak>My Profile</span>
        </a>
        @endif

        <div class="mt-4 pt-4 border-t border-white/10">
            <a href="#" class="nav-item">
                <i class="fas fa-cog icon"></i>
                <span x-show="sidebarOpen || isMobile" x-cloak>Settings</span>
            </a>
        </div>
    </nav>

    {{-- User info --}}
    <div class="p-3 border-t border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/10 cursor-pointer">
            <img src="{{ auth()->user()->avatarUrl() }}" class="w-9 h-9 rounded-full flex-shrink-0 object-cover" alt="">
            <div x-show="sidebarOpen || isMobile" x-cloak class="overflow-hidden">
                <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                <div class="text-indigo-300 text-xs">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- Main content wrapper --}}
<div class="flex flex-col flex-1 min-w-0 transition-all duration-300"
     :style="!isMobile ? (sidebarOpen ? 'margin-left:260px' : 'margin-left:72px') : 'margin-left:0'">

    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-30 flex items-center justify-between px-4 py-3 gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <button @click="sidebarOpen = !sidebarOpen"
                class="text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 flex-shrink-0">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <div class="relative hidden sm:block">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search employees..."
                    class="pl-9 pr-4 py-2 text-sm bg-gray-100 rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-indigo-300 w-48 md:w-64">
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- Mobile search icon --}}
            <button class="sm:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-search"></i>
            </button>
            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-bell text-lg"></i>
                @if(auth()->user()->isAdmin())
                @php $notifCount = \App\Models\Leave::where('status','pending')->count() + \App\Models\Timesheet::where('status','submitted')->count(); @endphp
                @if($notifCount > 0)
                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center leading-none">{{ $notifCount }}</span>
                @endif
                @endif
            </button>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100">
                    <img src="{{ auth()->user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover" alt="">
                    <span class="text-sm font-medium text-gray-700 hidden md:block max-w-24 truncate">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:block"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user text-gray-400 w-4"></i> My Profile
                    </a>
                    @else
                    <a href="{{ route('employee.profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user text-gray-400 w-4"></i> My Profile
                    </a>
                    @endif
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-4 md:p-6">
        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak
            class="mb-4 flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1">{{ session('success') }}</span>
            <button @click="show=false" class="text-green-500 flex-shrink-0"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak
            class="mb-4 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
            <span class="flex-1">{{ session('error') }}</span>
            <button @click="show=false" class="text-red-500 flex-shrink-0"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if(session('invite_link'))
        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-sm">
            <div class="font-semibold mb-1">Invitation Link (share with employee):</div>
            <div class="text-xs bg-blue-100 p-2 rounded font-mono break-all">{{ session('invite_link') }}</div>
        </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
