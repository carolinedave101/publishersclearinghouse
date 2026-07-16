<div class="p-4">
    @if(empty($history))
        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No campaigns sent to this winner yet.</p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-2 font-medium text-gray-500 dark:text-gray-400">Campaign</th>
                    <th class="text-left py-2 font-medium text-gray-500 dark:text-gray-400">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $entry)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 text-gray-900 dark:text-white">{{ $entry['campaign'] }}</td>
                        <td class="py-2 text-gray-500 dark:text-gray-400">
                            {{ $entry['sent_at'] ? \Carbon\Carbon::parse($entry['sent_at'])->format('M j, Y g:i A') : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Total: {{ count($history) }} campaign(s)</p>
    @endif
</div>
