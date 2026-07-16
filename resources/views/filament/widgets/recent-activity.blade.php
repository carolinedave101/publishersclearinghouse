<x-filament::widget>
    <x-filament::card class="fi-widget">
        <div class="flex items-center justify-between px-6 py-4">
            <h2 class="text-lg font-bold tracking-tight fi-widget-heading">
                {{ $this->getHeading() }}
            </h2>
        </div>
        <div class="px-6 pb-5">
            @if ($activities->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($activities as $activity)
                        <div class="flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-primary-500"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-700 dark:text-gray-200">
                                    {{ \Illuminate\Support\Str::limit($activity->description ?? ('[' . $activity->action . '] ' . $activity->collection), 80) }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $activity->created_at?->diffForHumans() ?? '' }}
                                    @if ($activity->admin)· {{ $activity->admin }}@endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No recent activity yet.</p>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
