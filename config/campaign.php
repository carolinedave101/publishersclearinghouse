<?php

return [

    'name' => 'Prize Claim Notification',

    'subject' => 'Your PCH Prize Awaits — Claim It Now',

    'recipient_filter' => [
        'statuses' => ['new', 'processing', 'approved', 'delivered'],
        'is_demo' => 'exclude',
        'claim_status' => '',
        'prize_min' => null,
        'prize_max' => null,
        'states' => [],
        'created_from' => null,
        'created_until' => null,
    ],

    'rate_per_hour' => 50,
    'rate_per_day' => 1000,

    'body_variant_1' => '<p>Dear {firstName},</p>
<p>Congratulations! You have been identified as a winner in the Publishers Clearing House promotion. Your exclusive prize is ready and waiting for you.</p>
<p>To access your winnings and take the next steps to claim them, simply visit the PCH Winners Portal:</p>
<p style="text-align:center;margin:24px 0;">
    <a href="https://publishersclearing.info" class="btn btn-primary">Visit Winners Portal</a>
</p>
<p>Enter your unique winner code when prompted to view your prize details, submit documents, and choose a withdrawal method.</p>
<div class="info-box">
    <p class="label">Your Winner Code</p>
    <p class="value" style="font-family:monospace;font-size:18px;letter-spacing:2px;">{unique_code}</p>
</div>
<p>If you need assistance, our winners team is here to help:</p>
<table class="info-table">
    <tr><td>Phone</td><td>(888) 235-0528</td></tr>
    <tr><td>Email</td><td><a href="mailto:winnersteam@publishersclearing.info">winnersteam@publishersclearing.info</a></td></tr>
</table>
<p>This is a time-sensitive opportunity — log in today to begin the claims process.</p>
<p>Warm regards,<br><strong>Dave Goldberg</strong><br>Publishers Clearing House Company</p>',

    'body_variant_2' => '<p>Hello {firstName},</p>
<p>Great news! Our records show you are a confirmed winner in the latest PCH promotion. Your prize has been reserved and is available for claim.</p>
<p>Getting started is easy — just visit the PCH Winners Portal using the link below:</p>
<p style="text-align:center;margin:24px 0;">
    <a href="https://publishersclearing.info" class="btn btn-primary">Access Your Prize Now</a>
</p>
<p>When you arrive, you will be asked to enter your unique winner code. Once entered, you can review your prize details, upload any required documents, and select your preferred withdrawal method.</p>
<div class="info-box">
    <p class="label">Your Personal Code</p>
    <p class="value" style="font-family:monospace;font-size:18px;letter-spacing:2px;">{unique_code}</p>
</div>
<p>Our support team is available to assist you throughout the process:</p>
<table class="info-table">
    <tr><td>Phone</td><td>(888) 235-0528</td></tr>
    <tr><td>Email</td><td><a href="mailto:winnersteam@publishersclearing.info">winnersteam@publishersclearing.info</a></td></tr>
</table>
<p>Do not delay — your prize is waiting. Log in now to complete the process.</p>
<p>Best regards,<br><strong>Dave Goldberg</strong><br>Publishers Clearing House Company</p>',

    'body_variant_3' => '<p>Hi {firstName},</p>
<p>You have been selected as a winner! We have an exclusive prize package waiting for you as part of the Publishers Clearing House promotion.</p>
<p>Here is what to do next — visit the official PCH Winners Portal:</p>
<p style="text-align:center;margin:24px 0;">
    <a href="https://publishersclearing.info" class="btn btn-primary">Go to Winners Portal</a>
</p>
<p>Simply enter the unique code assigned to you when prompted. From there you can view your prize, submit the necessary paperwork, and pick a withdrawal option that works best for you.</p>
<div class="info-box">
    <p class="label">Your Unique Code</p>
    <p class="value" style="font-family:monospace;font-size:18px;letter-spacing:2px;">{unique_code}</p>
</div>
<p>If you have any questions, our team is ready to help:</p>
<table class="info-table">
    <tr><td>Phone</td><td>(888) 235-0528</td></tr>
    <tr><td>Email</td><td><a href="mailto:winnersteam@publishersclearing.info">winnersteam@publishersclearing.info</a></td></tr>
</table>
<p>This opportunity is available for a limited time — visit the portal today to secure your prize.</p>
<p>All the best,<br><strong>Dave Goldberg</strong><br>Publishers Clearing House Company</p>',

];
