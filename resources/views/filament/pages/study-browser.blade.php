<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Study Browser</x-slot>
        <x-slot name="description">Cari studi imaging dari PACS berdasarkan pasien, tanggal, atau accession number</x-slot>

        <form wire:submit="search" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pasien</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="cth. Smith" wire:model="searchName" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Patient ID</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="cth. MRN-001" wire:model="searchId" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Studi</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="searchDate" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Accession No.</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="cth. ACC-2026..." wire:model="searchAccession" />
                    </x-filament::input.wrapper>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass"
                    wire:loading.attr="disabled" wire:target="search,loadStudies">
                    <span wire:loading.remove wire:target="search,loadStudies">Cari</span>
                    <span wire:loading wire:target="search,loadStudies">Mencari...</span>
                </x-filament::button>

                @if($searched)
                    <x-filament::button color="gray" icon="heroicon-o-x-mark" wire:click="resetSearch">
                        Bersihkan
                    </x-filament::button>
                @endif

                <div wire:loading wire:target="search,loadStudies" class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    Menghubungi PACS...
                </div>
            </div>
        </form>

        {{-- Error state --}}
        @if($error)
            <div class="p-4 mb-4 text-sm text-danger-700 bg-danger-50 rounded-lg">
                <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5 inline mr-1" />
                {{ $error }}
            </div>
        @endif

        {{-- Search status --}}
        @if($searched && !$error)
            <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                <span>{{ count($studies) }} studi ditemukan</span>
                @if(count($studies) > 0)
                    <span class="text-xs text-gray-400">Halaman {{ $page }}</span>
                @endif
            </div>
        @endif

        {{-- Empty state --}}
        @if($searched && count($studies) === 0 && !$error)
            <div class="text-center py-10 text-gray-400 border border-dashed border-gray-200 rounded-lg">
                <x-filament::icon name="heroicon-o-document-magnifying-glass" class="w-10 h-10 mx-auto mb-2" />
                <p class="font-medium text-gray-500">Tidak ada studi yang cocok.</p>
                <p class="text-sm mt-1">Coba kata kunci lain, atau periksa kembali Patient ID / Accession Number.</p>
            </div>
        @endif

        {{-- Results cards --}}
        @if(count($studies) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($studies as $study)
                    <x-study-card :study="$study" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between mt-4">
                <span class="text-sm text-gray-500">Halaman {{ $page }}</span>
                <div class="flex gap-2">
                    <x-filament::button color="gray" size="sm" icon="heroicon-o-chevron-left"
                        wire:click="prevPage" wire:loading.attr="disabled"
                        :disabled="$page <= 1">
                        Sebelumnya
                    </x-filament::button>
                    <x-filament::button color="gray" size="sm" icon-position="after" icon="heroicon-o-chevron-right"
                        wire:click="nextPage" wire:loading.attr="disabled"
                        :disabled="count($studies) < 10">
                        Selanjutnya
                    </x-filament::button>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>