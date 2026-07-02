<x-app-layout>
    @section('title', 'Patients')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Patient Management</x-ui.heading>
            <div class="flex items-center gap-3">
                <form action="{{ route('patients.set-server') }}" method="POST">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()"
                        class="rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('patients.create') }}">
                    <x-ui.button variant="primary" size="sm">+ Add Patient</x-ui.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name</label>
                        <x-ui.input name="patientName" value="{{ $query['patientName'] ?? '' }}" placeholder="Smith^John" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient ID</label>
                        <x-ui.input name="patientId" value="{{ $query['patientId'] ?? '' }}" placeholder="MRN12345" />
                    </div>
                    <div class="flex items-end gap-2">
                        <x-ui.button type="submit" variant="primary">Search</x-ui.button>
                        <a href="{{ route('patients.index') }}"><x-ui.button type="button" variant="outline">Clear</x-ui.button></a>
                    </div>
                </form>
            </div>

            @if(!empty($patients))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <p class="text-sm text-gray-500">{{ count($patients) }} patient(s) found</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Birth Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sex</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($patients as $p)
                                    @php $flat = \App\Services\Dcm4chee\DicomHelper::flattenPatient($p) @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 font-mono text-sm text-gray-500">{{ $flat['PatientID'] ?? '-' }}</td>
                                        <td class="px-4 py-3 font-medium text-sm">{{ $flat['PatientName'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $flat['PatientBirthDate'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $flat['PatientSex'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('patients.show', $flat['PatientID']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View Studies</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No patients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif(request()->hasAny(['patientName', 'patientId']))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500">No patients found matching your criteria.</p>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-500">Search for patients using the form above.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
