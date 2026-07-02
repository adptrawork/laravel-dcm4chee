<x-app-layout>
    @section('title', 'Study Detail')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <x-ui.heading level="h1" size="xl">Study Details</x-ui.heading>
                <p class="text-sm text-gray-500">{{ $study['StudyDescription'] ?? 'No description' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.show', $study['PatientID']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Patient</a>
                <a href="{{ route('studies.index') }}">
                    <x-ui.button variant="outline" size="sm">&larr; Back</x-ui.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Study Date</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['StudyDate'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Modalities</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['ModalitiesInStudy'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Accession #</p>
                    <p class="mt-1 text-sm font-mono font-semibold text-gray-900">{{ $study['AccessionNumber'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Instances</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['NumberOfStudyRelatedInstances'] ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <x-ui.heading level="h3" size="md">Series ({{ count($series) }})</x-ui.heading>
                </div>
                @if(!empty($series))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Modality</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Instances</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($series as $s)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $s['SeriesNumber'] ?? '-' }}</td>
                                        <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $s['Modality'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $s['SeriesDescription'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $s['NumberOfSeriesRelatedInstances'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('studies.series', [$studyUid, $s['SeriesInstanceUID']]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Instances</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No series found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No series found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
