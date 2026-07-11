<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">PACS Explorer</x-slot>
        <x-slot name="description">Cari studi berdasarkan Study Instance UID</x-slot>

        <form wire:submit="explore" class="max-w-lg">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Study Instance UID</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="1.2.840.114350.1.13..." wire:model="studyUid" />
                    </x-filament::input.wrapper>
                </div>
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Explore
                </x-filament::button>
            </div>
        </form>

        @if($error)
            <div class="mt-4 p-3 text-sm text-danger-700 bg-danger-50 rounded-lg">{{ $error }}</div>
        @endif
    </x-filament::section>
</x-filament-panels::page>