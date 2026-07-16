<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        @if(session()->has('site_logo_preview'))
            <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                <p class="text-sm font-medium mb-2">Current Logo Preview:</p>
                <img src="{{ session('site_logo_preview') }}" alt="Logo" style="max-height:60px; border-radius:10px;">
            </div>
        @endif

        <x-filament::button type="submit">
            Save Settings
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
