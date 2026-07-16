@component('emails.layout', ['subject' => "New Membership Signup - {$subscription->subscriber_name}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">New Membership Signup</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A new member has signed up for the PCH membership program.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Name</td><td>{{ $subscription->subscriber_name }}</td></tr>
    <tr><td>Email</td><td>{{ $subscription->subscriber_email }}</td></tr>
    @if ($subscription->tier)
        <tr><td>Plan</td><td>{{ $subscription->tier->name }}</td></tr>
    @endif
    <tr><td>Status</td><td>{{ ucfirst($subscription->status) }}</td></tr>
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/membership-subscriptions/{{ $subscription->id }}/edit" class="btn btn-secondary">View in Admin</a>
</div>
@endcomponent
