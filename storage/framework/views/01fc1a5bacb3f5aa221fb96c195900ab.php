<?php $__env->startSection('title', 'Create Account - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-[#D4AF37]/20">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-[#1B2A4A]">Create Account</h1>
                <p class="text-[#1B2A4A]/60 mt-2">Join PCH Winners Portal</p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    <p class="text-xs text-[#1B2A4A]/40 mt-1">At least 8 characters</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-semibold hover:from-[#B8960F] hover:to-[#D4AF37] transition-all shadow-lg shadow-[#D4AF37]/20">
                    Create Account
                </button>
            </form>

            <p class="text-center text-sm text-[#1B2A4A]/60 mt-6">
                Already have an account?
                <a href="<?php echo e(route('login')); ?>" class="text-[#D4AF37] font-semibold hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>
<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/auth/register.blade.php ENDPATH**/ ?>