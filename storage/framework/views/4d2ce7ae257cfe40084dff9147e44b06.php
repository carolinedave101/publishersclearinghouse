<?php $siteConfig = \App\Models\Setting::getSiteConfig(); ?>
<nav class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] text-white sticky top-0 z-50 shadow-lg border-b border-[#D4AF37]/20"
     x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 group">
                <img src="<?php echo e($siteConfig['logo'] ?? asset('logo.png')); ?>" alt="<?php echo e($siteConfig['site_name'] ?? 'Publishers Clearing House'); ?>" class="h-9 w-auto group-hover:scale-105 transition-transform" style="border-radius: 10px;">
            </a>
            <div class="hidden md:flex items-center gap-6">
                <a href="<?php echo e(route('home')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] transition-colors">Home</a>
                <a href="<?php echo e(route('giveaways')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] transition-colors">Giveaways</a>
                <a href="<?php echo e(route('games')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] transition-colors">Games</a>
                <a href="<?php echo e(route('shop')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] transition-colors relative">
                    Shop
                    <span x-show="$store.cart.count > 0"
                          x-text="$store.cart.count"
                          x-cloak
                          class="absolute -top-2 -right-4 bg-[#D4AF37] text-[#1B2A4A] text-[10px] font-bold min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1">
                    </span>
                </a>
                <a href="<?php echo e(route('memberships')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] transition-colors">Memberships</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('winner_id')): ?>
                    <?php
                        $winnerNav = \App\Models\Winner::find(session('winner_id'));
                    ?>
                    <a href="<?php echo e(route('winner.dashboard')); ?>" class="hidden lg:flex items-center gap-2 text-sm bg-[#D4AF37]/10 hover:bg-[#D4AF37]/20 text-white px-4 py-2 rounded-lg border border-[#D4AF37]/20 hover:border-[#D4AF37]/40 transition-all">
                        <span>🏠</span>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-white/70 hover:text-[#D4AF37] transition-colors bg-white/5 hover:bg-[#D4AF37]/10 px-3 py-2 rounded-lg border border-white/10 hover:border-[#D4AF37]/30 transition-all">
                            <span class="w-7 h-7 rounded-full bg-gradient-to-br from-[#D4AF37] to-amber-500 flex items-center justify-center text-xs font-bold text-white">🏆</span>
                            <span class="font-medium"><?php echo e($winnerNav?->first_name); ?></span>
                            <svg class="w-3.5 h-3.5 text-white/40" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-2xl border border-[#D4AF37]/20 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-400">Signed in as</p>
                                <p class="text-sm font-bold text-[#1B2A4A]"><?php echo e($winnerNav?->first_name); ?> <?php echo e($winnerNav?->last_name); ?></p>
                                <p class="text-[10px] text-[#D4AF37] font-mono mt-0.5">Code: <?php echo e($winnerNav?->unique_code); ?></p>
                            </div>
                            <div class="py-1">
                                <a href="<?php echo e(route('winner.dashboard')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1B2A4A] hover:bg-[#D4AF37]/5 font-semibold transition-colors">
                                    <span class="w-8 h-8 rounded-lg bg-[#D4AF37]/10 flex items-center justify-center text-base">🏠</span>
                                    <div><p class="font-medium">My Dashboard</p><p class="text-[10px] text-gray-400 font-normal">Overview & stats</p></div>
                                </a>
                            </div>
                            <hr class="my-1 border-gray-100">
                            <div class="px-4 py-2">
                                <form method="POST" action="<?php echo e(route('winner.logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-base">🚪</span>
                                        <div><p class="font-medium">Sign Out</p><p class="text-[10px] text-gray-400 font-normal text-left">End your session</p></div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="px-5 py-2 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-lg text-sm font-bold hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                        Login with Code
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white/70 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        <div x-show="mobileOpen" x-cloak class="md:hidden pb-4">
            <div class="flex flex-col gap-2">
                <a href="<?php echo e(route('home')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] py-1">Home</a>
                <a href="<?php echo e(route('giveaways')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] py-1">Giveaways</a>
                <a href="<?php echo e(route('games')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] py-1">Games</a>
                <a href="<?php echo e(route('shop')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] py-1">
                    Shop <span x-show="$store.cart.count > 0" x-text="'(' + $store.cart.count + ')'" x-cloak class="text-[#D4AF37]"></span>
                </a>
                <a href="<?php echo e(route('memberships')); ?>" class="text-sm text-white/70 hover:text-[#D4AF37] py-1">Memberships</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('winner_id')): ?>
                    <a href="<?php echo e(route('winner.dashboard')); ?>" class="text-sm bg-[#D4AF37]/20 text-[#D4AF37] font-bold py-2 px-3 rounded-lg border border-[#D4AF37]/30">🏠 My Dashboard</a>
                    <form method="POST" action="<?php echo e(route('winner.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm text-red-300 hover:text-red-200 py-1 text-left">🚪 Sign Out</button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="text-sm bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold py-2 px-4 rounded-lg text-center">Login with Code</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/components/nav.blade.php ENDPATH**/ ?>