<x-app-layout>
    @section('title', 'Patients')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patient Management</h2>
            <div class="flex items-center space-x-3">
                <form action="{{ route('patients.set-server') }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>
                                {{ $srv->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    + Add Patient
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Search</button>
                        <a href="{{ route('patients.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
                    </div>
                </form>
            </div>

            @if(!empty($patients))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-600">{{ count($patients) }} patient(s) found</p>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Birth Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sex</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($patients as $p)
                                @php $flat = \App\Services\Dcm4chee\DicomHelper::flattenPatient($p) @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono">{{ $flat['PatientID'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $flat['PatientName'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $flat['PatientBirthDate'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $flat['PatientSex'] ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('patients.show', $flat['PatientID']) }}" class="text-sm text-blue-600 hover:text-blue-800">View Studies</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(!empty($patients) && !$query['patientName'] && !$query['patientId'])
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">No patients found in PACS.</p>
                </div>
            @elseif(request()->hasAny(['patientName', 'patientId']))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <p class="text-gray-500">No patients found matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
