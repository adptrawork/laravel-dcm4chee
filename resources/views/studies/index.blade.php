<x-app-layout>
    @section('title', 'Study Browser')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Study Browser</x-ui.heading>
            <form action="{{ route('patients.set-server') }}" method="POST">
                @csrf
                <select name="server_id" onchange="this.form.submit()"
                    class="rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name</label>
                        <x-ui.input name="patientName" value="{{ $query['patientName'] ?? '' }}" placeholder="Smith^John" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                        <x-ui.input name="patientId" value="{{ $query['patientId'] ?? '' }}" placeholder="MRN12345" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Study Date</label>
                        <x-ui.input name="studyDate" value="{{ $query['studyDate'] ?? '' }}" placeholder="20200101-20201231" />
                    </div>
                    <div class="flex items-end gap-2">
                        <x-ui.button type="submit" variant="primary">Search</x-ui.button>
                        <a href="{{ route('studies.index') }}"><x-ui.button type="button" variant="outline">Clear</x-ui.button></a>
                    </div>
                </form>
            </div>

            @if(!empty($studies))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-500">{{ count($studies) }} study(ies) found</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Modality</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[130px]">Accession</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($studies as $study)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-sm text-gray-900">{{ $study['PatientName'] ?? $query['patientName'] ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $study['PatientID'] ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $study['StudyDate'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $study['StudyDescription'] ?? '-' }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $study['ModalitiesInStudy'] ?? '-' }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $study['AccessionNumber'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('studies.show', $study['StudyInstanceUID']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Series</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">No studies found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif(!$query['patientName'] && !$query['patientId'] && !$query['studyDate'])
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500">Search for studies using the form above.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500">No studies found matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
