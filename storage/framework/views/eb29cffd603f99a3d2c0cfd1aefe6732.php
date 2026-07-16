<?php $__env->startSection('title', 'Transaction History - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] border-b border-[#D4AF37]/10">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-xs text-white/40 mb-3">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-[#D4AF37] transition-colors">Home</a>
            <span>/</span>
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="hover:text-[#D4AF37] transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-[#D4AF37]">Transaction History</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Dashboard</a>
            <a href="<?php echo e(route('winner.deposits')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Make Deposit</a>
            <a href="<?php echo e(route('winner.withdrawals')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Withdraw Funds</a>
            <a href="<?php echo e(route('winner.transactions')); ?>" class="px-4 py-2 bg-[#D4AF37]/20 text-[#D4AF37] rounded-lg text-sm font-semibold border border-[#D4AF37]/30">Transaction History</a>
        </div>
    </div>
</div>

<main class="flex-1 bg-gradient-to-b from-[#FFFBF0] to-[#F5F0E0]">
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="grid md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl gold-border p-5 text-center">
                <p class="text-xs text-gray-400 mb-1">Prize Amount</p>
                <p class="text-xl font-bold gold-text-gradient">$<?php echo e(number_format($winner->prize_amount, 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-5 text-center">
                <p class="text-xs text-gray-400 mb-1">Total Deposits</p>
                <p class="text-xl font-bold text-green-600">$<?php echo e(number_format($winner->deposits->where('status', 'approved')->sum('amount'), 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-5 text-center">
                <p class="text-xs text-gray-400 mb-1">Total Withdrawn</p>
                <p class="text-xl font-bold text-red-500">$<?php echo e(number_format($winner->withdrawals->whereIn('status', ['approved', 'completed'])->sum('amount'), 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-5 text-center">
                <p class="text-xs text-gray-400 mb-1">Available Balance</p>
                <p class="text-xl font-bold text-green-600">$<?php echo e(number_format($winner->available_balance, 0)); ?></p>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl gold-border overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📊</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A]">Transaction Ledger</h3>
                        <p class="text-xs text-gray-400">Complete history of all financial activity</p>
                    </div>
                </div>
            </div>

            <?php
                $allTransactions = collect();

                $allTransactions = $allTransactions->merge(
                    $winner->deposits->map(fn($d) => [
                        'date' => $d->created_at,
                        'type' => 'Deposit',
                        'method' => $d->paymentMethod?->name ?? 'N/A',
                        'amount' => $d->amount,
                        'fee' => $d->fee,
                        'net' => $d->net_amount,
                        'status' => $d->status,
                        'description' => 'Deposit via ' . ($d->paymentMethod?->name ?? 'N/A'),
                        'icon' => '📥',
                        'color' => $d->status === 'approved' ? 'text-green-600' : ($d->status === 'rejected' ? 'text-red-500' : 'text-yellow-500'),
                    ])
                );

                $allTransactions = $allTransactions->merge(
                    $winner->withdrawals->map(fn($w) => [
                        'date' => $w->created_at,
                        'type' => 'Withdrawal',
                        'method' => $w->paymentMethod?->name ?? 'N/A',
                        'amount' => $w->amount,
                        'fee' => $w->fee,
                        'net' => -$w->net_amount,
                        'status' => $w->status,
                        'description' => 'Withdrawal to ' . ($w->paymentMethod?->name ?? 'N/A'),
                        'icon' => '💸',
                        'color' => $w->status === 'completed' ? 'text-red-500' : ($w->status === 'rejected' ? 'text-gray-400' : 'text-yellow-500'),
                    ])
                );

                $allTransactions = $allTransactions->sortByDesc('date');
            ?>

            <div class="overflow-x-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allTransactions->isNotEmpty()): ?>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Method</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Fee</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Net</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap"><?php echo e($tx['date']->format('M j, Y g:i A')); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="flex items-center gap-2 text-sm">
                                            <span><?php echo e($tx['icon']); ?></span>
                                            <span class="font-medium text-[#1B2A4A]"><?php echo e($tx['type']); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500"><?php echo e($tx['method']); ?></td>
                                    <td class="px-4 py-3 text-sm font-medium <?php echo e($tx['color']); ?>">$<?php echo e(number_format(abs($tx['amount']), 2)); ?></td>
                                    <td class="px-4 py-3 text-xs text-gray-400">$<?php echo e(number_format($tx['fee'], 2)); ?></td>
                                    <td class="px-4 py-3 text-sm font-semibold <?php echo e($tx['net'] >= 0 ? 'text-green-600' : 'text-red-500'); ?>">
                                        <?php echo e($tx['net'] >= 0 ? '+' : ''); ?>$<?php echo e(number_format($tx['net'], 2)); ?>

                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                            <?php switch($tx['status']):
                                                case ('pending'): ?> bg-yellow-100 text-yellow-700 <?php break; ?>
                                                <?php case ('approved'): ?> bg-blue-100 text-blue-700 <?php break; ?>
                                                <?php case ('completed'): ?> bg-green-100 text-green-700 <?php break; ?>
                                                <?php case ('rejected'): ?> bg-red-100 text-red-700 <?php break; ?>
                                                <?php default: ?> bg-gray-100 text-gray-600
                                            <?php endswitch; ?>
                                        "><?php echo e(ucfirst($tx['status'])); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">📊</div>
                        <p class="text-sm text-gray-400">No transactions yet</p>
                        <p class="text-xs text-gray-300 mt-1">Your financial activity will appear here</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/winner/transactions.blade.php ENDPATH**/ ?>