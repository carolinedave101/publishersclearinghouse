@php
    $data = $this->getViewData();
    $stats = $data['stats'];
    $hourlyData = $data['hourlyData'];
    $maxHourly = $data['maxHourly'];
    $totalForPie = $data['totalForPie'];
    $segments = $data['segments'];
    $dailyPct = $data['dailyPct'];
@endphp

@if(empty($stats))
    <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <p class="text-gray-500 dark:text-gray-400">Campaign data not available.</p>
    </div>
@else
<div class="space-y-6">

    {{-- Status Banner + Progress --}}
    <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="flex items-center justify-between mb-3">
            <div>
                <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ ucfirst($stats['status']) }}</span>
                @if($stats['completed_at'])
                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Completed {{ $stats['completed_at']->format('M j, Y g:i A') }}</span>
                @elseif($stats['started_at'])
                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Started {{ $stats['started_at']->format('M j, Y g:i A') }}</span>
                @endif
            </div>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 ease-in-out"
                 style="width: {{ $stats['progress'] }}%; background: linear-gradient(90deg, #D4AF37, #C5A030);"></div>
        </div>
        <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $stats['progress'] }}% complete</span>
            <span>{{ number_format($stats['sent'] + $stats['failed']) }} / {{ number_format($stats['total']) }}</span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sent</p>
            <p class="text-3xl font-bold text-success-600 dark:text-success-400">{{ number_format($stats['sent']) }}</p>
        </div>
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</p>
            <p class="text-3xl font-bold text-danger-600 dark:text-danger-400">{{ number_format($stats['failed']) }}</p>
        </div>
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining</p>
            <p class="text-3xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Est. Time</p>
            <p class="text-3xl font-bold text-gray-700 dark:text-gray-300">@if($stats['estimated_hours']) {{ $stats['estimated_hours'] }}h @else — @endif</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Daily Quota Gauge --}}
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Daily Quota</h3>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden">
                <div class="h-full rounded-full flex items-center justify-end pr-2 text-xs font-bold text-white transition-all"
                     style="width: {{ $dailyPct }}%; background: {{ $dailyPct > 90 ? '#DC2626' : ($dailyPct > 70 ? '#F59E0B' : '#10B981') }};">
                    @if($dailyPct > 20) {{ $dailyPct }}% @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($stats['daily_used']) }} / {{ number_format($stats['daily_max']) }} sent today</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Hourly: {{ $stats['hourly_used'] }} / {{ $stats['hourly_max'] }}</p>
        </div>

        {{-- 24-hour Send Rate Bar Chart --}}
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Send Rate (24h)</h3>
            <div class="flex items-end gap-1 h-24">
                @foreach($hourlyData as $i => $count)
                    <div class="flex-1 flex flex-col items-center justify-end h-full">
                        <div class="w-full rounded-t transition-all duration-200 hover:opacity-80"
                             style="height: {{ max(2, ($count / $maxHourly) * 100) }}%; background: linear-gradient(180deg, #D4AF37, #B8960F);"
                             title="Hour {{ $i }}: {{ $count }} sent"></div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                <span>0h</span><span>12h</span><span>23h</span>
            </div>
        </div>

        {{-- Status Breakdown --}}
        <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Recipients by Status</h3>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-5 overflow-hidden flex">
                @foreach($segments as $seg)
                    @php $pct = max(1, round($seg['count'] / $totalForPie * 100)); @endphp
                    @if($pct > 0)
                        <div style="width: {{ $pct }}%; background: {{ $seg['color'] }};" class="h-full first:rounded-l-full last:rounded-r-full" title="{{ $seg['label'] }}: {{ number_format($seg['count']) }}"></div>
                    @endif
                @endforeach
            </div>
            <div class="mt-2 space-y-1">
                @foreach($segments as $seg)
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full inline-block" style="background: {{ $seg['color'] }}"></span>
                            {{ $seg['label'] }}
                        </span>
                        <span class="font-medium">{{ number_format($seg['count']) }} ({{ round($seg['count'] / $totalForPie * 100) }}%)</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Campaign Info --}}
    <div class="filament-stats-card p-4 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Name</span>
                <p class="font-medium">{{ $stats['name'] ?? $this->campaign?->name ?? '' }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Subject</span>
                <p class="font-medium truncate">{{ $this->campaign?->subject ?? '' }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Variants</span>
                <p class="font-medium">{{ $stats['variants'] }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Rate</span>
                <p class="font-medium">{{ $stats['hourly_max'] }}/hr &bull; {{ $stats['daily_max'] }}/day</p>
            </div>
        </div>
        @if($this->campaign?->recipient_filter)
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <span class="text-xs text-gray-500 dark:text-gray-400">Filter:</span>
            <span class="text-xs text-gray-700 dark:text-gray-300 ml-2">
                @php $filter = $this->campaign->recipient_filter; @endphp
                @if(!empty($filter['statuses'])) Status: {{ implode(', ', $filter['statuses']) }} | @endif
                @if(!empty($filter['is_demo'])) {{ $filter['is_demo'] }} | @endif
                @if(!empty($filter['prize_min'])) ${{ number_format($filter['prize_min']) }}+ @endif
                @if(!empty($filter['prize_max'])) up to ${{ number_format($filter['prize_max']) }} @endif
                @if(!empty($filter['states'])) States: {{ implode(', ', $filter['states']) }} @endif
            </span>
        </div>
        @endif
    </div>
</div>
@endif
