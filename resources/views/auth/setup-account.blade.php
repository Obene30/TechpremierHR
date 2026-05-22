<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Account - Fawthrite HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center p-6" style="font-family: system-ui, sans-serif;">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-th text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Set Up Your Account</h2>
            <p class="text-gray-500 mt-1">You've been invited to join Fawthrite HR</p>
            <div class="mt-2 text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg inline-block">
                {{ $invitation->email }}
            </div>
        </div>
        @if($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('invitation.setup.post', $invitation->token) }}" class="space-y-4" x-data="{ showPass: false }">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $invitation->name) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Your full name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone (optional)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="+234 800 000 0000">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth (optional)</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required minlength="8"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 pr-10" placeholder="Create a password">
                    <button type="button" @click="showPass=!showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="Confirm password">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition-colors mt-2">
                Complete Setup
            </button>
        </form>
    </div>
</body>
</html>
