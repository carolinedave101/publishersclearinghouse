<div class="max-w-lg mx-auto px-4 -mt-10 relative z-10">
    <div class="bg-white rounded-2xl shadow-xl shadow-[#D4AF37]/10 border border-[#D4AF37]/20 p-8 animate-scale-in">
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm flex items-center gap-2">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-600 text-sm flex items-center gap-2">✅ {{ session('success') }}</div>
        @endif
        <div class="flex items-center gap-4 mb-5">
            <div class="w-12 h-12 bg-gradient-to-br from-[#D4AF37] to-amber-500 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-[#D4AF37]/20">🔑</div>
            <div>
                <h2 class="font-bold text-[#1B2A4A] text-lg">Enter Your Winner Code</h2>
                <p class="text-gray-500 text-xs">Found in your winner notification email</p>
            </div>
        </div>
        <a href="{{ route('register') }}" class="block text-center text-xs text-[#1B2A4A]/50 mb-4 hover:text-[#D4AF37] transition-colors">
            Don't have a winner code? <span class="text-[#D4AF37] font-semibold">Register now</span>
        </a>
        <form action="{{ route('winner.lookup') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="sr-only">Winner Code</label>
                <input
                    type="text"
                    name="code"
                    id="code"
                    required
                    maxlength="10"
                    placeholder="e.g. W33KHUMX"
                    class="w-full px-5 py-4 text-center text-xl font-bold tracking-[0.3em] uppercase border-2 border-[#D4AF37]/30 rounded-xl focus:border-[#D4AF37] focus:ring-4 focus:ring-[#D4AF37]/20 outline-none transition-all placeholder:tracking-normal placeholder:text-gray-300 bg-gradient-to-r from-yellow-50/50 to-white"
                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10)"
                >
            </div>
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                Claim Your Prize →
            </button>
            <p class="text-center text-xs text-gray-400">
                By entering your code, you agree to our <a href="#" class="text-[#D4AF37] hover:underline">Terms of Service</a>
            </p>
        </form>
    </div>
</div>