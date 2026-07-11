<x-filament-panels::page>
    @if($error)
        <div class="p-4 text-sm text-danger-700 bg-danger-50 rounded-lg mb-4">
            <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-5 h-5 inline mr-1" />
            {{ $error }}
        </div>
        <a href="{{ url('/admin/studies') }}" class="text-primary-600 hover:underline text-sm">&larr; Kembali ke Study Browser</a>
    @elseif($study)
        {{-- Tab navigation --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex gap-4">
                <button wire:click="$set('activeTab', 'overview')" class="pb-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'overview' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'series')" class="pb-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'series' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Series ({{ count($series) }})
                </button>
                <button wire:click="$set('activeTab', 'metadata')" class="pb-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'metadata' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Raw Metadata
                </button>
            </nav>
        </div>

        {{-- Overview tab --}}
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
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
                            <dd class="text-right max-w-[200px] truncate">{{ $study->studyDescription }}</dd>
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
                    </dl>
                </x-filament::section>
            </div>

            <div class="mt-4">
                <x-filament::section>
                    <x-slot name="heading">DICOM Identifiers</x-slot>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Study UID</dt>
                            <dd class="font-mono text-xs max-w-[350px] truncate">{{ $study->studyUid ?? '-' }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
            </div>

            <div class="mt-4">
                <x-viewer-button :studyUid="$study->studyUid" />
            </div>
        @endif

        {{-- Series tab --}}
        @if($activeTab === 'series')
            <x-filament::section>
                <x-slot name="heading">Series List</x-slot>
                @if(count($series) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Modality</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Instances</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($series as $s)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 font-mono">{{ $s->seriesNumber ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $s->seriesDescription ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            <x-modality-badge :label="$s->modality ?? '?'" :color="$s->modalityColor()" />
                                        </td>
                                        <td class="px-3 py-2 text-right">{{ $s->instances }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-sm py-4 text-center">No series found</p>
                @endif
            </x-filament::section>
        @endif

        {{-- Metadata tab --}}
        @if($activeTab === 'metadata')
            <x-filament::section>
                <x-slot name="heading">Raw DICOM JSON</x-slot>
                <pre class="text-xs text-gray-700 bg-gray-50 p-4 rounded-lg overflow-x-auto max-h-[600px]">{{ $rawJson }}</pre>
            </x-filament::section>
        @endif
    @endif
</x-filament-panels::page>