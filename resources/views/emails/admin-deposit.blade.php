@component('emails.layout', ['subject' => "New Deposit - \${$deposit->amount} from {$deposit->winner?->first_name ?? 'Unknown'}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">New Deposit Submitted</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A winner has submitted a new deposit for review.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Winner</td><td>{{ $deposit->winner->first_name }} {{ $deposit->winner->last_name }}</td></tr>
    <tr><td>Email</td><td>{{ $deposit->winner->email }}</td></tr>
    <tr><td>Amount</td><td>${{ number_format($deposit->amount, 2) }}</td></tr>
    <tr><td>Payment Method</td><td>{{ $deposit->paymentMethod?->name ?? 'N/A' }}</td></tr>
    <tr><td>Winner Code</td><td style="font-family:monospace">{{ $deposit->winner->unique_code }}</td></tr>
    @if($deposit->notes)
        <tr><td>Notes</td><td>{{ $deposit->notes }}</td></tr>
    @endif
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/deposits/{{ $deposit->id }}/edit" class="btn btn-secondary">Review Deposit</a>
</div>
@endcomponent
