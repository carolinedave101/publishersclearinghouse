<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publishers Clearing House</title>
</head>
<body style="margin:0;padding:0;background:#0B1424;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0B1424;padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#1B2A4A,#0B1424);padding:28px;text-align:center;">
                            <div style="display:inline-block;width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#D4AF37,#B8960F);color:#1B2A4A;font-size:30px;font-weight:bold;line-height:64px;">P</div>
                            <h1 style="color:#D4AF37;font-size:22px;margin:14px 0 0;">Publishers Clearing House</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 40px;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recipientName): ?>
                                <p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Hello <?php echo e($recipientName); ?>,</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h2 style="font-size:20px;color:#1B2A4A;margin:0 0 16px;"><?php echo e($subjectLine); ?></h2>
                            <p style="font-size:15px;color:#374151;line-height:1.7;margin:0 0 24px;"><?php echo nl2br(e($messageBody)); ?></p>
                            <p style="margin:0 0 28px;">
                                <a href="<?php echo e($appUrl); ?>" style="display:inline-block;background:linear-gradient(135deg,#D4AF37,#B8960F);color:#1B2A4A;text-decoration:none;font-weight:bold;padding:12px 28px;border-radius:10px;">Visit PCH Portal</a>
                            </p>
                            <p style="font-size:13px;color:#94A3B8;margin:0;">This message was sent from the Publishers Clearing House admin team.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#0B1424;padding:20px;text-align:center;color:#64748B;font-size:12px;">
                            © <?php echo e(date('Y')); ?> Publishers Clearing House. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/admin-message.blade.php ENDPATH**/ ?>