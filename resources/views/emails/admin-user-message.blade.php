@component('emails.layout', ['subject' => "New User Message - {$message->subject}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">New User Message</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A user has sent a new message through the portal.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>From</td><td>{{ $message->user?->name ?? 'Unknown User' }} ({{ $message->user?->email ?? 'N/A' }})</td></tr>
    <tr><td>Subject</td><td>{{ $message->subject }}</td></tr>
</table>

<div class="info-box">
    <p class="label">Message</p>
    <p style="margin:8px 0 0;font-size:14px;color:#374151;line-height:1.6;font-style:italic;border-left:3px solid #D4AF37;padding-left:16px;">{{ $message->message }}</p>
</div>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/user-messages" class="btn btn-secondary">View in Admin</a>
</div>
@endcomponent
