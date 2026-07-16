<?php $__env->startSection('title', 'My Dashboard - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] border-b border-[#D4AF37]/10">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-xs text-white/40 mb-3">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-[#D4AF37] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[#D4AF37]">My Dashboard</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="px-4 py-2 bg-[#D4AF37]/20 text-[#D4AF37] rounded-lg text-sm font-semibold border border-[#D4AF37]/30 transition-all hover:bg-[#D4AF37]/30">
                🏠 Dashboard
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_messages']): ?>
                <a href="<?php echo e(route('winner.dashboard')); ?>#messages" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    💬 Messages
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_documents']): ?>
                <a href="<?php echo e(route('winner.dashboard')); ?>#documents" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📄 Documents
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_deposits']): ?>
                <a href="<?php echo e(route('winner.deposits')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📥 Deposit
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_withdrawals']): ?>
                <a href="<?php echo e(route('winner.withdrawals')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    💸 Withdraw
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_transactions']): ?>
                <a href="<?php echo e(route('winner.transactions')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📊 Transactions
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_orders']): ?>
                <a href="<?php echo e(route('winner.orders')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📦 My Orders
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_giveaways']): ?>
                <a href="<?php echo e(route('giveaways')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🎁 Giveaways
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_games']): ?>
                <a href="<?php echo e(route('games')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🎡 Spin & Win
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_shop']): ?>
                <a href="<?php echo e(route('shop')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🛍️ Shop
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_memberships']): ?>
                <a href="<?php echo e(route('memberships')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    ⭐ Memberships
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<main class="flex-1 bg-gradient-to-b from-[#FFFBF0] to-[#F5F0E0]">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <div class="animate-slide-down p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2 shadow-sm">
                <span>✅</span> <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$winner->is_claimed): ?>
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B2A4A] via-[#2A1F00] to-[#1B2A4A]">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-5 left-10 text-5xl animate-float" style="animation-delay: 0s">🏆</div>
                <div class="absolute top-10 right-20 text-4xl animate-float" style="animation-delay: 1s">💰</div>
                <div class="absolute bottom-10 left-20 text-5xl animate-float" style="animation-delay: 2s">🎉</div>
                <div class="absolute bottom-5 right-10 text-3xl animate-float" style="animation-delay: 0.5s">⭐</div>
            </div>
            <div class="max-w-6xl mx-auto px-4 py-16 md:py-20 text-center relative">
                <div class="inline-block px-5 py-1.5 bg-[#D4AF37]/20 border border-[#D4AF37]/30 rounded-full text-[#D4AF37] text-xs font-semibold mb-5 tracking-widest uppercase animate-fade-in-up">
                    🎯 Prize Unclaimed
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up animate-delay-100">
                    Congratulations, <?php echo e($winner->first_name); ?>!
                </h1>
                <p class="text-white/70 text-lg md:text-xl mb-2 animate-fade-in-up animate-delay-200">
                    You've won a prize worth
                </p>
                <div class="text-5xl md:text-7xl font-bold gold-gradient inline-block mb-6 animate-fade-in-up animate-delay-300">
                    $<?php echo e(number_format($winner->prize_amount, 0)); ?>

                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner->prize_description): ?>
                    <p class="text-white/50 text-sm mb-6 animate-fade-in-up animate-delay-300"><?php echo e($winner->prize_description); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="animate-fade-in-up animate-delay-400">
                    <form action="<?php echo e(route('winner.claim')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-10 py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-2xl shadow-[#D4AF37]/30 animate-pulse-gold">
                            🎉 Claim Your Prize Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B2A4A] via-[#1A3A1A] to-[#1B2A4A]">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-5 left-10 text-5xl animate-float" style="animation-delay: 0s">🎉</div>
                <div class="absolute top-10 right-20 text-4xl animate-float" style="animation-delay: 1s">💰</div>
                <div class="absolute bottom-10 left-20 text-5xl animate-float" style="animation-delay: 2s">🏆</div>
                <div class="absolute bottom-5 right-10 text-3xl animate-float" style="animation-delay: 0.5s">⭐</div>
            </div>
            <div class="max-w-6xl mx-auto px-4 py-16 md:py-20 text-center relative">
                <div class="text-6xl mb-4 animate-bounce">🎉</div>
                <div class="inline-block px-5 py-1.5 bg-green-500/20 border border-green-500/30 rounded-full text-green-400 text-xs font-semibold mb-5 tracking-widest uppercase animate-fade-in-up">
                    ✅ Prize Claimed
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up animate-delay-100">
                    Congratulations, <?php echo e($winner->first_name); ?>!
                </h1>
                <p class="text-white/80 text-lg md:text-xl mb-2 animate-fade-in-up animate-delay-200">
                    Your prize of <span class="text-[#D4AF37] font-bold">$<?php echo e(number_format($winner->prize_amount, 0)); ?></span> has been claimed successfully!
                </p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_dates']): ?>
                    <p class="text-white/40 text-sm animate-fade-in-up animate-delay-300">
                        Claimed on <?php echo e($winner->claimed_at ? date('F j, Y', strtotime($winner->claimed_at)) : 'N/A'); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_balance_summary'] || $winnerConfig['show_winner_code']): ?>
    
    <div class="max-w-6xl mx-auto px-4 -mt-8 relative z-10">
        <div class="grid md:grid-cols-3 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_balance_summary']): ?>
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">💰</div>
                    <span class="text-xs text-gray-400">Prize Amount</span>
                </div>
                <p class="text-3xl font-bold gold-text-gradient"><?php echo e(number_format($winner->prize_amount, 0)); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner->prize_description): ?>
                    <p class="text-xs text-gray-400 mt-1"><?php echo e($winner->prize_description); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_balance_summary']): ?>
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up animate-delay-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">📊</div>
                    <span class="text-xs text-gray-400">Status</span>
                </div>
                <p class="text-2xl font-bold text-[#1B2A4A] capitalize">
                    <?php
                        $labels = ['new' => 'New', 'review' => 'Under Review', 'docs_needed' => 'Documents Needed', 'processing' => 'Processing', 'approved' => 'Approved', 'delivered' => 'Delivered'];
                        $statusColors = ['new' => 'text-[#D4AF37]', 'review' => 'text-orange-500', 'docs_needed' => 'text-blue-500', 'processing' => 'text-purple-500', 'approved' => 'text-green-500', 'delivered' => 'text-green-600'];
                    ?>
                    <span class="<?php echo e($statusColors[$winner->status] ?? 'text-gray-500'); ?>"><?php echo e($labels[$winner->status] ?? $winner->status); ?></span>
                </p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_winner_code']): ?>
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up animate-delay-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">🔑</div>
                    <span class="text-xs text-gray-400">Winner Code</span>
                </div>
                <p class="text-xl font-bold text-[#1B2A4A] font-mono tracking-wider"><?php echo e($winner->unique_code); ?></p>
                <button onclick="navigator.clipboard.writeText('<?php echo e($winner->unique_code); ?>'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 2000)" class="text-xs text-[#D4AF37] hover:underline mt-1">
                    Copy
                </button>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="max-w-6xl mx-auto px-4 py-10">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_next_steps'] && $winner->next_steps): ?>
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-6 mb-8 animate-fade-in-up">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-xl">📋</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A] text-lg mb-2">Next Steps</h3>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"><?php echo e($winner->next_steps); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_quick_actions']): ?>
        
        <div class="grid md:grid-cols-6 gap-4 mb-10">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_giveaways']): ?>
            <a href="<?php echo e(route('giveaways')); ?>" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🎁</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Enter Giveaways</p>
                <p class="text-xs text-gray-400 mt-1">Win more prizes</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_games']): ?>
            <a href="<?php echo e(route('games')); ?>" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🎡</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Spin & Win</p>
                <p class="text-xs text-gray-400 mt-1">Try your luck!</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_deposits']): ?>
            <a href="<?php echo e(route('winner.deposits')); ?>" class="winner-card-hover bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">📥</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Make a Deposit</p>
                <p class="text-xs text-gray-400 mt-1">Add funds</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_withdrawals']): ?>
            <a href="<?php echo e(route('winner.withdrawals')); ?>" class="winner-card-hover bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">💸</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Withdraw Funds</p>
                <p class="text-xs text-gray-400 mt-1">Cash out winnings</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_shop']): ?>
            <a href="<?php echo e(route('shop')); ?>" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🛍️</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Shop PCH Gear</p>
                <p class="text-xs text-gray-400 mt-1">Exclusive merch</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_memberships']): ?>
            <a href="<?php echo e(route('memberships')); ?>" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">⭐</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Memberships</p>
                <p class="text-xs text-gray-400 mt-1">VIP benefits</p>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_messages'] || $winnerConfig['show_documents']): ?>
        
        <div class="grid md:grid-cols-2 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_messages']): ?>
            <div id="messages" class="bg-white rounded-2xl gold-border overflow-hidden animate-fade-in-up winner-card-hover">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">💬</div>
                        <div>
                            <h3 class="font-bold text-[#1B2A4A]">Messages</h3>
                            <p class="text-xs text-gray-400">Chat with admin</p>
                        </div>
                    </div>
                    <span class="text-xs bg-[#D4AF37]/10 text-[#D4AF37] px-3 py-1 rounded-full font-semibold"><?php echo e($winner->messages->count()); ?></span>
                </div>
                <div class="p-5 max-h-72 overflow-y-auto border-b border-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $winner->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full <?php echo e($message->sent_by_admin ? 'bg-[#D4AF37]/20 text-[#D4AF37]' : 'bg-gray-100 text-gray-500'); ?> flex items-center justify-center text-xs font-bold">
                                        <?php echo e($message->sent_by_admin ? 'PCH' : 'W'); ?>

                                    </span>
                                    <span class="text-xs font-medium <?php echo e($message->sent_by_admin ? 'text-[#D4AF37]' : 'text-gray-500'); ?>"><?php echo e($message->sent_by_admin ? 'PCH Admin' : 'You'); ?></span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_dates']): ?>
                                    <span class="text-[10px] text-gray-400"><?php echo e($message->created_at->diffForHumans()); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message->subject): ?>
                                <p class="text-sm font-semibold text-[#1B2A4A] mb-1"><?php echo e($message->subject); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <p class="text-sm text-gray-600"><?php echo e($message->content); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$message->read && !$message->sent_by_admin): ?>
                                <form action="<?php echo e(route('winner.messages.read', $message)); ?>" method="POST" class="mt-2">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-xs text-[#D4AF37] font-medium bg-[#D4AF37]/5 px-3 py-1 rounded-lg hover:bg-[#D4AF37]/10 transition-colors">Mark as read</button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📬</div>
                            <p class="text-sm text-gray-400">No messages yet</p>
                            <p class="text-xs text-gray-300 mt-1">Send a message to admin below</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <div class="p-5 bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <form action="<?php echo e(route('winner.messages.send')); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="subject" placeholder="Subject" required maxlength="255"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                        <textarea name="content" rows="2" placeholder="Type your message..." required
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all resize-none"></textarea>
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-xl text-sm font-bold hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                            📤 Send Message
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_documents']): ?>
            <div id="documents" class="bg-white rounded-2xl gold-border overflow-hidden animate-fade-in-up animate-delay-100 winner-card-hover">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📄</div>
                        <div>
                            <h3 class="font-bold text-[#1B2A4A]">Documents</h3>
                            <p class="text-xs text-gray-400">Upload required documents</p>
                        </div>
                    </div>
                    <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-semibold"><?php echo e($winner->documents->count()); ?></span>
                </div>
                <div class="p-5 max-h-60 overflow-y-auto border-b border-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $winner->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-sm flex-shrink-0">
                                    <?php $icons = ['requested' => '📋', 'submitted' => '🕐', 'verified' => '✅', 'rejected' => '❌']; ?>
                                    <?php echo e($icons[$doc->status] ?? '📄'); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#1B2A4A]"><?php echo e($doc->custom_type ?? ucfirst(str_replace('_', ' ', $doc->document_type))); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winnerConfig['show_dates'] && $doc->submitted_at): ?>
                                        <p class="text-xs text-green-600">Submitted <?php echo e($doc->submitted_at->diffForHumans()); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($doc->file_name): ?>
                                        <p class="text-xs text-gray-400 truncate max-w-[180px]"><?php echo e($doc->file_name); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <?php
                                $statusColors = ['requested' => 'bg-gray-100 text-gray-600', 'submitted' => 'bg-yellow-100 text-yellow-700', 'verified' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                            ?>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0 <?php echo e($statusColors[$doc->status] ?? 'bg-gray-100'); ?>">
                                <?php echo e(ucfirst($doc->status)); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📋</div>
                            <p class="text-sm text-gray-400">No documents yet</p>
                            <p class="text-xs text-gray-300 mt-1">Upload a document below</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <div class="p-5 bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <form action="<?php echo e(route('winner.documents.upload')); ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <select name="document_type" required
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                            <option value="">Select document type...</option>
                            <option value="government_id">Government ID</option>
                            <option value="proof_of_address">Proof of Address</option>
                            <option value="tax_form_w9">Tax Form W-9</option>
                            <option value="bank_information">Bank Information</option>
                            <option value="signed_agreement">Signed Agreement</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx"
                                    class="hidden" onchange="this.nextElementSibling.textContent = this.files[0]?.name || 'Choose file'">
                                <span class="block w-full px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-400 truncate hover:border-[#D4AF37] transition-colors">📎 Choose file (max 10MB)</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-bold hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/20">
                            📤 Upload Document
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</main>
<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/winner/dashboard.blade.php ENDPATH**/ ?>