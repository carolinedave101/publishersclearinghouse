<?php $__env->startSection('title', 'Withdraw Funds - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] border-b border-[#D4AF37]/10">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-xs text-white/40 mb-3">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-[#D4AF37] transition-colors">Home</a>
            <span>/</span>
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="hover:text-[#D4AF37] transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-[#D4AF37]">Withdraw Funds</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Dashboard</a>
            <a href="<?php echo e(route('winner.deposits')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Make Deposit</a>
            <a href="<?php echo e(route('winner.withdrawals')); ?>" class="px-4 py-2 bg-[#D4AF37]/20 text-[#D4AF37] rounded-lg text-sm font-semibold border border-[#D4AF37]/30">Withdraw Funds</a>
            <a href="<?php echo e(route('winner.transactions')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Transaction History</a>
        </div>
    </div>
</div>

<main class="flex-1 bg-gradient-to-b from-[#FFFBF0] to-[#F5F0E0]">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="max-w-4xl mx-auto px-4 pt-6">
            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2 shadow-sm">
                <span>✅</span> <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="max-w-4xl mx-auto px-4 pt-6">
            <div class="p-4 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2 shadow-sm">
                <span>❌</span> <?php echo e($errors->first()); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl gold-border p-6 text-center">
                <div class="text-3xl mb-2">💰</div>
                <p class="text-xs text-gray-400 mb-1">Prize Amount</p>
                <p class="text-2xl font-bold gold-text-gradient">$<?php echo e(number_format($winner->prize_amount, 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-6 text-center">
                <div class="text-3xl mb-2">💳</div>
                <p class="text-xs text-gray-400 mb-1">Total Withdrawn</p>
                <p class="text-2xl font-bold text-red-500">$<?php echo e(number_format($winner->withdrawals->whereIn('status', ['approved', 'completed'])->sum('amount'), 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="text-xs text-gray-400 mb-1">Available Balance</p>
                <p class="text-2xl font-bold text-green-600">$<?php echo e(number_format($winner->available_balance, 0)); ?></p>
            </div>
        </div>

        
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-xl">📋</div>
                <div>
                    <h3 class="font-bold text-[#1B2A4A] text-lg mb-3">Steps to Withdraw Your Winnings</h3>
                    <ol class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                            <div>
                                <span class="font-semibold text-[#1B2A4A]">Claim Your Prize</span>
                                <p class="text-gray-500">Click "Claim Your Prize" on your dashboard to start the process.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                            <div>
                                <span class="font-semibold text-[#1B2A4A]">Submit Required Documents</span>
                                <p class="text-gray-500">Upload a valid Government ID, Proof of Address, and completed Tax Form W-9.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                            <div>
                                <span class="font-semibold text-[#1B2A4A]">Choose Payment Method</span>
                                <p class="text-gray-500">Select your preferred withdrawal method from the options below.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">4</span>
                            <div>
                                <span class="font-semibold text-[#1B2A4A]">Enter Amount & Account Details</span>
                                <p class="text-gray-500">Specify the amount to withdraw and provide your account/payment details.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">5</span>
                            <div>
                                <span class="font-semibold text-[#1B2A4A]">Wait for Processing</span>
                                <p class="text-gray-500">Our team will review and process your withdrawal. You'll receive an email once completed.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($winner->available_balance > 0): ?>
            <div class="bg-white rounded-2xl gold-border p-6 mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">💸</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A]">Request Withdrawal</h3>
                        <p class="text-xs text-gray-400">Available balance: $<?php echo e(number_format($winner->available_balance, 0)); ?></p>
                    </div>
                </div>

                <form action="<?php echo e(route('winner.withdrawals.request')); ?>" method="POST" class="space-y-4" x-data="withdrawalForm()">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Payment Method</label>
                        <select name="payment_method_id" x-model="methodId" @change="loadMethodDetails()" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                            <option value="">Choose a withdrawal method...</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($method->id); ?>" data-instructions="<?php echo e($method->instructions); ?>"><?php echo e($method->name); ?> - <?php echo e(ucfirst($method->type)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payment_method_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div x-show="methodId" x-cloak>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-sm text-blue-700">
                            <p class="font-semibold mb-1">📌 Payment Instructions</p>
                            <div x-html="methodInstructions" class="text-blue-600 text-xs leading-relaxed prose prose-sm max-w-none"></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Withdrawal Amount ($)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">$</span>
                            <input type="number" name="amount" min="1" max="<?php echo e($winner->available_balance); ?>" step="0.01" required
                                placeholder="0.00"
                                class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Max: $<?php echo e(number_format($winner->available_balance, 2)); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div x-show="methodId" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account / Payment Details</label>
                        <p class="text-xs text-gray-400 mb-2">Provide the details needed to send your payment (bank account, PayPal email, wallet address, etc.)</p>
                        <div id="account-details" class="space-y-3">
                            <template x-if="methodType === 'bank'">
                                <div class="space-y-3">
                                    <input type="text" x-model="account.bank_name" placeholder="Bank Name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                    <input type="text" x-model="account.account_name" placeholder="Account Holder Name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                    <input type="text" x-model="account.account_number" placeholder="Account Number" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                    <input type="text" x-model="account.routing_number" placeholder="Routing Number" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                </div>
                            </template>
                            <template x-if="methodType === 'paypal'">
                                <input type="email" x-model="account.paypal_email" placeholder="PayPal Email Address" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                            </template>
                            <template x-if="methodType === 'crypto'">
                                <div class="space-y-3">
                                    <input type="text" x-model="account.wallet_address" placeholder="Wallet Address" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                    <input type="text" x-model="account.network" placeholder="Network (BTC, ETH, USDT, etc.)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                </div>
                            </template>
                            <template x-if="methodType === 'card'">
                                <div class="space-y-3">
                                    <input type="text" x-model="account.card_number" placeholder="Card Number" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" x-model="account.expiry" placeholder="MM/YY" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                                        <input type="text" x-model="account.cvv" placeholder="CVV" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF137]/20 outline-none text-sm transition-all">
                                    </div>
                                </div>
                            </template>
                            <template x-if="methodType === 'offline'">
                                <textarea x-model="account.details" rows="3" placeholder="Enter your payment details..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all resize-none"></textarea>
                            </template>
                        </div>
                        <input type="hidden" name="account_details" x-bind:value="JSON.stringify(account)">
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-xl text-sm font-bold hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                        Submit Withdrawal Request
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl gold-border p-8 text-center mb-8">
                <div class="text-5xl mb-4">🔒</div>
                <h3 class="font-bold text-[#1B2A4A] text-lg mb-2">No Available Balance</h3>
                <p class="text-sm text-gray-400">Your available balance is $0.00. Please claim your prize and complete the required steps to access your winnings.</p>
                <a href="<?php echo e(route('winner.dashboard')); ?>" class="inline-block mt-4 px-6 py-2.5 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-xl text-sm font-bold">Go to Dashboard</a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-white rounded-2xl gold-border overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📜</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A]">Withdrawal History</h3>
                        <p class="text-xs text-gray-400">Your past withdrawal requests</p>
                    </div>
                </div>
                <span class="text-xs bg-[#D4AF37]/10 text-[#D4AF37] px-3 py-1 rounded-full font-semibold"><?php echo e($winner->withdrawals->count()); ?></span>
            </div>
            <div class="overflow-x-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $winner->withdrawals->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <?php
                                    $statusIcons = ['pending' => '🕐', 'approved' => '✅', 'completed' => '🎉', 'rejected' => '❌'];
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                                ?>
                                <span><?php echo e($statusIcons[$withdrawal->status] ?? '📄'); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-[#1B2A4A]">$<?php echo e(number_format($withdrawal->amount, 2)); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo e($withdrawal->paymentMethod?->name ?? 'Unknown'); ?></p>
                                </div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium <?php echo e($statusColors[$withdrawal->status] ?? 'bg-gray-100'); ?>">
                                <?php echo e(ucfirst($withdrawal->status)); ?>

                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] text-gray-400"><?php echo e($withdrawal->created_at->format('M j, Y g:i A')); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($withdrawal->admin_notes): ?>
                                <p class="text-[10px] text-gray-400 italic truncate max-w-[200px]">Note: <?php echo e(strip_tags($withdrawal->admin_notes)); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">💸</div>
                        <p class="text-sm text-gray-400">No withdrawal requests yet</p>
                        <p class="text-xs text-gray-300 mt-1">Use the form above to request your first withdrawal</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
function withdrawalForm() {
    return {
        methodId: '',
        methodType: '',
        methodInstructions: '',
        account: {},
        methods: <?php echo json_encode($paymentMethods->mapWithKeys(fn($m) => [$m->id => ['type' => $m->type, 'instructions' => $m->instructions]]), 512) ?>,
        loadMethodDetails() {
            const method = this.methods[this.methodId];
            if (method) {
                this.methodType = method.type;
                this.methodInstructions = method.instructions || 'Follow the instructions provided by admin.';
                this.account = {};
            }
        }
    }
}
</script>

<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/winner/withdrawals.blade.php ENDPATH**/ ?>