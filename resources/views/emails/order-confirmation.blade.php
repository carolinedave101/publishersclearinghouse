@component('emails.layout', ['subject' => "Order Confirmation - #{$order->id}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Order Confirmation</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">Thank you for your order!</p>

<p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Dear {{ $order->customer_name }},</p>

<p>Your order has been received and is being processed. Here are your order details:</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Order #</td><td>{{ $order->id }}</td></tr>
    <tr><td>Status</td><td>{{ ucfirst($order->status) }}</td></tr>
    <tr><td>Total</td><td style="font-weight:800;">${{ number_format($order->total, 2) }}</td></tr>
</table>

<div class="info-box">
    <p class="label">Shipping Address</p>
    <p class="value" style="font-weight:400;font-size:14px;">
        {{ $order->address }}<br>
        {{ $order->city }}, {{ $order->state }} {{ $order->zip }}
    </p>
</div>

<div class="btn-center">
    <a href="{{ config('app.url') }}/orders" class="btn btn-primary">View My Orders</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">If you have any questions about your order, please contact our support team.</p>
@endcomponent
