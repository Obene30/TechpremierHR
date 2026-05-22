@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="space-y-6" x-data="{ tab: 'profile' }">
    <div>
        <h1 class="text-xl font-bold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500">Manage your personal information and account settings</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="relative inline-block mb-4">
                <img src="{{ $user->avatarUrl() }}" id="avatarPreview" class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-indigo-100">
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ $user->designation ?? 'Employee' }}</p>
            @if($user->department)
            <p class="text-xs text-indigo-600 mt-1">{{ $user->department->name }}</p>
            @endif
            <span class="badge badge-{{ $user->status }} mt-2">{{ ucfirst(str_replace('_',' ',$user->status)) }}</span>
            <div class="mt-4 space-y-2 text-left text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-envelope text-gray-400 w-4 text-sm"></i> {{ $user->email }}
                </div>
                @if($user->phone)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-phone text-gray-400 w-4 text-sm"></i> {{ $user->phone }}
                </div>
                @endif
                @if($user->date_joined)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-calendar text-gray-400 w-4 text-sm"></i> Joined {{ $user->date_joined->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
                <button @click="tab='profile'" :class="tab==='profile' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">Profile</button>
                <button @click="tab='password'" :class="tab==='password' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">Password</button>
            </div>

            {{-- Profile Tab --}}
            <div x-show="tab==='profile'" class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5">Edit Profile</h3>
                @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ $user->avatarUrl() }}" id="previewImg" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                            <label class="cursor-pointer px-4 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50 text-gray-600">
                                <i class="fas fa-upload mr-2"></i> Choose Photo
                                <input type="file" name="avatar" class="hidden" accept="image/*"
                                    onchange="document.getElementById('previewImg').src=URL.createObjectURL(this.files[0])">
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-xl text-sm text-gray-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" placeholder="+234 ...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700">Save Changes</button>
                </form>
            </div>

            {{-- Password Tab --}}
            <div x-show="tab==='password'" x-cloak class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5">Change Password</h3>
                <form method="POST" action="{{ route('employee.profile.password') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
