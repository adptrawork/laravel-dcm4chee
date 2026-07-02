<x-app-layout>
    @section('title', 'Study Tracker - ' . $item->accession_number)
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <x-ui.heading level="h1" size="xl">Study Tracker</x-ui.heading>
                <p class="text-sm text-gray-500">Accession: <span class="font-mono">{{ $item->accession_number }}</span></p>
            </div>
            <a href="{{ route('study-tracker.index') }}">
                <x-ui.button variant="outline" size="sm">&larr; Back</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <x-ui.heading level="h2" size="md" class="mb-4">Patient Information</x-ui.heading>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $item->patient_name }}</span></div>
                    <div><span class="text-gray-500">Patient ID:</span> <span class="font-mono">{{ $item->patient_id }}</span></div>
                    <div><span class="text-gray-500">Modality:</span> <span>{{ $item->modality }}</span></div>
                    <div><span class="text-gray-500">Physician:</span> <span>{{ $item->requesting_physician ?? '-' }}</span></div>
                    <div class="col-span-2"><span class="text-gray-500">Examination:</span> <span>{{ $item->procedure_description }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <x-ui.heading level="h2" size="md" class="mb-4">Workflow Timeline</x-ui.heading>
                <div class="space-y-0">
                    @php
                        $steps = [
                            'registered' => 'Registered',
                            'mw_published' => 'MWL Published',
                            'taken_by_modality' => 'Taken by Modality',
                            'acquiring' => 'Acquiring',
                            'acquired' => 'Acquired',
                            'sent_to_pacs' => 'Sent to PACS',
                            'archived' => 'Archived',
                            'reported' => 'Reported',
                            'verified' => 'Verified',
                        ];
                        $timestamps = [
                            'registered' => $item->created_at,
                            'mw_published' => $item->sent_at,
                            'taken_by_modality' => $item->taken_at,
                            'acquired' => $item->acquired_at,
                            'archived' => $item->archived_at,
                            'reported' => $item->reported_at,
                            'verified' => $item->verified_at,
                        ];
                        $current = array_search($item->status, array_keys($steps));
                    @endphp

                    @foreach($steps as $key => $label)
                        @php
                            $idx = array_search($key, array_keys($steps));
                            $done = $idx <= $current;
                            $ts = $timestamps[$key] ?? null;
                        @endphp
                        <div class="flex items-start gap-4 pb-4 {{ !$loop->last ? 'border-l-2 border-gray-200 ml-2.5 pl-6' : 'ml-2.5 pl-6' }}">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center -ml-9 mt-0.5
                                {{ $done ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                @if($done)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <span class="text-xs">{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium {{ $done ? 'text-gray-900' : 'text-gray-400' }}">{{ $label }}</p>
                                @if($ts)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $ts instanceof \Carbon\Carbon ? $ts->format('d M Y H:i') : $ts }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if(!empty($study))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <x-ui.heading level="h2" size="md" class="mb-4">Study Details</x-ui.heading>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">Study UID:</span> <span class="font-mono text-xs">{{ $study['StudyInstanceUID'] ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Study Date:</span> <span>{{ $study['StudyDate'] ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Modality:</span> <span>{{ $study['ModalitiesInStudy'] ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Series:</span> <span>{{ $study['NumberOfStudyRelatedSeries'] ?? '-' }}</span></div>
                        <div><span class="text-gray-500">Instances:</span> <span>{{ $study['NumberOfStudyRelatedInstances'] ?? '-' }}</span></div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('studies.show', $study['StudyInstanceUID']) }}" target="_blank">
                            <x-ui.button variant="primary" size="sm">View in Study Browser</x-ui.button>
                        </a>
                    </div>
                </div>
            @elseif(in_array($item->status, ['sent_to_pacs', 'archived', 'reported', 'verified']))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center text-sm text-gray-400">
                    Study details not found in PACS.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
