@props(['study'])
@php
    $studyUid = $study['studyUid'] ?? null;
    $ohifUrl = $studyUid ? env('OHIF_URL', 'http://localhost:3000') . '/viewer?StudyInstanceUIDs=' . $studyUid : null;
    $modalities = $study['modalities'] ?? [];
@endphp
<div class="bg-white rounded-lg border border-gray-200 hover:border-primary-300 hover:shadow-sm transition-all cursor-pointer"
     x-data="{ open: false }" x-on:click="open = true">
    <div class="p-4">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    @forelse($modalities as $mod)
                        <x-modality-badge :label="$mod" :color="\App\Services\Dcm4chee\DicomHelper::modalityColor($mod)" />
                    @empty
                        <span class="text-xs text-gray-300">-</span>
                    @endforelse
                </div>
                <p class="font-semibold text-gray-900 truncate">{{ \App\Services\Dcm4chee\DicomHelper::formatPatientName($study['patientName']) }}</p>
                <p class="text-xs font-mono text-gray-400">{{ $study['patientId'] ?? '-' }}</p>
            </div>
        </div>

        @if($study['studyDescription'] ?? null)
            <p class="text-xs text-gray-500 truncate mt-1.5" title="{{ $study['studyDescription'] }}">{{ $study['studyDescription'] }}</p>
        @endif

        <div class="flex items-center gap-3 text-xs text-gray-400 mt-2">
            <span class="flex items-center gap-1">
                <x-filament::icon name="heroicon-o-calendar" class="w-3.5 h-3.5" />
                {{ \App\Services\Dcm4chee\DicomHelper::formatStudyDate($study['studyDate']) }}
            </span>
            <span>{{ $study['series'] }} series / {{ $study['instances'] }} images</span>
        </div>

        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-gray-100">
            @if($ohifUrl)
                <a href="{{ $ohifUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors"
                   x-on:click.stop>
                    <x-filament::icon name="heroicon-o-eye" class="w-3.5 h-3.5" />
                    OHIF
                </a>
            @endif
            <button x-on:click.stop="open = true"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
                <x-filament::icon name="heroicon-o-information-circle" class="w-3.5 h-3.5" />
                Detail
            </button>
            @if($studyUid)
                <a href="{{ url('/admin/studies/' . $studyUid) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded text-xs font-medium text-gray-500 bg-gray-50 hover:bg-gray-100 transition-colors ml-auto">
                    <x-filament::icon name="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                </a>
            @endif
        </div>
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
                    <h3 class="text-lg font-semibold text-gray-900">{{ \App\Services\Dcm4chee\DicomHelper::formatPatientName($study['patientName']) }}</h3>
                    <button x-on:click="open = false" class="text-gray-400 hover:text-gray-600">
                        <x-filament::icon name="heroicon-o-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Patient ID</dt>
                        <dd class="font-mono font-medium">{{ $study['patientId'] ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Study Date</dt>
                        <dd>{{ \App\Services\Dcm4chee\DicomHelper::formatStudyDate($study['studyDate']) }}</dd>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Accession</dt>
                        <dd class="font-mono">{{ $study['accessionNumber'] ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Modality</dt>
                        <dd class="flex gap-1">
                            @forelse($modalities as $mod)
                                <x-modality-badge :label="$mod" :color="\App\Services\Dcm4chee\DicomHelper::modalityColor($mod)" />
                            @empty
                                <span>-</span>
                            @endforelse
                        </dd>
                    </div>
                    @if($study['studyDescription'] ?? null)
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="text-right max-w-[200px] truncate">{{ $study['studyDescription'] }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Referring</dt>
                        <dd>{{ isset($study['referringPhysician']) ? \App\Services\Dcm4chee\DicomHelper::formatPatientName($study['referringPhysician']) : '-' }}</dd>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-gray-50">
                        <dt class="text-gray-500">Study UID</dt>
                        <dd class="font-mono text-xs max-w-[250px] truncate">{{ $studyUid ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Series / Instances</dt>
                        <dd>{{ $study['series'] }} / {{ $study['instances'] }}</dd>
                    </div>
                </dl>

                @if($ohifUrl)
                    <div class="mt-5 pt-4 border-t border-gray-100 flex gap-2">
                        <a href="{{ $ohifUrl }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors flex-1 justify-center">
                            <x-filament::icon name="heroicon-o-eye" class="w-4 h-4" />
                            Open in OHIF
                        </a>
                        <a href="{{ url('/admin/studies/' . $studyUid) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
                            <x-filament::icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                            Full Page
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>