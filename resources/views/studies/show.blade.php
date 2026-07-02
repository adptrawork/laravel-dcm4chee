<x-app-layout>
    @section('title', 'Study Detail')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Study Details</h2>
                <p class="text-sm text-gray-500">{{ $study['StudyDescription'] ?? 'No description' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('patients.show', $study['PatientID']) }}" class="text-sm text-blue-600 hover:text-blue-800">View Patient</a>
                <a href="{{ route('studies.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Study Date</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['StudyDate'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Modalities</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['ModalitiesInStudy'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Accession #</p>
                    <p class="mt-1 text-sm font-mono font-semibold text-gray-900">{{ $study['AccessionNumber'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Instances</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $study['NumberOfStudyRelatedInstances'] ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Series ({{ count($series) }})</h3>
                </div>
                @if(!empty($series))
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modality</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instances</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($series as $s)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $s['SeriesNumber'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-mono">{{ $s['Modality'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $s['SeriesDescription'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $s['NumberOfSeriesRelatedInstances'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('studies.series', [$studyUid, $s['SeriesInstanceUID']]) }}" class="text-sm text-blue-600 hover:text-blue-800">View Instances</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No series found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
