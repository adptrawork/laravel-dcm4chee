<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">PACS Status</x-slot>
            <p class="text-2xl font-bold">{{ $pacsStatus ?? 'Checking...' }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Pending Worklist</x-slot>
            <p class="text-2xl font-bold">{{ $pendingWorklist }}</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
