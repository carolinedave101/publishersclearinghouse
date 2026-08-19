@extends('layouts.app')

@section('title', 'Register - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center bg-gradient-to-br from-[#FFFBF0] to-[#F5F0E0] px-4 py-12">
    <div class="w-full max-w-lg">
        <div class="bg-gradient-to-r from-[#1B2A4A] via-[#2C3E6B] to-[#1B2A4A] border-2 border-[#D4AF37] rounded-2xl p-5 mb-6 text-center shadow-xl shadow-[#D4AF37]/20">
            <p class="text-[#D4AF37] text-sm font-semibold uppercase tracking-widest mb-1">Publishers Clearing House</p>
            <h2 class="text-white text-xl md:text-2xl font-bold leading-snug">Register to stand a chance to win a <span class="text-[#D4AF37]">$10,000</span> to <span class="text-[#D4AF37]">$8,500,000</span> prize award</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-xl shadow-[#D4AF37]/10 border border-[#D4AF37]/20 p-8">
            <div class="text-center mb-8">
                <div class="text-5xl mb-4">🏆</div>
                <h1 class="text-3xl font-bold text-[#1B2A4A]">Register as a Winner</h1>
                <p class="text-[#1B2A4A]/60 mt-2">Complete your registration to receive your personal winner code</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Street Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="zip" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">ZIP Code</label>
                    <input type="text" name="zip" id="zip" value="{{ old('zip') }}" required maxlength="10"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Create Password</label>
                        <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                        <p class="text-xs text-[#1B2A4A]/40 mt-1">At least 8 characters</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                    🏆 Claim My Winner Code →
                </button>
            </form>

            <p class="text-center text-sm text-[#1B2A4A]/60 mt-6">
                Already have a winner code?
                <a href="{{ route('login') }}" class="text-[#D4AF37] font-semibold hover:underline">Log in here</a>
            </p>
        </div>
    </div>
</div>
@include('components.footer')
@endsection