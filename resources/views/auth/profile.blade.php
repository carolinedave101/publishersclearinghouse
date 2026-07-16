@extends('layouts.app')

@section('title', 'My Profile - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-[#1B2A4A] mb-8">My Profile</h1>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Account Details</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-[#1B2A4A] to-[#2A3A5A] text-white font-semibold hover:from-[#2A3A5A] hover:to-[#1B2A4A] transition-all shadow-lg shadow-[#1B2A4A]/20">
                        Save Changes
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Change Password</h2>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Current Password</label>
                        <input type="password" name="current_password" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                        @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">New Password</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>

                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-semibold hover:from-[#B8960F] hover:to-[#D4AF37] transition-all shadow-lg shadow-[#D4AF37]/20">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('components.footer')
@endsection
