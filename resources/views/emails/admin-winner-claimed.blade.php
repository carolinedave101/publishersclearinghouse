@component('emails.layout', ['subject' => "Prize Claimed - {$winner->first_name} {$winner->last_name}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Prize Claimed!</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A winner has claimed their prize.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Winner</td><td>{{ $winner->first_name }} {{ $winner->last_name }}</td></tr>
    <tr><td>Email</td><td>{{ $winner->email }}</td></tr>
    <tr><td>Prize Amount</td><td style="font-weight:800;">${{ number_format($winner->prize_amount, 0) }}</td></tr>
    <tr><td>Prize Description</td><td>{{ $winner->prize_description ?? 'N/A' }}</td></tr>
    <tr><td>Winner Code</td><td style="font-family:monospace">{{ $winner->unique_code }}</td></tr>
    @if($winner->claimed_at)
        <tr><td>Claimed At</td><td>{{ $winner->claimed_at->format('M j, Y g:i A') }}</td></tr>
    @endif
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/winners/{{ $winner->id }}/edit" class="btn btn-secondary">View in Admin</a>
</div>
@endcomponent
