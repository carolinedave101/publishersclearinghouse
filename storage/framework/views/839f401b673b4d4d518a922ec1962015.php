<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark light">
    <meta name="supported-color-schemes" content="dark light">
    <title><?php echo e($subject ?? 'Publishers Clearing House'); ?></title>
    <style>
        body{margin:0;padding:0;background-color:#0B1424;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif}
        .email-wrapper{width:100%;table-layout:fixed;background-color:#0B1424;padding:24px 0}
        .email-container{max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.15)}
        .email-header{background:linear-gradient(135deg,#1B2A4A 0%,#0F1D35 100%);padding:32px 40px;text-align:center}
        .email-header .logo-circle{display:inline-block;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#D4AF37 0%,#B8960F 100%);color:#1B2A4A;font-size:26px;font-weight:800;line-height:56px;text-align:center;margin-bottom:12px}
        .email-header h1{margin:0;color:#D4AF37;font-size:22px;font-weight:700;letter-spacing:0.5px}
        .email-header .tagline{margin:6px 0 0;color:#8899B4;font-size:13px}
        .email-body{padding:36px 40px}
        .email-body p{margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151}
        .email-body p:last-child{margin-bottom:0}
        .email-body h2{margin:0 0 16px;font-size:20px;color:#1B2A4A;font-weight:700}
        .btn{display:inline-block;padding:13px 32px;border-radius:10px;font-weight:700;font-size:15px;text-decoration:none;text-align:center}
        .btn-primary{background:linear-gradient(135deg,#D4AF37 0%,#C5A030 100%);color:#1B2A4A}
        .btn-secondary{background:#1B2A4A;color:#ffffff}
        .btn-center{text-align:center;margin:24px 0}
        .info-box{background:#F8FAFC;border:1px solid #E5E7EB;border-radius:12px;padding:20px;margin:0 0 20px}
        .info-box .label{font-size:12px;color:#6B7280;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px}
        .info-box .value{font-size:16px;color:#1B2A4A;font-weight:600;margin:0}
        .info-table{width:100%;border-collapse:collapse;margin:0 0 16px}
        .info-table td{padding:8px 0;font-size:14px;color:#374151;border-bottom:1px solid #F3F4F6}
        .info-table td:last-child{text-align:right;font-weight:600;color:#1B2A4A}
        .highlight-box{background:linear-gradient(135deg,#FEF9E7 0%,#FDF2D0 100%);border:1px solid #FCD34D;border-radius:12px;padding:24px;text-align:center;margin:0 0 20px}
        .highlight-box .amount{font-size:36px;font-weight:800;color:#1B2A4A;margin:0;line-height:1.2}
        .highlight-box .desc{font-size:14px;color:#6B7280;margin:4px 0 0}
        .divider{border:none;border-top:1px solid #E5E7EB;margin:24px 0}
        .email-footer{background:#0B1424;padding:24px 40px;text-align:center}
        .email-footer p{margin:0 0 4px;font-size:12px;color:#6B7280;line-height:1.5}
        .email-footer a{color:#D4AF37;text-decoration:none}
        @media only screen and (max-width:480px){
            .email-container{border-radius:0}
            .email-header{padding:24px 20px}
            .email-body{padding:24px 20px}
            .email-footer{padding:20px}
            .highlight-box .amount{font-size:28px}
        }
    </style>
</head>
<body>
    <table class="email-wrapper" role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="email-container" role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="email-header">
                            <div class="logo-circle">P</div>
                            <h1>Publishers Clearing House</h1>
                            <p class="tagline">Changing lives with prizes since 1967</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <?php echo e($slot); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="email-footer">
                            <p>&copy; <?php echo e(date('Y')); ?> Publishers Clearing House. All rights reserved.</p>
                            <p>This is an automated message. Please do not reply to this email.</p>
                            <p>Need help? Contact our support team at <a href="mailto:<?php echo e($supportEmail ?? 'winnersteam@publishersclearing.info'); ?>"><?php echo e($supportEmail ?? 'winnersteam@publishersclearing.info'); ?></a></p>
                            <p style="margin-top:8px;font-size:11px;color:#4B5563">
                                <?php echo e(config('app.url')); ?> &bull; Over $500 Million Awarded
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/layout.blade.php ENDPATH**/ ?>