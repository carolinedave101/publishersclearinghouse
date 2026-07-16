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
    <div class="header"><h1>New User Message</h1></div>
    <div class="content">
        <p>A user has sent a new message:</p>
        <p><strong>From:</strong> <?php echo e($message->user?->name ?? 'Unknown User'); ?> (<?php echo e($message->user?->email ?? 'N/A'); ?>)</p>
        <p><strong>Subject:</strong> <?php echo e($message->subject); ?></p>
        <p><strong>Message:</strong></p>
        <blockquote style="background:#f8f4ec;padding:15px;border-radius:8px;border-left:4px solid #D4AF37;margin:10px 0;">
            <?php echo e($message->message); ?>

        </blockquote>
        <p style="text-align:center;margin-top:30px;">
            <a href="<?php echo e(config('app.url')); ?>/admin/user-messages" style="display:inline-block;padding:12px 30px;background:#1B2A4A;color:#fff;text-decoration:none;border-radius:50px;font-weight:bold;">View in Admin</a>
        </p>
    </div>
    <div class="footer-text">&copy; <?php echo e(date('Y')); ?> Publishers Clearing House</div>
</div>
</body>
</html>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/admin-user-message.blade.php ENDPATH**/ ?>