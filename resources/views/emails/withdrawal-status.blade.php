@component('emails.layout', ['subject' => "Withdrawal Status Update - \${$withdrawal->amount}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Withdrawal Request Update</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">Your withdrawal request has been updated.</p>

<p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Dear {{ $winner->first_name }},</p>

<p>This is an update regarding your withdrawal request of <strong>${{ number_format($withdrawal->amount, 2) }}</strong>.</p>

<div class="info-box" style="text-align:center;border-color:#D4AF37;">
    <p class="label" style="text-align:center;">Current Status</p>
    <p class="value" style="text-align:center;font-size:20px;">
        @switch($withdrawal->status)
            @case('approved')
                ✅ Approved
                @break
            @case('completed')
                ✅ Completed
                @break
            @case('rejected')
                ❌ Rejected
                @break
            @default
                ⏳ {{ ucfirst($withdrawal->status) }}
        @endswitch
    </p>
</div>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Amount</td><td style="font-weight:800;">${{ number_format($withdrawal->amount, 2) }}</td></tr>
    <tr><td>Payment Method</td><td>{{ $withdrawal->paymentMethod?->name ?? 'N/A' }}</td></tr>
    @if($withdrawal->status === 'approved' || $withdrawal->status === 'completed')
        <tr><td>Net Amount</td><td>${{ number_format($withdrawal->net_amount, 2) }}</td></tr>
    @endif
    <tr><td>Requested On</td><td>{{ $withdrawal->created_at->format('M j, Y') }}</td></tr>
</table>

@if($withdrawal->status === 'approved' || $withdrawal->status === 'completed')
    <p>Your withdrawal has been processed. The funds should appear in your account shortly.</p>
@elseif($withdrawal->status === 'rejected')
    <p>Unfortunately, your withdrawal request could not be processed at this time. Please contact support for more information.</p>
@else
    <p>Your withdrawal request is currently being reviewed. We will notify you once there is an update.</p>
@endif

<div class="btn-center">
    <a href="{{ config('app.url') }}/withdrawals" class="btn btn-primary">View My Withdrawals</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">If you have any questions, please contact our support team.</p>
@endcomponent
