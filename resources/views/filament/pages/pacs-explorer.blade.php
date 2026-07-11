<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">PACS Explorer</x-slot>
        <x-slot name="description">Cari studi berdasarkan Study Instance UID — tempel UID dari DICOM viewer atau daftar studi</x-slot>

        <form wire:submit="explore" class="max-w-xl">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-filament::input.wrapper label="Study Instance UID">
                        <x-filament::input type="text" placeholder="1.2.840.114350.1.13..." wire:model="studyUid" />
                    </x-filament::input.wrapper>
                    <p class="text-xs text-gray-400 mt-1">Contoh: 1.2.840.114350.1.13.XXXXX.3.7.3.XXXXX.XX</p>
                </div>
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Explore
                </x-filament::button>
            </div>
        </form>

        @if($error)
            <div class="mt-4 p-3 text-sm text-danger-700 bg-danger-50 rounded-lg flex items-center gap-2">
                <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                {{ $error }}
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
