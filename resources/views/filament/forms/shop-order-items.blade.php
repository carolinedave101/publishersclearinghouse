<div class="divide-y divide-gray-200">
    @forelse($items as $item)
        <div class="flex items-center justify-between py-2 text-sm">
            <div class="flex-1 min-w-0">
                <span class="text-gray-900 font-medium">{{ $item['name'] ?? 'Item' }}</span>
                <span class="text-gray-500 ml-2">&times; {{ $item['quantity'] ?? 1 }}</span>
            </div>
            <div class="text-right flex-shrink-0 ml-4">
                <span class="text-gray-900">${{ number_format($item['price'] ?? 0, 2) }}</span>
                @if(isset($item['line_total']))
                    <span class="text-gray-400 ml-2">(${{ number_format($item['line_total'], 2) }})</span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 py-2">No items</p>
    @endforelse
</div>
