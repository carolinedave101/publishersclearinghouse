<?php $__env->startComponent('emails.layout', ['subject' => "New Order #{$order->id} - {$order->customer_name}"]); ?>
<h2 style="color:#D4AF37;margin:0 0 4px;">New Order Received</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A new order has been placed in the shop.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Customer</td><td><?php echo e($order->customer_name); ?> (<?php echo e($order->customer_email); ?>)</td></tr>
    <tr><td>Order #</td><td><?php echo e($order->id); ?></td></tr>
    <tr><td>Total</td><td>$<?php echo e(number_format($order->total, 2)); ?></td></tr>
    <tr><td>Shipping To</td><td><?php echo e($order->address); ?>, <?php echo e($order->city); ?>, <?php echo e($order->state); ?> <?php echo e($order->zip); ?></td></tr>
    <tr><td>Status</td><td><?php echo e(ucfirst($order->status)); ?></td></tr>
</table>

<div class="btn-center">
    <a href="<?php echo e(config('app.url')); ?>/admin/shop-orders/<?php echo e($order->id); ?>/edit" class="btn btn-secondary">View in Admin</a>
</div>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/admin-order-notification.blade.php ENDPATH**/ ?>