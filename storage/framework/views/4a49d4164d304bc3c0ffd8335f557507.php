<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>
body { font-family: Georgia, serif; margin: 0; padding: 0; background: #f5f0e8; }
.container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
.header { text-align: center; padding: 30px; background: linear-gradient(135deg, #1B2A4A 0%, #2a3f6a 100%); border-radius: 12px 12px 0 0; }
.header h1 { color: #D4AF37; margin: 0; font-size: 24px; }
.content { background: #fff; padding: 40px 30px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.footer-text { text-align: center; padding: 30px; color: #999; font-size: 12px; }
</style></head>
<body>
<div class="container">
    <div class="header"><h1>Document Uploaded by Winner</h1></div>
    <div class="content">
        <p>A winner has uploaded a new document:</p>
        <p><strong>Winner:</strong> <?php echo e($winner->first_name); ?> <?php echo e($winner->last_name); ?> (<?php echo e($winner->email); ?>)</p>
        <p><strong>Document Type:</strong> <?php echo e(ucfirst($document->document_type)); ?></p>
        <p><strong>File:</strong> <?php echo e($document->file_name); ?></p>
        <p><strong>Size:</strong> <?php echo e(round($document->file_size / 1024, 1)); ?> KB</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($document->custom_type): ?>
            <p><strong>Custom Type:</strong> <?php echo e($document->custom_type); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <p style="text-align:center;margin-top:30px;">
            <a href="<?php echo e(config('app.url')); ?>/admin/documents/<?php echo e($document->id); ?>/edit" style="display:inline-block;padding:12px 30px;background:#1B2A4A;color:#fff;text-decoration:none;border-radius:50px;font-weight:bold;">View in Admin</a>
        </p>
    </div>
    <div class="footer-text">&copy; <?php echo e(date('Y')); ?> Publishers Clearing House</div>
</div>
</body>
</html>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/admin-document-uploaded.blade.php ENDPATH**/ ?>