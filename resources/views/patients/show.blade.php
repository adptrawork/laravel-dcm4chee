<x-app-layout>
    @section('title', 'Patient: ' . ($patient['PatientID'] ?? 'Unknown'))
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $patient['PatientName'] ?? 'Unknown' }}</h2>
                <p class="text-sm text-gray-500">Patient ID: {{ $patient['PatientID'] ?? '-' }}</p>
            </div>
            <a href="{{ route('patients.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Patient ID</p>
                    <p class="mt-1 text-lg font-mono font-semibold text-gray-900">{{ $patient['PatientID'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Birth Date</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $patient['PatientBirthDate'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Sex</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $patient['PatientSex'] ?? '-' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Studies ({{ count($studies) }})</h3>
                </div>
                @if(!empty($studies))
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Study UID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalities</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($studies as $study)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono text-gray-500 max-w-xs truncate">{{ $study['StudyInstanceUID'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $study['StudyDate'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $study['StudyDescription'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $study['ModalitiesInStudy'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('studies.show', $study['StudyInstanceUID']) }}" class="text-sm text-blue-600 hover:text-blue-800">View Series</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No studies found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
