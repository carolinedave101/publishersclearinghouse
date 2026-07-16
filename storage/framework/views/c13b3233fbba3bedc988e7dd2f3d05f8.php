<?php $__env->startSection('title', 'My Dashboard - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1B2A4A]">Welcome, <?php echo e(auth()->user()->name); ?>!</h1>
                <p class="text-[#1B2A4A]/60 mt-1">Here's your activity overview</p>
            </div>
            <a href="<?php echo e(route('profile')); ?>"
                class="px-4 py-2 rounded-xl bg-white border border-[#D4AF37]/20 text-[#1B2A4A] font-medium hover:bg-[#D4AF37]/5 transition-colors text-sm">
                Edit Profile
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-[#1B2A4A]"><?php echo e($spinStats['total']); ?></div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Total Spins</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-[#D4AF37]"><?php echo e($spinStats['wins']); ?></div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Prizes Won</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-red-500"><?php echo e($spinStats['jackpots']); ?></div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Jackpots Hit</div>
            </div>
            <a href="<?php echo e(route('messages')); ?>" class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center hover:border-[#D4AF37]/30 transition-colors relative">
                <div class="text-3xl font-bold text-[#1B2A4A]"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadMessages > 0): ?><span class="text-[#D4AF37]"><?php echo e($unreadMessages); ?></span><?php else: ?> 0 <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Unread Messages</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadMessages > 0): ?>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Recent Spins</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentSpins->isEmpty()): ?>
                        <p class="text-[#1B2A4A]/40 text-center py-8">No spins yet. <a href="<?php echo e(route('games')); ?>" class="text-[#D4AF37] hover:underline">Play now!</a></p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentSpins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: <?php echo e($spin->segment?->color ?? '#888'); ?>"></span>
                                        <div>
                                            <p class="text-sm font-medium text-[#1B2A4A]"><?php echo e($spin->prize_label); ?></p>
                                            <p class="text-xs text-[#1B2A4A]/40"><?php echo e($spin->created_at->diffForHumans()); ?></p>
                                        </div>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spin->prize_value > 0 && $spin->prize_type !== 'nothing'): ?>
                                        <span class="text-sm font-semibold text-[#D4AF37]">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spin->prize_type === 'cash'): ?>
                                                $<?php echo e(number_format($spin->prize_value, 0)); ?>

                                            <?php else: ?>
                                                <?php echo e(number_format($spin->prize_value)); ?> <?php echo e($spin->prize_type); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Your Giveaways</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entries->isEmpty()): ?>
                        <p class="text-[#1B2A4A]/40 text-center py-8">No entries yet. <a href="<?php echo e(route('giveaways')); ?>" class="text-[#D4AF37] hover:underline">Browse giveaways!</a></p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-[#1B2A4A]"><?php echo e($entry->giveaway?->title ?? 'Giveaway'); ?></p>
                                        <p class="text-xs text-[#1B2A4A]/40"><?php echo e($entry->created_at->diffForHumans()); ?></p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-[#D4AF37]/10 text-[#D4AF37] font-medium">Entered</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Recent Orders</h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderStats['total'] > 0): ?>
                            <a href="<?php echo e(route('orders')); ?>" class="text-sm text-[#D4AF37] hover:underline">View all</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orders->isEmpty()): ?>
                        <p class="text-[#1B2A4A]/40 text-center py-8">No orders yet. <a href="<?php echo e(route('shop')); ?>" class="text-[#D4AF37] hover:underline">Visit the shop!</a></p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-[#1B2A4A]"><?php echo e(Str::limit($order->items[0]['name'] ?? 'Order', 40)); ?></p>
                                        <p class="text-xs text-[#1B2A4A]/40"><?php echo e($order->created_at->format('M j, Y')); ?> &middot; <?php echo e(count($order->items ?? [])); ?> item(s)</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-[#1B2A4A]">$<?php echo e(number_format($order->total, 2)); ?></p>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                            <?php if($order->status === 'completed'): ?> bg-green-50 text-green-600
                                            <?php elseif($order->status === 'shipped'): ?> bg-blue-50 text-blue-600
                                            <?php elseif($order->status === 'cancelled'): ?> bg-red-50 text-red-600
                                            <?php else: ?> bg-yellow-50 text-yellow-600 <?php endif; ?>">
                                            <?php echo e(ucfirst($order->status)); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Messages</h2>
                        <a href="<?php echo e(route('messages')); ?>" class="text-sm text-[#D4AF37] hover:underline">View all</a>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentMessages->isEmpty()): ?>
                        <p class="text-[#1B2A4A]/40 text-center py-4">No messages yet.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-3 rounded-xl <?php echo e($msg->direction === 'admin_to_user' ? 'bg-[#1B2A4A]/5 border border-[#1B2A4A]/10' : 'bg-gray-50 border border-gray-100'); ?>">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($msg->direction === 'admin_to_user' ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'bg-gray-200 text-gray-600'); ?>">
                                            <?php echo e($msg->direction === 'admin_to_user' ? 'Admin' : 'You'); ?>

                                        </span>
                                        <span class="text-xs text-[#1B2A4A]/40"><?php echo e($msg->created_at->diffForHumans()); ?></span>
                                    </div>
                                    <p class="text-sm font-medium text-[#1B2A4A]"><?php echo e($msg->subject); ?></p>
                                    <p class="text-xs text-[#1B2A4A]/60 mt-1 line-clamp-2"><?php echo e($msg->message); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Winnings</h2>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalWon > 0): ?>
                        <div class="mb-3 p-4 rounded-xl bg-gradient-to-r from-[#D4AF37]/10 to-yellow-50 border border-[#D4AF37]/20 text-center">
                            <p class="text-sm text-[#1B2A4A]/60">Total Cash Won from Spins</p>
                            <p class="text-2xl font-bold text-[#D4AF37]">$<?php echo e(number_format($totalWon, 0)); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner): ?>
                        <div class="p-4 rounded-xl bg-green-50 border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg">🏆</span>
                                <span class="text-sm font-bold text-green-800">Prize Winner</span>
                            </div>
                            <p class="text-sm font-medium text-green-700"><?php echo e($winner->prize_description ?? 'Congratulations!'); ?></p>
                            <p class="text-sm text-green-600 font-semibold mt-1">$<?php echo e(number_format($winner->prize_amount, 2)); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner->next_steps): ?>
                                <p class="text-xs text-green-600 mt-2"><?php echo e($winner->next_steps); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="flex gap-2 mt-3">
                                <a href="<?php echo e(route('winner.dashboard')); ?>" class="text-xs px-3 py-1.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition-colors">
                                    Winner Dashboard
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-[#1B2A4A]/40 text-center py-4">No winnings tied to your account yet.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Membership</h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$subscription): ?>
                            <a href="<?php echo e(route('memberships')); ?>" class="text-sm text-[#D4AF37] hover:underline">View plans</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription): ?>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-bold text-[#1B2A4A]"><?php echo e($subscription->tier?->name ?? 'Active'); ?> Member</p>
                                <p class="text-xs text-[#1B2A4A]/40">Since <?php echo e($subscription->created_at->format('M Y')); ?></p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-600 font-medium">
                                <?php echo e(ucfirst($subscription->status)); ?>

                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->ends_at): ?>
                            <p class="text-xs text-[#1B2A4A]/60 mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->ends_at->isFuture()): ?>
                                    Renews <?php echo e($subscription->ends_at->format('M j, Y')); ?>

                                <?php else: ?>
                                    Expired <?php echo e($subscription->ends_at->format('M j, Y')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->tier && is_array($subscription->tier->features)): ?>
                            <div class="space-y-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subscription->tier->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center gap-2 text-xs text-[#1B2A4A]/70">
                                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <?php echo e($feature); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <p class="text-[#1B2A4A]/40 text-center py-4">No membership yet. Check out our plans for exclusive benefits!</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-3">Quick Stats</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]"><?php echo e($entries->count()); ?></p>
                            <p class="text-xs text-[#1B2A4A]/60">Giveaway Entries</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]"><?php echo e($orderStats['total']); ?></p>
                            <p class="text-xs text-[#1B2A4A]/60">Orders Placed</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#D4AF37]">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderStats['spent'] > 0): ?>
                                    $<?php echo e(number_format($orderStats['spent'], 0)); ?>

                                <?php else: ?>
                                    $0
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="text-xs text-[#1B2A4A]/60">Total Spent</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]">
                                <?php echo e($subscription ? ucfirst($subscription->tier?->name ?? 'Active') : 'None'); ?>

                            </p>
                            <p class="text-xs text-[#1B2A4A]/60">Membership</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/dashboard.blade.php ENDPATH**/ ?>