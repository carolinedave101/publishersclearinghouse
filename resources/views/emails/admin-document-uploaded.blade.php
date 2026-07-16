@component('emails.layout', ['subject' => "Document Uploaded - {$winner->first_name} {$winner->last_name}"])
<h2 style="color:#D4AF37;margin:0 0 4px;">Document Uploaded by Winner</h2>
<p style="font-size:14px;color:#6B7280;margin:0 0 24px;">A winner has uploaded a new document.</p>

<table class="info-table" role="presentation" cellpadding="0" cellspacing="0">
    <tr><td>Winner</td><td>{{ $winner->first_name }} {{ $winner->last_name }} ({{ $winner->email }})</td></tr>
    <tr><td>Document Type</td><td>{{ ucfirst($document->document_type) }}</td></tr>
    <tr><td>File Name</td><td>{{ $document->file_name }}</td></tr>
    <tr><td>File Size</td><td>{{ round($document->file_size / 1024, 1) }} KB</td></tr>
    @if ($document->custom_type)
        <tr><td>Custom Type</td><td>{{ $document->custom_type }}</td></tr>
    @endif
</table>

<div class="btn-center">
    <a href="{{ config('app.url') }}/admin/documents/{{ $document->id }}/edit" class="btn btn-secondary">View in Admin</a>
</div>
@endcomponent
