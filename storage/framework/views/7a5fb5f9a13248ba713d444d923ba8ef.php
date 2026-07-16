<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Georgia, serif; margin: 0; padding: 0; background: #f5f0e8; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; padding: 30px; background: linear-gradient(135deg, #1B2A4A 0%, #2a3f6a 100%); border-radius: 12px 12px 0 0; }
        .header h1 { color: #D4AF37; margin: 0; font-size: 28px; letter-spacing: 1px; }
        .header .subtitle { color: #fff; margin-top: 8px; font-size: 16px; }
        .content { background: #fff; padding: 40px 30px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .prize-box { background: linear-gradient(135deg, #D4AF37 0%, #C5A55A 100%); color: #1B2A4A; text-align: center; padding: 30px; border-radius: 12px; margin: 20px 0; }
        .prize-box .amount { font-size: 48px; font-weight: bold; margin: 0; }
        .prize-box .label { font-size: 18px; margin: 5px 0 0; }
        .code-box { background: #f8f4ec; border: 2px dashed #D4AF37; text-align: center; padding: 20px; border-radius: 8px; margin: 25px 0; }
        .code-box .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1B2A4A; font-family: 'Courier New', monospace; }
        .code-box .label { color: #666; font-size: 14px; margin-bottom: 10px; }
        .btn { display: inline-block; background: #E63946; color: #fff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-size: 18px; font-weight: bold; margin: 20px 0; }
        .footer-text { text-align: center; padding: 30px; color: #999; font-size: 12px; }
        .confetti { text-align: center; font-size: 40px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="confetti">🎉🏆🎉</div>
            <h1>CONGRATULATIONS!</h1>
            <div class="subtitle">Publishers Clearing House</div>
        </div>
        <div class="content">
            <p style="font-size: 20px; text-align: center;">Dear <?php echo e($winner->first_name); ?> <?php echo e($winner->last_name); ?>,</p>
            <p style="font-size: 16px; text-align: center; color: #555;">We are thrilled to inform you that you are a winner!</p>
            <div class="prize-box">
                <p class="amount"><?php echo e($formattedPrize); ?></p>
                <p class="label"><?php echo e($winner->prize_description ?? 'Prize Awarded'); ?></p>
            </div>
            <p style="text-align: center; color: #555;">To claim your prize, enter your unique winner code on our Winners Portal.</p>
            <div class="code-box">
                <div class="label">YOUR UNIQUE WINNER CODE</div>
                <div class="code"><?php echo e($winner->unique_code); ?></div>
            </div>
            <div style="text-align: center;">
                <a href="<?php echo e($appUrl); ?>" class="btn">CLAIM YOUR PRIZE NOW</a>
            </div>
            <p style="text-align: center; color: #888; font-size: 14px; margin-top: 30px;">
                Visit <strong><?php echo e($appUrl); ?></strong> and enter your code to see your personalized celebration!
            </p>
            <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
            <p style="color: #999; font-size: 13px; text-align: center;">
                This email was sent to <?php echo e($winner->email); ?> by Publishers Clearing House.<br>
                If you have any questions, please contact our winners support team.
            </p>
        </div>
        <div class="footer-text">
            &copy; <?php echo e(date('Y')); ?> Publishers Clearing House. All Rights Reserved.
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/emails/winner-notification.blade.php ENDPATH**/ ?>