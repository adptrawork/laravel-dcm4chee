<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Study Browser</x-slot>
        <x-slot name="description">Cari studi imaging dari PACS berdasarkan pasien, tanggal, atau accession number</x-slot>

        <form wire:submit="search">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5">

    <div class="grid grid-cols-12 gap-4">

        {{-- Patient Name --}}
        <div class="col-span-12 md:col-span-6 xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                Nama Pasien
            </label>

            <x-filament::input.wrapper prefix-icon="heroicon-m-user">
                <x-filament::input
                    wire:model.live.debounce.500ms="searchName"
                    placeholder="Cari nama pasien..."
                />
            </x-filament::input.wrapper>
        </div>

        {{-- Patient ID --}}
        <div class="col-span-12 md:col-span-6 xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                Patient ID
            </label>

            <x-filament::input.wrapper prefix-icon="heroicon-m-identification">
                <x-filament::input
                    wire:model.live.debounce.500ms="searchId"
                    placeholder="MRN-00001"
                />
            </x-filament::input.wrapper>
        </div>

        {{-- Study Date --}}
        <div class="col-span-12 md:col-span-6 xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                Tanggal Studi
            </label>

            <x-filament::input.wrapper prefix-icon="heroicon-m-calendar-days">
                <x-filament::input
                    type="date"
                    wire:model.live="searchDate"
                />
            </x-filament::input.wrapper>
        </div>

        {{-- Accession --}}
        <div class="col-span-12 md:col-span-6 xl:col-span-3">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                Accession Number
            </label>

            <x-filament::input.wrapper prefix-icon="heroicon-m-hashtag">
                <x-filament::input
                    wire:model.live.debounce.500ms="searchAccession"
                    placeholder="ACC-20260001"
                />
            </x-filament::input.wrapper>
        </div>

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
                    <x-filament::loading-indicator class="h-4 w-4" />
                    Menghubungi PACS...
                </div>
            </div>
        </form>
    </x-filament::section>

    @if($error)
        <x-filament::section>
            <div class="flex items-center gap-2 text-sm text-danger-700">
                <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                {{ $error }}
            </div>
        </x-filament::section>
    @endif

    @if($searched && !$error)
        <div class="flex items-center justify-between text-sm text-gray-500 mt-2 mb-3">
            <span>{{ count($studies) }} studi ditemukan</span>
            @if(count($studies) > 0)
                <span class="text-xs text-gray-400">Halaman {{ $page }}</span>
            @endif
        </div>
    @endif

    @if($searched && count($studies) === 0 && !$error)
        <x-filament::section>
            <div class="text-center py-10 text-gray-400">
                <x-filament::icon name="heroicon-o-document-magnifying-glass" class="w-10 h-10 mx-auto mb-2" />
                <p class="font-medium text-gray-500">Tidak ada studi yang cocok.</p>
                <p class="text-sm mt-1">Coba kata kunci lain, atau periksa kembali Patient ID / Accession Number.</p>
            </div>
        </x-filament::section>
    @endif

    @if(count($studies) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($studies as $study)
                <x-study-card :study="$study" />
            @endforeach
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
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
</x-filament-panels::page>
