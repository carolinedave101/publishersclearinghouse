<?php $__env->startSection('title', 'Messages - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-[#1B2A4A]">Messages</h1>
            <button onclick="document.getElementById('composeForm').classList.toggle('hidden')"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-semibold hover:scale-105 transition-transform text-sm">
                + New Message
            </button>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div id="composeForm" class="hidden mb-8 bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
            <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Send a Message to Admin</h2>
            <form method="POST" action="<?php echo e(route('messages.store')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Subject</label>
                    <input type="text" name="subject" required maxlength="255"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Message</label>
                    <textarea name="message" required rows="5"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-[#1B2A4A] to-[#2A3A5A] text-white font-semibold hover:from-[#2A3A5A] hover:to-[#1B2A4A] transition-all">
                    Send Message
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 <?php echo e(!$msg->is_read && $msg->direction === 'admin_to_user' ? 'ring-2 ring-[#D4AF37]/20' : ''); ?>">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full <?php echo e($msg->direction === 'admin_to_user' ? 'bg-[#D4AF37]/20 text-[#D4AF37]' : 'bg-[#1B2A4A]/10 text-[#1B2A4A]'); ?> flex items-center justify-center font-bold text-sm">
                                <?php echo e($msg->direction === 'admin_to_user' ? 'A' : 'U'); ?>

                            </div>
                            <div>
                                <p class="font-semibold text-[#1B2A4A]"><?php echo e($msg->subject); ?></p>
                                <p class="text-xs text-[#1B2A4A]/40">
                                    <?php echo e($msg->direction === 'admin_to_user' ? 'PCH Admin' : 'You'); ?>

                                    &middot; <?php echo e($msg->created_at->format('M j, Y g:i A')); ?>

                                </p>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$msg->is_read && $msg->direction === 'admin_to_user'): ?>
                            <span class="text-xs px-2 py-1 rounded-full bg-[#D4AF37]/10 text-[#D4AF37] font-medium">New</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-[#1B2A4A]/70 text-sm leading-relaxed whitespace-pre-line"><?php echo e($msg->message); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-16">
                    <div class="text-5xl mb-4">💬</div>
                    <h3 class="text-xl font-bold text-[#1B2A4A] mb-2">No Messages Yet</h3>
                    <p class="text-[#1B2A4A]/60">Send a message to admin and they'll get back to you.</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messages->hasPages()): ?>
            <div class="mt-8">
                <?php echo e($messages->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/messages.blade.php ENDPATH**/ ?>