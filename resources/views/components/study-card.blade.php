@props(['study'])
@php
    $ohifUrl = $study->ohifUrl();
@endphp
<div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer"
     x-data="{ open: false }" x-on:click="open = true">
    <div class="flex items-start justify-between mb-2">
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-gray-900 truncate">{{ $study->formattedPatientName() }}</p>
            <p class="text-xs font-mono text-gray-400">{{ $study->patientId ?? '-' }}</p>
        </div>
        <div class="flex gap-1 flex-shrink-0 ml-2">
            @forelse($study->modalityColors() as $mod)
                <x-modality-badge :label="$mod['label']" :color="$mod['color']" />
            @empty
                <span class="text-xs text-gray-300">-</span>
            @endforelse
        </div>
    </div>

    @if($study->studyDescription)
        <p class="text-sm text-gray-600 truncate mb-2" title="{{ $study->studyDescription }}">{{ $study->studyDescription }}</p>
    @endif

    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
        <span>{{ $study->formattedStudyDate() }}</span>
        <span>{{ $study->series }} series</span>
        <span>{{ $study->instances }} images</span>
    </div>

    <div class="flex items-center gap-2">
        @if($ohifUrl)
            <a href="{{ $ohifUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-primary-600 bg-primary-50 hover:bg-primary-100 transition-colors"
               x-on:click.stop>
                <x-filament::icon name="heroicon-o-eye" class="w-3.5 h-3.5" />
                Buka di OHIF
            </a>
        @endif
        <button x-on:click.stop="open = true"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
            <x-filament::icon name="heroicon-o-information-circle" class="w-3.5 h-3.5" />
            Detail
        </button>
        @if($study->studyUid)
            <a href="{{ url('/admin/studies/' . $study->studyUid) }}"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-gray-500 bg-gray-50 hover:bg-gray-100 transition-colors">
                <x-filament::icon name="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                Full Page
            </a>
        @endif
    </div>

    {{-- Quick view modal --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         x-on:click.away="open = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto"
             x-on:click.stop>
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Study Detail</h3>
                    <button x-on:click="open = false" class="text-gray-400 hover:text-gray-600">
                        <x-filament::icon name="heroicon-o-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Patient</dt>
                        <dd class="font-medium text-gray-900">{{ $study->formattedPatientName() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Patient ID</dt>
                        <dd class="font-mono text-gray-900">{{ $study->patientId ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Study Date</dt>
                        <dd>{{ $study->formattedStudyDate() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Accession</dt>
                        <dd class="font-mono">{{ $study->accessionNumber ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Modality</dt>
                        <dd class="flex gap-1">
                            @forelse($study->modalityColors() as $mod)
                                <x-modality-badge :label="$mod['label']" :color="$mod['color']" />
                            @empty
                                <span>-</span>
                            @endforelse
                        </dd>
                    </div>
                    @if($study->studyDescription)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="text-right max-w-[200px] truncate">{{ $study->studyDescription }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Referring</dt>
                        <dd>{{ $study->referringPhysician ? \App\Services\Dcm4chee\DicomHelper::formatPatientName($study->referringPhysician) : '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Study UID</dt>
                        <dd class="font-mono text-xs max-w-[250px] truncate">{{ $study->studyUid ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Series / Instances</dt>
                        <dd>{{ $study->series }} / {{ $study->instances }}</dd>
                    </div>
                </dl>

                @if($ohifUrl)
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ $ohifUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors w-full justify-center">
                            <x-filament::icon name="heroicon-o-eye" class="w-4 h-4" />
                            Open in OHIF Viewer
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>