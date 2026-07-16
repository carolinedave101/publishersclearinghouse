<x-filament-panels::page>
    <x-filament-panels::form wire:submit="send">
        {{ $this->form }}

        <div style="display:flex;gap:8px;margin-top:16px;">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-paper-airplane">
                Send Email
            </x-filament::button>
            <x-filament::button type="reset" color="gray" icon="heroicon-o-arrow-path" wire:click="mount">
                Reset
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
