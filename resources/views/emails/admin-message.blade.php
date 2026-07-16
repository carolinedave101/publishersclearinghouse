@component('emails.layout', ['subject' => $subjectLine])
@if ($recipientName)
    <p style="font-size:18px;color:#1B2A4A;margin:0 0 16px;">Hello {{ $recipientName }},</p>
@endif

<h2>{{ $subjectLine }}</h2>

{!! $messageBody !!}

<div class="btn-center">
    <a href="{{ $appUrl }}" class="btn btn-primary">Visit PCH Portal</a>
</div>

<hr class="divider">

<p style="font-size:13px;color:#9CA3AF;text-align:center;">This message was sent from the Publishers Clearing House admin team.</p>
@endcomponent
