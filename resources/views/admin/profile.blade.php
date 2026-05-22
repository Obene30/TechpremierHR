@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="space-y-6" x-data="{ tab: 'profile' }">

    <div>
        <h1 class="text-xl font-bold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500">Manage your account information and password</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
            <div class="relative inline-block mb-4">
                <img src="{{ $user->avatarUrl() }}" id="avatarPreview"
                    class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-indigo-100">
                <label class="absolute bottom-0 right-0 w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-indigo-700 shadow">
                    <i class="fas fa-camera text-white text-xs"></i>
                    <input type="file" class="hidden" accept="image/*"
                        onchange="document.getElementById('avatarPreview').src=URL.createObjectURL(this.files[0]);
                                  document.getElementById('avatarInput').files = this.files;">
                </label>
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ $user->designation ?? 'Administrator' }}</p>
            <span class="inline-block mt-2 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">Admin</span>

            <div class="mt-5 space-y-2 text-left text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-envelope text-gray-400 w-4"></i>
                    <span class="truncate">{{ $user->email }}</span>
                </div>
                @if($user->phone)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-phone text-gray-400 w-4"></i> {{ $user->phone }}
                </div>
                @endif
                @if($user->department)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-building text-gray-400 w-4"></i> {{ $user->department->name }}
                </div>
                @endif
                @if($user->date_joined)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-calendar text-gray-400 w-4"></i> Joined {{ $user->date_joined->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit">
                <button @click="tab='profile'"
                    :class="tab==='profile' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                    Edit Profile
                </button>
                <button @click="tab='password'"
                    :class="tab==='password' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                    Change Password
                </button>
            </div>

            {{-- Profile Tab --}}
            <div x-show="tab==='profile'" class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5">Edit Profile</h3>

                @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.update') }}"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    {{-- Hidden avatar input synced from card --}}
                    <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full px-4 py-2.5 border border-gray-100 bg-gray-50 rounded-xl text-sm text-gray-400 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                placeholder="+234 800 000 0000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                            <input type="text" name="designation" value="{{ old('designation', $user->designation) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                placeholder="e.g. HR Director">
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                            class="w-full py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password Tab --}}
            <div x-show="tab==='password'" x-cloak class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5">Change Password</h3>

                <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                    @csrf

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="current_password" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 pr-10"
                                placeholder="Enter current password">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                        @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 pr-10"
                                placeholder="At least 8 characters">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 pr-10"
                                placeholder="Repeat new password">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                            class="w-full py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
