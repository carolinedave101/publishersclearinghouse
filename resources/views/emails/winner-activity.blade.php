@component('emails.layout', ['subject' => $subject ?? 'Congratulations from PCH!'])
<p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Hello {{ $winner->first_name }},</p>

@if ($messageBody)
    {!! $messageBody !!}
@else
    <p>Congratulations! You have been selected as a winner with <strong>Publishers Clearing House</strong>. Your prize is ready to be claimed.</p>
@endif

<div class="highlight-box">
    <p class="amount">{{ $formattedPrize }}</p>
    <p class="desc">Your Prize Amount</p>
</div>

<div class="info-box">
    <p class="label">Winner Code</p>
    <p class="value" style="font-family:'Courier New',monospace;letter-spacing:2px;">{{ $winner->unique_code }}</p>
</div>

<p>Log in to your winner dashboard to track your prize and claim status:</p>

<div class="btn-center">
    <a href="{{ $appUrl }}" class="btn btn-primary">Go to Dashboard</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">This is an automated message from Publishers Clearing House. Please do not reply to this email.</p>
@endcomponent
