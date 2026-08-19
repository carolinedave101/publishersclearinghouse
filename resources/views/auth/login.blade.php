@extends('layouts.app')

@section('title', 'Login - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center bg-gradient-to-br from-[#FFFBF0] to-[#F5F0E0] px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl shadow-[#D4AF37]/10 border border-[#D4AF37]/20 p-8">
            <div class="text-center mb-8">
                <div class="text-5xl mb-4">🏆</div>
                <h1 class="text-3xl font-bold text-[#1B2A4A]">Winner Login</h1>
                <p class="text-[#1B2A4A]/60 mt-2">Access your winner dashboard</p>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-8 p-1 bg-[#1B2A4A]/5 rounded-xl" x-data="{ tab: 'code' }">
                <button type="button" @click="tab = 'code'"
                    :class="tab === 'code' ? 'bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] shadow-lg' : 'text-[#1B2A4A]/60 hover:text-[#1B2A4A]'"
                    class="py-2.5 rounded-lg text-sm font-bold transition-all">Winner Code</button>
                <button type="button" @click="tab = 'password'"
                    :class="tab === 'password' ? 'bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] shadow-lg' : 'text-[#1B2A4A]/60 hover:text-[#1B2A4A]'"
                    class="py-2.5 rounded-lg text-sm font-bold transition-all">Email & Password</button>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div x-show="tab === 'code'" x-cloak>
                <form action="{{ route('winner.lookup') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="code" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Winner Code</label>
                        <input type="text" name="code" id="code" required maxlength="10" autofocus
                            placeholder="e.g. W33KHUMX"
                            class="w-full px-5 py-4 text-center text-xl font-bold tracking-[0.3em] uppercase border-2 border-[#D4AF37]/30 rounded-xl focus:border-[#D4AF37] focus:ring-4 focus:ring-[#D4AF37]/20 outline-none transition-all placeholder:tracking-normal placeholder:text-gray-300 bg-gradient-to-r from-yellow-50/50 to-white"
                            oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10)">
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                        🔑 Access My Dashboard →
                    </button>
                </form>
            </div>

            <div x-show="tab === 'password'" x-cloak>
                <form action="{{ route('winner.login.password') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Password</label>
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                        🔑 Sign In →
                    </button>
                </form>
            </div>

            <div class="mt-8 p-4 bg-gradient-to-r from-[#D4AF37]/5 to-amber-50 rounded-xl border border-[#D4AF37]/10">
                <p class="text-xs text-gray-500 text-center">
                    <span class="text-[#D4AF37] font-semibold">Don't have a code?</span><br>
                    <a href="{{ route('register') }}" class="text-[#D4AF37] font-semibold hover:underline">Register now</a> to get your personal winner code.
                </p>
            </div>
        </div>
    </div>
</div>
@include('components.footer')
@endsection