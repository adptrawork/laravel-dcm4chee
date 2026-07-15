<x-filament-panels::page>
    @if($studyUid)
        <x-filament::section class="mb-4">
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-success-700">
                    <x-filament::icon name="heroicon-o-check-circle" class="w-5 h-5" />
                    Upload Successful
                </div>
            </x-slot>
            <div class="flex items-center gap-3">
                <span class="text-sm">Study UID:</span>
                <code class="text-sm bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $studyUid }}</code>
                <x-filament::button tag="a" href="{{ url('/admin/studies/' . $studyUid) }}" color="primary" size="sm" icon="heroicon-o-eye">
                    View Study
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon name="heroicon-o-arrow-up-on-square" class="w-4 h-4" />
                Upload DICOM to PACS
            </div>
        </x-slot>

        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button wire:click="upload" color="primary" icon="heroicon-o-arrow-up-tray">
                Upload to PACS
            </x-filament::button>
        </div>

        <p class="text-xs text-gray-400 mt-3">
            Files are sent to PACS via STOW-RS. Supported: CT, MR, CR, DX, US, and other DICOM modalities.
        </p>
    </x-filament::section>

    @if($result)
        <x-filament::section class="mt-4">
            <x-slot name="heading">Result</x-slot>
            <pre class="text-xs bg-black/5 p-4 rounded-lg overflow-x-auto">{{ $result }}</pre>
        </x-filament::section>
    @endif
</x-filament-panels::page>
