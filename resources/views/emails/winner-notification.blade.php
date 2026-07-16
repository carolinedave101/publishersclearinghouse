@component('emails.layout', ['subject' => "Congratulations {$winner->first_name}! You've Won!"])
<div style="text-align:center;margin:0 0 20px;">
    <span style="font-size:48px;">🏆</span>
</div>

<h2 style="text-align:center;color:#1B2A4A;font-size:24px;margin:0 0 8px;">CONGRATULATIONS!</h2>
<p style="text-align:center;font-size:18px;color:#374151;margin:0 0 24px;">Dear {{ $winner->first_name }} {{ $winner->last_name }},</p>

<p style="text-align:center;font-size:16px;color:#4B5563;">We are thrilled to inform you that you are a winner!</p>

<div class="highlight-box">
    <p class="amount">{{ $formattedPrize }}</p>
    <p class="desc">{{ $winner->prize_description ?? 'Prize Awarded' }}</p>
</div>

<p style="text-align:center;color:#4B5563;">To claim your prize, enter your unique winner code on our Winners Portal.</p>

<div class="info-box" style="text-align:center;">
    <p class="label" style="text-align:center;">YOUR UNIQUE WINNER CODE</p>
    <p class="value" style="font-family:'Courier New',monospace;font-size:24px;letter-spacing:4px;text-align:center;">{{ $winner->unique_code }}</p>
</div>

<div class="btn-center">
    <a href="{{ $appUrl }}" class="btn btn-primary" style="font-size:18px;padding:16px 48px;">CLAIM YOUR PRIZE NOW</a>
</div>

<p style="text-align:center;color:#6B7280;font-size:14px;">Visit <strong>{{ $appUrl }}</strong> and enter your code to see your personalized celebration!</p>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">
    This email was sent to {{ $winner->email }} by Publishers Clearing House.<br>
    If you have any questions, please contact our winners support team.
</p>
@endcomponent
