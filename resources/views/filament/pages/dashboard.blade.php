<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">Orders Waiting</x-slot>
            <p class="text-2xl font-bold text-gray-600">{{ $ordersWaiting }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Scheduled</x-slot>
            <p class="text-2xl font-bold text-warning-600">{{ $ordersScheduled }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">In Progress</x-slot>
            <p class="text-2xl font-bold text-info-600">{{ $ordersInProgress }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Ready to Read</x-slot>
            <p class="text-2xl font-bold text-success-600">{{ $readyToRead }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Reported Today</x-slot>
            <p class="text-2xl font-bold text-purple-600">{{ $reportedToday }}</p>
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">Active Worklist</x-slot>
        {{ $this->table }}
    </x-filament::section>

    <x-filament::section class="mt-4">
        <x-slot name="heading">PACS Health</x-slot>
        <p class="text-lg font-medium @if(str_contains($pacsStatus, 'Connected')) text-success-600 @else text-danger-600 @endif">
            {{ $pacsStatus }}
        </p>
    </x-filament::section>
</x-filament-panels::page>
