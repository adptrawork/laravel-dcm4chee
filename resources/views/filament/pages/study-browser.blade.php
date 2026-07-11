<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Study Browser</x-slot>
        <x-slot name="description">Search studies from PACS</x-slot>
        <div class="grid grid-cols-4 gap-2 mb-4">
            <x-filament::input.wrapper><x-filament::input type="text" placeholder="Patient Name" wire:model="searchName" /></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input type="text" placeholder="Patient ID" wire:model="searchId" /></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input type="text" placeholder="Study Date (YYYYMMDD)" wire:model="searchDate" /></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input type="text" placeholder="Accession" wire:model="searchAccession" /></x-filament::input.wrapper>
        </div>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
