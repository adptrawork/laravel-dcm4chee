<x-filament-panels::page>
    @if($error)
        <x-filament::section>
            <div class="flex items-center gap-2 text-sm text-danger-700">
                <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                {{ $error }}
            </div>
            <a href="{{ url('/admin/study-browser') }}" class="text-primary-600 hover:underline text-sm">&larr; Kembali</a>
        </x-filament::section>
    @elseif(!empty($study))
        <x-filament::tabs>
            <x-filament::tabs.item wire:click="$set('activeTab', 'overview')" :active="$activeTab === 'overview'" icon="heroicon-o-information-circle">
                Overview
            </x-filament::tabs.item>
            <x-filament::tabs.item wire:click="$set('activeTab', 'series')" :active="$activeTab === 'series'" icon="heroicon-o-list-bullet" :badge="count($series)">
                Series
            </x-filament::tabs.item>
            <x-filament::tabs.item wire:click="$set('activeTab', 'metadata')" :active="$activeTab === 'metadata'" icon="heroicon-o-code-bracket">
                Metadata
            </x-filament::tabs.item>
        </x-filament::tabs>

        @if($activeTab === 'overview')
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-filament::section>
                    <x-slot name="heading">Patient</x-slot>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Name</dt>
                            <dd class="font-medium">{{ \App\Services\Dcm4chee\DicomHelper::formatPatientName($study['patientName']) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Patient ID</dt>
                            <dd class="font-mono">{{ $study['patientId'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Study</x-slot>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Date</dt>
                            <dd>{{ \App\Services\Dcm4chee\DicomHelper::formatStudyDate($study['studyDate']) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Accession</dt>
                            <dd class="font-mono">{{ $study['accessionNumber'] ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Modality</dt>
                            <dd class="flex gap-1">
                                @forelse(($study['modalities'] ?? []) as $mod)
                                    <x-modality-badge :label="$mod" :color="\App\Services\Dcm4chee\DicomHelper::modalityColor($mod)" />
                                @empty
                                    <span>-</span>
                                @endforelse
                            </dd>
                        </div>
                        @if($study['studyDescription'] ?? null)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Description</dt>
                            <dd class="truncate max-w-[250px]">{{ $study['studyDescription'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Referring</dt>
                            <dd>{{ isset($study['referringPhysician']) ? \App\Services\Dcm4chee\DicomHelper::formatPatientName($study['referringPhysician']) : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Series / Instances</dt>
                            <dd>{{ $study['series'] }} / {{ $study['instances'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Study UID</dt>
                            <dd class="font-mono text-xs truncate max-w-[300px]">{{ $study['studyUid'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
            </div>

            <div class="mt-4">
                @if($study['studyUid'] ?? null)
                    <x-viewer-button :studyUid="$study['studyUid']" />
                @endif
            </div>
        @endif

        @if($activeTab === 'series')
            <div class="mt-4 flex gap-4">
                <div class="{{ $selectedSeriesUid ? 'w-1/2' : 'w-full' }}">
                    <x-filament::section>
                        <x-slot name="heading">Series</x-slot>
                        @if(count($series) > 0)
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">#</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Modality</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-24">Instances</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($series as $s)
                                        <tr class="hover:bg-gray-50 cursor-pointer {{ $selectedSeriesUid === $s['seriesUid'] ? 'bg-primary-50' : '' }}"
                                            wire:click="selectSeries('{{ $s['seriesUid'] }}')">
                                            <td class="px-3 py-2.5 font-mono text-sm">{{ $s['seriesNumber'] ?? '-' }}</td>
                                            <td class="px-3 py-2.5 text-sm">{{ $s['seriesDescription'] ?? '-' }}</td>
                                            <td class="px-3 py-2.5">
                                                <x-modality-badge :label="$s['modality'] ?? '?'" :color="\App\Services\Dcm4chee\DicomHelper::modalityColor($s['modality'] ?? '')" />
                                            </td>
                                            <td class="px-3 py-2.5 text-sm text-right tabular-nums">{{ $s['instances'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-400 text-sm py-4 text-center">No series found</p>
                        @endif
                    </x-filament::section>
                </div>

                @if($selectedSeriesUid)
                    <div class="w-1/2">
                        <x-filament::section>
                            <x-slot name="heading">Instances</x-slot>
                            @if(count($instances) > 0)
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">SOP Class</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-20">Frames</th>
                                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase w-10">DL</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($instances as $inst)
                                            @php
                                                $downloadUrl = isset($server) && $inst['instanceUid']
                                                    ? $server->wado_uri . '?requestType=WADO&studyUID=' . urlencode($study['studyUid'] ?? '') . '&seriesUID=' . urlencode($selectedSeriesUid) . '&objectUID=' . urlencode($inst['instanceUid']) . '&contentType=application/dicom'
                                                    : null;
                                            @endphp
                                            <tr class="hover:bg-gray-50 cursor-pointer {{ $selectedInstanceUid === $inst['instanceUid'] ? 'bg-primary-50' : '' }}"
                                                wire:click="selectInstance('{{ $inst['instanceUid'] }}')">
                                                <td class="px-3 py-2.5 font-mono text-sm">{{ $inst['instanceNumber'] ?? '-' }}</td>
                                                <td class="px-3 py-2.5 text-xs text-gray-500 truncate max-w-[180px]">{{ $inst['sopClassUid'] ?? '-' }}</td>
                                                <td class="px-3 py-2.5 text-sm text-right tabular-nums">{{ $inst['numberOfFrames'] ?? '1' }}</td>
                                                <td class="px-2 py-2.5 text-right">
                                                    @if($downloadUrl)
                                                        <a href="{{ $downloadUrl }}" target="_blank" class="text-primary-600 hover:text-primary-800" title="Download DICOM">
                                                            <x-filament::icon name="heroicon-o-arrow-down-tray" class="w-4 h-4" />
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-400 text-sm py-4 text-center">No instances found</p>
                            @endif
                        </x-filament::section>

                        @if($selectedInstanceUid && !empty($instanceMetadata))
                            <div class="mt-4">
                                <x-filament::section>
                                    <x-slot name="heading">
                                        <div class="flex items-center gap-3">
                                            <span>Instance #{{ $instanceMetadata['00200013']['Value'][0] ?? '' }} Tags</span>
                                            <a href="javascript:void(0)" wire:click="$set('activeTab', 'instance-metadata')" class="text-xs text-primary-600 hover:underline">Full View →</a>
                                        </div>
                                    </x-slot>
                                    <div class="max-h-[400px] overflow-y-auto">
                                        @php $tags = array_slice($instanceMetadata, 0, 20); @endphp
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="border-b border-gray-200">
                                                    <th class="px-2 py-1 text-left font-medium text-gray-500 w-28">Tag</th>
                                                    <th class="px-2 py-1 text-left font-medium text-gray-500">Name</th>
                                                    <th class="px-2 py-1 text-left font-medium text-gray-500">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($tags as $tag => $val)
                                                    @php
                                                        $v = $val['Value'][0] ?? null;
                                                        $v = is_array($v) ? (is_string($v['Alphabetic'] ?? null) ? $v['Alphabetic'] : json_encode($v)) : (is_string($v) ? $v : json_encode($v));
                                                    @endphp
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-2 py-1.5 font-mono">{{ $tag }}</td>
                                                        <td class="px-2 py-1.5">{{ $val['name'] ?? '-' }}</td>
                                                        <td class="px-2 py-1.5 truncate max-w-[200px]">{{ $v }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if(count($instanceMetadata) > 20)
                                            <p class="text-center text-gray-400 mt-2">
                                                +{{ count($instanceMetadata) - 20 }} more tags
                                            </p>
                                        @endif
                                    </div>
                                </x-filament::section>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'instance-metadata' && $selectedInstanceUid)
            <div class="mt-4">
                <x-filament::tabs>
                    <x-filament::tabs.item wire:click="$set('metaSubTab', 'formatted')" :active="($metaSubTab ?? 'formatted') === 'formatted'" icon="heroicon-o-table-cells">
                        Formatted
                    </x-filament::tabs.item>
                    <x-filament::tabs.item wire:click="$set('metaSubTab', 'raw')" :active="($metaSubTab ?? 'formatted') === 'raw'" icon="heroicon-o-code-bracket">
                        Raw JSON
                    </x-filament::tabs.item>
                </x-filament::tabs>

                <div class="mt-2">
                    @if(($metaSubTab ?? 'formatted') === 'formatted')
                        <x-filament::section>
                            <x-slot name="heading">All DICOM Tags</x-slot>
                            <div class="max-h-[600px] overflow-y-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-200 sticky top-0 bg-white">
                                            <th class="px-2 py-1 text-left font-medium text-gray-500 w-28">Tag</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-500">Name</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-500">VR</th>
                                            <th class="px-2 py-1 text-left font-medium text-gray-500">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($instanceMetadata as $tag => $val)
                                            @php
                                                $v = $val['Value'][0] ?? null;
                                                $v = is_array($v) ? (is_string($v['Alphabetic'] ?? null) ? $v['Alphabetic'] : json_encode($v)) : (is_string($v) ? $v : json_encode($v));
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-1.5 font-mono">{{ $tag }}</td>
                                                <td class="px-2 py-1.5">{{ $val['name'] ?? '-' }}</td>
                                                <td class="px-2 py-1.5">{{ $val['vr'] ?? '-' }}</td>
                                                <td class="px-2 py-1.5 break-all max-w-[300px]">{{ $v }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-filament::section>
                    @else
                        <x-filament::section>
                            <x-slot name="heading">Raw DICOM JSON</x-slot>
                            <pre class="text-xs text-gray-700 bg-gray-50 p-4 rounded-lg overflow-x-auto max-h-[600px] leading-relaxed">{{ $instanceRawJson }}</pre>
                        </x-filament::section>
                    @endif
                </div>
            </div>
        @endif

        @if($activeTab === 'metadata')
            <div class="mt-4">
                <x-filament::section>
                    <x-slot name="heading">Raw DICOM JSON</x-slot>
                    <pre class="text-xs text-gray-700 bg-gray-50 p-4 rounded-lg overflow-x-auto max-h-[600px] leading-relaxed">{{ $rawJson }}</pre>
                </x-filament::section>
            </div>
        @endif
    @endif
</x-filament-panels::page>