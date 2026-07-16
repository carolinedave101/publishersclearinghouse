<?php $__env->startSection('title', 'Make a Deposit - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] border-b border-[#D4AF37]/10">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-xs text-white/40 mb-3">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-[#D4AF37] transition-colors">Home</a>
            <span>/</span>
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="hover:text-[#D4AF37] transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-[#D4AF37]">Make a Deposit</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('winner.dashboard')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Dashboard</a>
            <a href="<?php echo e(route('winner.deposits')); ?>" class="px-4 py-2 bg-[#D4AF37]/20 text-[#D4AF37] rounded-lg text-sm font-semibold border border-[#D4AF37]/30">Make Deposit</a>
            <a href="<?php echo e(route('winner.withdrawals')); ?>" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">Withdraw Funds</a>
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
                <div class="text-3xl mb-2">📥</div>
                <p class="text-xs text-gray-400 mb-1">Total Deposits</p>
                <p class="text-2xl font-bold text-green-600">$<?php echo e(number_format($winner->deposits->where('status', 'approved')->sum('amount'), 0)); ?></p>
            </div>
            <div class="bg-white rounded-2xl gold-border p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="text-xs text-gray-400 mb-1">Available Balance</p>
                <p class="text-2xl font-bold text-green-600">$<?php echo e(number_format($winner->available_balance, 0)); ?></p>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl gold-border p-6 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📥</div>
                <div>
                    <h3 class="font-bold text-[#1B2A4A]">Make a Deposit</h3>
                    <p class="text-xs text-gray-400">Deposit funds to your winners account</p>
                </div>
            </div>

            <form action="<?php echo e(route('winner.deposits.submit')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="depositForm()">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Payment Method</label>
                    <select name="payment_method_id" x-model="methodId" @change="loadMethodDetails()" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                        <option value="">Choose a deposit method...</option>
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

                <div x-show="methodId && methodInstructions" x-cloak>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
                        <p class="font-semibold mb-1">📌 Payment Instructions</p>
                        <div x-html="methodInstructions" class="text-blue-600 text-xs leading-relaxed prose prose-sm max-w-none"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount ($)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">$</span>
                        <input type="number" name="amount" min="1" step="0.01" required
                            placeholder="0.00"
                            class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                    </div>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Proof of Payment</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-[#D4AF37]/50 transition-colors">
                        <input type="file" name="proof_file" id="proof_file" required accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx"
                            class="hidden" onchange="handleFileSelect(event)">
                        <div id="upload-placeholder">
                            <div class="text-4xl mb-2">📎</div>
                            <p class="text-sm text-gray-500 mb-1">Drop your payment proof here or click to browse</p>
                            <p class="text-xs text-gray-400">Accepted: PDF, JPG, PNG, DOC (max 10MB)</p>
                        </div>
                        <div id="file-preview" class="hidden">
                            <div class="text-4xl mb-2">📄</div>
                            <p id="file-name" class="text-sm font-semibold text-[#1B2A4A]"></p>
                            <p id="file-size" class="text-xs text-gray-400"></p>
                            <button type="button" onclick="resetFileInput()" class="mt-2 text-xs text-red-500 hover:underline">Remove file</button>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['proof_file'];
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="Any additional information about this deposit..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all resize-none"></textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-sm font-bold hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-500/20">
                    Submit Deposit
                </button>
            </form>
        </div>

        
        <div class="bg-white rounded-2xl gold-border overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📜</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A]">Deposit History</h3>
                        <p class="text-xs text-gray-400">Your past deposits</p>
                    </div>
                </div>
                <span class="text-xs bg-[#D4AF37]/10 text-[#D4AF37] px-3 py-1 rounded-full font-semibold"><?php echo e($winner->deposits->count()); ?></span>
            </div>
            <div class="overflow-x-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $winner->deposits->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deposit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <?php
                                    $statusIcons = ['pending' => '🕐', 'approved' => '✅', 'rejected' => '❌'];
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                                ?>
                                <span><?php echo e($statusIcons[$deposit->status] ?? '📄'); ?></span>
                                <div>
                                    <p class="text-sm font-semibold text-[#1B2A4A]">$<?php echo e(number_format($deposit->amount, 2)); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo e($deposit->paymentMethod?->name ?? 'Unknown'); ?></p>
                                </div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium <?php echo e($statusColors[$deposit->status] ?? 'bg-gray-100'); ?>">
                                <?php echo e(ucfirst($deposit->status)); ?>

                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] text-gray-400"><?php echo e($deposit->created_at->format('M j, Y g:i A')); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($deposit->admin_notes): ?>
                                <p class="text-[10px] text-gray-400 italic truncate max-w-[200px]">Note: <?php echo e(strip_tags($deposit->admin_notes)); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">📥</div>
                        <p class="text-sm text-gray-400">No deposits yet</p>
                        <p class="text-xs text-gray-300 mt-1">Use the form above to make your first deposit</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
function depositForm() {
    return {
        methodId: '',
        methodInstructions: '',
        methods: <?php echo json_encode($paymentMethods->mapWithKeys(fn($m) => [$m->id => ['instructions' => $m->instructions]]), 15, 512) ?>,
        loadMethodDetails() {
            const method = this.methods[this.methodId];
            this.methodInstructions = method ? (method.instructions || '') : '';
        }
    }
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    document.getElementById('upload-placeholder').classList.add('hidden');
    document.getElementById('file-preview').classList.remove('hidden');
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
}

function resetFileInput() {
    document.getElementById('proof_file').value = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('file-preview').classList.add('hidden');
}
</script>

<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/winner/deposits.blade.php ENDPATH**/ ?>