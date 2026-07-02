<x-app-layout>
    @section('title', 'Study Browser')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Study Browser</h2>
            <div class="flex items-center space-x-3">
                <form action="{{ route('patients.set-server') }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name</label>
                        <input type="text" name="patientName" value="{{ $query['patientName'] ?? '' }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Smith^John">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                        <input type="text" name="patientId" value="{{ $query['patientId'] ?? '' }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="MRN12345">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Study Date</label>
                        <input type="text" name="studyDate" value="{{ $query['studyDate'] ?? '' }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="20200101-20201231">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Search</button>
                        <a href="{{ route('studies.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
                    </div>
                </form>
            </div>

            @if(!empty($studies))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-600">{{ count($studies) }} study(ies) found</p>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modality</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Accession</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($studies as $study)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">
                                        <div class="font-medium text-gray-900">{{ $study['PatientName'] ?? $query['patientName'] ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $study['PatientID'] ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $study['StudyDate'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $study['StudyDescription'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-mono">{{ $study['ModalitiesInStudy'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $study['AccessionNumber'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('studies.show', $study['StudyInstanceUID']) }}" class="text-sm text-blue-600 hover:text-blue-800">View Series</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(!$query['patientName'] && !$query['patientId'] && !$query['studyDate'])
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">No studies found in PACS.</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">No studies found matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
