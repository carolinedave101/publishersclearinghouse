@component('emails.layout', ['subject' => "Withdrawal Request - \${$withdrawal->amount} from {$withdrawal->winner?->first_name ?? 'Unknown'}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Withdrawal Request</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A winner has requested a withdrawal.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Winner</td><td>{{ $withdrawal->winner->first_name }} {{ $withdrawal->winner->last_name }}</td></tr>
    <tr><td>Email</td><td>{{ $withdrawal->winner->email }}</td></tr>
    <tr><td>Amount</td><td style="font-weight:800;">${{ number_format($withdrawal->amount, 2) }}</td></tr>
    <tr><td>Payment Method</td><td>{{ $withdrawal->paymentMethod?->name ?? 'N/A' }}</td></tr>
    <tr><td>Winner Code</td><td style="font-family:monospace">{{ $withdrawal->winner->unique_code }}</td></tr>
    @if($withdrawal->notes)
        <tr><td>Notes</td><td>{{ $withdrawal->notes }}</td></tr>
    @endif
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/withdrawals/{{ $withdrawal->id }}/edit" class="btn btn-secondary">Review Withdrawal</a>
</div>
@endcomponent
