<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Patient Registration</x-slot>
        <x-slot name="description">Register patient to PACS</x-slot>
        <form wire:submit="register">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit">Register</x-filament::button>
            </div>
        </form>
        @if ($result)
            <div class="mt-4 p-3 rounded {{ str_contains($result, 'Error') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ $result }}
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
