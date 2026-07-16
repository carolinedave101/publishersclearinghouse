@php $siteConfig = \App\Models\Setting::getSiteConfig(); @endphp
<footer class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] text-white/60 border-t border-[#D4AF37]/10">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <img src="{{ $siteConfig['logo'] ?? asset('logo.png') }}" alt="{{ $siteConfig['site_name'] ?? 'Publishers Clearing House' }}" class="h-9 w-auto mb-2" style="border-radius: 10px;">
                <p class="text-sm mt-2 text-white/40">{{ $siteConfig['footer_text'] ?? 'Publishers Clearing House — Changing lives with prizes since 1967.' }}</p>
                <div class="flex gap-3 mt-4">
                    <a href="#" class="w-9 h-9 bg-[#D4AF37]/10 rounded-full flex items-center justify-center hover:bg-[#D4AF37]/20 transition-colors text-sm" aria-label="Facebook">📘</a>
                    <a href="#" class="w-9 h-9 bg-[#D4AF37]/10 rounded-full flex items-center justify-center hover:bg-[#D4AF37]/20 transition-colors text-sm" aria-label="Twitter">🐦</a>
                    <a href="#" class="w-9 h-9 bg-[#D4AF37]/10 rounded-full flex items-center justify-center hover:bg-[#D4AF37]/20 transition-colors text-sm" aria-label="Instagram">📷</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Quick Links</h4>
                <div class="flex flex-col gap-1.5">
                    <a href="{{ route('giveaways') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Giveaways</a>
                    <a href="{{ route('games') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Spin & Win</a>
                    <a href="{{ route('shop') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Shop</a>
                    <a href="{{ route('memberships') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Memberships</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Support</h4>
                <div class="flex flex-col gap-1.5">
                    <a href="#" class="text-sm hover:text-[#D4AF37] transition-colors">FAQ</a>
                    <a href="#" class="text-sm hover:text-[#D4AF37] transition-colors">Contact Us</a>
                    <a href="#" class="text-sm hover:text-[#D4AF37] transition-colors">Privacy Policy</a>
                    <a href="#" class="text-sm hover:text-[#D4AF37] transition-colors">Terms of Service</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Winner Support</h4>
                <div class="flex flex-col gap-1.5">
                    <a href="{{ route('home') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Check Your Code</a>
                    <a href="{{ route('winner.dashboard') }}" class="text-sm hover:text-[#D4AF37] transition-colors">Winner Dashboard</a>
                </div>
            </div>
        </div>
        <div class="border-t border-[#D4AF37]/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-2">
            <p class="text-xs text-white/30">&copy; {{ date('Y') }} {{ $siteConfig['site_name'] ?? 'Publishers Clearing House' }}. All Rights Reserved.</p>
            <p class="text-xs text-white/20">🏆 {{ $siteConfig['footer_tagline'] ?? 'Over $500 Million Awarded to Winners Like You' }}</p>
        </div>
    </div>
</footer>