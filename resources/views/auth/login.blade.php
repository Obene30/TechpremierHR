<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Fawthrite HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 50%, #ede9fe 100%); }
    </style>
</head>
<body class="min-h-screen flex">
    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-1/2 hero-bg flex-col justify-between p-12">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-th text-white"></i>
            </div>
            <span class="text-xl font-bold text-gray-800"><span class="text-indigo-600">Fawthrite</span> HR</span>
        </div>
        <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Smarter HR.<br><span class="text-indigo-600">Stronger Teams.</span></h1>
            <p class="text-gray-500 mb-8">Fawthrite HR helps you manage your workforce efficiently with ease.</p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-users text-indigo-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Employee Management</div>
                        <div class="text-sm text-gray-500">Store and manage employee information, appraisals, and more in one secure place.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-calendar-check text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Leave Management</div>
                        <div class="text-sm text-gray-500">Manage leave requests, approvals, and track employee absences effortlessly.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-clock text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Timesheet Tracking</div>
                        <div class="text-sm text-gray-500">Monitor work hours and improve team efficiency with accurate timesheets.</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-chart-bar text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Insightful Reports</div>
                        <div class="text-sm text-gray-500">Make data-driven decisions with powerful reports and analytics.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-400">
            <i class="fas fa-shield-alt text-indigo-400"></i>
            <span>Secure, cloud-based HR platform</span>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="lg:hidden flex items-center justify-center gap-2 mb-6">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-th text-white"></i>
                    </div>
                    <span class="text-xl font-bold"><span class="text-indigo-600">Fawthrite</span> HR</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Welcome back!</h2>
                <p class="text-gray-500 mt-1">Sign in to your Fawthrite HR account</p>
            </div>

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5" x-data="{ showPass: false }">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400"
                            placeholder="Enter your email address">
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="#" class="text-sm text-indigo-600 hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400"
                            placeholder="Enter your password">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    Sign In
                </button>
                <div class="text-center text-sm text-gray-400">or</div>
                <button type="button" class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 font-medium py-3 rounded-xl hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Sign in with Google
                </button>
                <p class="text-center text-sm text-gray-500">
                    Don't have an account? <a href="#" class="text-indigo-600 hover:underline">Contact your administrator</a>
                </p>
            </form>

            <div class="mt-6 p-4 bg-gray-50 rounded-xl text-xs text-gray-500">
                <div class="font-medium mb-1">Demo credentials:</div>
                <div>Admin: admin@fawthritehr.com / password</div>
                <div>Employee: john.doe@company.com / password</div>
            </div>
        </div>
    </div>
</body>
</html>
