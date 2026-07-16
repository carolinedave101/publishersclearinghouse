@component('emails.layout', ['subject' => 'Welcome to PCH Membership!'])
<div style="text-align:center;margin:0 0 16px;">
    <span style="font-size:40px;">🎉</span>
</div>

<h2 style="text-align:center;color:#1B2A4A;margin:0 0 4px;">Welcome to PCH Membership!</h2>
<p style="text-align:center;font-size:14px;color:#6B7280;margin:0 0 24px;">Thank you for joining the PCH family.</p>

<p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Dear {{ $subscription->subscriber_name }},</p>

<p>Thank you for joining the PCH membership program! Your membership is now active and you're ready to start enjoying exclusive benefits.</p>

@if ($subscription->tier)
    <div class="highlight-box">
        <p class="amount" style="font-size:24px;">{{ $subscription->tier->name }} Member</p>
        <p class="desc">Your Membership Plan</p>
    </div>
@endif

<p>As a member, you now enjoy exclusive benefits including multiplied entries to giveaways, member-only prizes, and more.</p>

<div class="btn-center">
    <a href="{{ config('app.url') }}/memberships" class="btn btn-primary">View Membership</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">We're excited to have you on this journey. Stay tuned for exclusive member offers!</p>
@endcomponent
