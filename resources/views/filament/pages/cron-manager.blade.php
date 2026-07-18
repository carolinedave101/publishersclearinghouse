<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary-100 rounded-lg">
                        <x-heroicon-o-inbox class="w-6 h-6 text-primary-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jobs in Queue</p>
                        <p class="text-2xl font-bold">{{ $stats['queue_count'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-danger-100 rounded-lg">
                        <x-heroicon-o-exclamation-circle class="w-6 h-6 text-danger-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Failed Jobs</p>
                        <p class="text-2xl font-bold">{{ $stats['failed_count'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-success-100 rounded-lg">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-success-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Emails Sent (All Time)</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_sent']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-warning-100 rounded-lg">
                        <x-heroicon-o-megaphone class="w-6 h-6 text-warning-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Active Campaigns</p>
                        <p class="text-2xl font-bold">{{ $stats['active_campaigns'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cron URL Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">External Cron URL</h3>
                    <p class="text-xs text-gray-500 mt-1">Use this URL with cron-job.org, EasyCron, or any cron service that supports HTTPS.</p>
                    <div class="mt-2 flex items-center gap-2">
                        <code class="px-3 py-1.5 bg-gray-100 rounded text-sm font-mono text-gray-700 break-all">{{ $cronUrl }}</code>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-filament::button wire:click="refreshToken" color="warning" size="sm">
                        Regenerate Token
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Scheduled Tasks Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900">Scheduled Tasks</h3>
                <div>
                    <x-filament::button wire:click="$dispatch('open-create-modal')" color="primary" size="sm">
                        Add Task
                    </x-filament::button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase tracking-wider">Command</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase tracking-wider">Frequency</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 text-xs uppercase tracking-wider">Enabled</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase tracking-wider">Last Run</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 text-xs uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr class="border-b border-gray-100 hover:bg-gray-50" wire:key="task-{{ $task['id'] }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $task['description'] }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $task['command'] }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <select
                                        wire:change="updateFrequency({{ $task['id'] }}, $event.target.value)"
                                        class="text-xs border border-gray-300 rounded px-2 py-1 text-gray-700"
                                    >
                                        <option value="everyMinute" {{ $task['frequency'] === 'everyMinute' ? 'selected' : '' }}>Every Minute</option>
                                        <option value="everyFiveMinutes" {{ $task['frequency'] === 'everyFiveMinutes' ? 'selected' : '' }}>Every 5 Min</option>
                                        <option value="everyTenMinutes" {{ $task['frequency'] === 'everyTenMinutes' ? 'selected' : '' }}>Every 10 Min</option>
                                        <option value="everyFifteenMinutes" {{ $task['frequency'] === 'everyFifteenMinutes' ? 'selected' : '' }}>Every 15 Min</option>
                                        <option value="everyThirtyMinutes" {{ $task['frequency'] === 'everyThirtyMinutes' ? 'selected' : '' }}>Every 30 Min</option>
                                        <option value="hourly" {{ $task['frequency'] === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                        <option value="daily" {{ $task['frequency'] === 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ $task['frequency'] === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ $task['frequency'] === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        wire:click="toggleTask({{ $task['id'] }}, {{ $task['is_enabled'] ? 'false' : 'true' }})"
                                        class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium {{ $task['is_enabled'] ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-500' }}"
                                    >
                                        {{ $task['is_enabled'] ? 'ON' : 'OFF' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    @if($task['last_run_at'])
                                        {{ \Carbon\Carbon::parse($task['last_run_at'])->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                    @if($task['last_output'])
                                        <button
                                            wire:click="$set('showOutput', '{{ $task['id'] }}')"
                                            class="ml-1 text-primary-600 hover:text-primary-800"
                                            x-on:click="
                                                $nextTick(() => $dispatch('show-output', { id: {{ $task['id'] }}, output: @js($task['last_output']) }))
                                            "
                                        >
                                            (view output)
                                        </button>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <x-filament::button wire:click="runNow({{ $task['id'] }})" color="success" size="xs">
                                            Run Now
                                        </x-filament::button>
                                        <x-filament::button wire:click="deleteTask({{ $task['id'] }})" color="danger" size="xs">
                                            Delete
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No scheduled tasks configured yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Log Viewer --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900">Queue Worker Log <span class="text-xs text-gray-500 font-normal">(last 50 lines)</span></h3>
                <x-filament::button wire:click="$refresh" color="gray" size="xs">
                    Refresh Log
                </x-filament::button>
            </div>
            <div class="p-4">
                <pre class="text-xs font-mono bg-gray-900 text-green-400 rounded-lg p-4 overflow-x-auto max-h-96 leading-relaxed">{{ $logPreview }}</pre>
            </div>
        </div>

        {{-- Rate Limit Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Email Rate Limits</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Stackmail:</span>
                    <span class="font-medium">~50/hr | ~1000/day</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Campaign Default:</span>
                    <span class="font-medium">50/hr | 1000/day</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500">Queue Worker:</span>
                    <span class="font-medium">Processes 1 job at a time, stops after 55s</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
