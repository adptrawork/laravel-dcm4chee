<x-app-layout>
    @section('title', 'Patient: ' . ($patient['PatientID'] ?? 'Unknown'))
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <x-ui.heading level="h1" size="xl">{{ $patient['PatientName'] ?? 'Unknown' }}</x-ui.heading>
                <p class="text-sm text-gray-500">Patient ID: {{ $patient['PatientID'] ?? '-' }}</p>
            </div>
            <a href="{{ route('patients.index') }}">
                <x-ui.button variant="outline" size="sm">&larr; Back</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Patient ID</p>
                    <p class="mt-1 text-lg font-mono font-semibold text-gray-900">{{ $patient['PatientID'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Birth Date</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $patient['PatientBirthDate'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Sex</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $patient['PatientSex'] ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <x-ui.heading level="h3" size="md">Studies ({{ count($studies) }})</x-ui.heading>
                </div>
                @if(!empty($studies))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study UID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalities</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($studies as $study)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500 max-w-xs truncate">{{ $study['StudyInstanceUID'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $study['StudyDate'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $study['StudyDescription'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $study['ModalitiesInStudy'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('studies.show', $study['StudyInstanceUID']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Series</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No studies found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No studies found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
