<x-filament-panels::page>
    @if($error)
        <x-filament::section>
            <div class="flex items-center gap-2 text-sm text-danger-700">
                <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                {{ $error }}
            </div>
            <a href="{{ url('/admin/study-browser') }}" class="text-primary-600 hover:underline text-sm">&larr; Kembali</a>
        </x-filament::section>
    @elseif($study)
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
                            <dd class="font-medium">{{ $study->formattedPatientName() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Patient ID</dt>
                            <dd class="font-mono">{{ $study->patientId ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Study</x-slot>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Date</dt>
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
                            <dd class="truncate max-w-[250px]">{{ $study->studyDescription }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Referring</dt>
                            <dd>{{ $study->referringPhysician ? \App\Services\Dcm4chee\DicomHelper::formatPatientName($study->referringPhysician) : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Series / Instances</dt>
                            <dd>{{ $study->series }} / {{ $study->instances }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Study UID</dt>
                            <dd class="font-mono text-xs truncate max-w-[300px]">{{ $study->studyUid ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
            </div>

            <div class="mt-4">
                <x-viewer-button :studyUid="$study->studyUid" />
            </div>
        @endif

        @if($activeTab === 'series')
            <div class="mt-4">
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
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 font-mono text-sm">{{ $s->seriesNumber ?? '-' }}</td>
                                        <td class="px-3 py-2.5 text-sm">{{ $s->seriesDescription ?? '-' }}</td>
                                        <td class="px-3 py-2.5">
                                            <x-modality-badge :label="$s->modality ?? '?'" :color="$s->modalityColor()" />
                                        </td>
                                        <td class="px-3 py-2.5 text-sm text-right tabular-nums">{{ $s->instances }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-400 text-sm py-4 text-center">No series found</p>
                    @endif
                </x-filament::section>
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