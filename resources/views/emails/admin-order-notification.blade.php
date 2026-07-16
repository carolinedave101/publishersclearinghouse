@component('emails.layout', ['subject' => "New Order #{$order->id} - {$order->customer_name}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">New Order Received</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A new order has been placed in the shop.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Customer</td><td>{{ $order->customer_name }} ({{ $order->customer_email }})</td></tr>
    <tr><td>Order #</td><td>{{ $order->id }}</td></tr>
    <tr><td>Total</td><td>${{ number_format($order->total, 2) }}</td></tr>
    <tr><td>Shipping To</td><td>{{ $order->address }}, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}</td></tr>
    <tr><td>Status</td><td>{{ ucfirst($order->status) }}</td></tr>
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/shop-orders/{{ $order->id }}/edit" class="btn btn-secondary">View in Admin</a>
</div>
@endcomponent
