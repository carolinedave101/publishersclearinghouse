@component('emails.layout', ['subject' => "Deposit Confirmation - \${$deposit->amount}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Deposit Confirmation</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">Your deposit has been received.</p>

<p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Dear {{ $winner->first_name }},</p>

<p>This is to confirm that we have received your deposit of <strong>${{ number_format($deposit->amount, 2) }}</strong> via <strong>{{ $deposit->paymentMethod?->name ?? 'your selected method' }}</strong>.</p>

<div class="highlight-box">
    <p class="amount">${{ number_format($deposit->amount, 2) }}</p>
    <p class="desc">Deposit Amount</p>
</div>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Status</td><td>{{ ucfirst($deposit->status) }}</td></tr>
    <tr><td>Date</td><td>{{ $deposit->created_at->format('M j, Y g:i A') }}</td></tr>
    @if($deposit->notes)
        <tr><td>Notes</td><td>{{ $deposit->notes }}</td></tr>
    @endif
</table>

<p>Our team will review your deposit and update your account accordingly. You will be notified once it has been approved.</p>

<div class="btn-center">
    <a href="{{ config('app.url') }}/deposits" class="btn btn-primary">View My Deposits</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">If you have any questions, please contact our support team.</p>
@endcomponent
