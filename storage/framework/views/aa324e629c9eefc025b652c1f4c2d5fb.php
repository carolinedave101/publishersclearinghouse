<div class="divide-y divide-gray-200">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2 text-sm">
            <div class="flex-1 min-w-0">
                <span class="text-gray-900 font-medium"><?php echo e($item['name'] ?? 'Item'); ?></span>
                <span class="text-gray-500 ml-2">&times; <?php echo e($item['quantity'] ?? 1); ?></span>
            </div>
            <div class="text-right flex-shrink-0 ml-4">
                <span class="text-gray-900">$<?php echo e(number_format($item['price'] ?? 0, 2)); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($item['line_total'])): ?>
                    <span class="text-gray-400 ml-2">($<?php echo e(number_format($item['line_total'], 2)); ?>)</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-gray-400 py-2">No items</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/filament/forms/shop-order-items.blade.php ENDPATH**/ ?>