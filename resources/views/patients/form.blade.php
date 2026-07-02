<x-app-layout>
    @section('title', 'Add Patient')
    <x-slot name="header">
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Patients</a>
        <x-ui.heading level="h1" size="xl">Add Patient</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('preview'))
                <div class="bg-gray-50 rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
                    <x-ui.heading level="h3" size="sm" class="mb-2">DICOM JSON Preview</x-ui.heading>
                    <pre class="text-xs bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>{{ json_encode(session('preview'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    <div class="mt-4 flex items-center gap-3">
                        <form method="POST" action="{{ route('patients.store') }}">
                            @csrf
                            <input type="hidden" name="server_id" value="{{ old('server_id', $serverId) }}">
                            <input type="hidden" name="patientName" value="{{ old('patientName') }}">
                            <input type="hidden" name="patientId" value="{{ old('patientId') }}">
                            <input type="hidden" name="patientBirthDate" value="{{ old('patientBirthDate') }}">
                            <input type="hidden" name="patientSex" value="{{ old('patientSex') }}">
                            <x-ui.button type="submit" variant="primary" color="green">Confirm Submit</x-ui.button>
                        </form>
                        <a href="{{ route('patients.create') }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="server_id" class="block text-sm font-medium text-gray-700 mb-1">Target Server</label>
                            <select name="server_id"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ $srv->id == old('server_id', $serverId) ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                            @error('server_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="patientName" class="block text-sm font-medium text-gray-700 mb-1">Patient Name <span class="text-red-500">*</span></label>
                                <x-ui.input id="patientName" name="patientName" value="{{ old('patientName') }}" required placeholder="Smith^John" />
                                <p class="mt-1 text-xs text-gray-400">Format: Last^First Middle</p>
                                @error('patientName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="patientId" class="block text-sm font-medium text-gray-700 mb-1">Patient ID <span class="text-red-500">*</span></label>
                                <x-ui.input id="patientId" name="patientId" value="{{ old('patientId') }}" required placeholder="MRN12345" />
                                @error('patientId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="patientBirthDate" class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                                <x-ui.input id="patientBirthDate" type="date" name="patientBirthDate" value="{{ old('patientBirthDate') }}" />
                                @error('patientBirthDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="patientSex" class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                                <select name="patientSex"
                                    class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <option value="">-- Select --</option>
                                    <option value="M" {{ old('patientSex') == 'M' ? 'selected' : '' }}>Male (M)</option>
                                    <option value="F" {{ old('patientSex') == 'F' ? 'selected' : '' }}>Female (F)</option>
                                    <option value="O" {{ old('patientSex') == 'O' ? 'selected' : '' }}>Other (O)</option>
                                </select>
                                @error('patientSex') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('patients.index') }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
                            <div class="flex items-center gap-3">
                                <x-ui.button type="submit" name="preview" value="1" variant="outline">Preview JSON</x-ui.button>
                                <x-ui.button type="submit" variant="primary">Submit to PACS</x-ui.button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-gray-50 rounded-xl border border-gray-200 shadow-sm p-4 mt-6">
                <x-ui.heading level="h4" size="sm" class="mb-2">DICOM JSON Format</x-ui.heading>
                <p class="text-xs text-gray-500">Data will be sent to the PACS as DICOM JSON.</p>
                <table class="mt-2 text-xs text-gray-600">
                    <tr><td class="pr-4 font-mono">00100010</td><td>Patient Name (PN)</td><td class="pl-4">→</td><td class="pl-4">PatientName field</td></tr>
                    <tr><td class="pr-4 font-mono">00100020</td><td>Patient ID (LO)</td><td class="pl-4">→</td><td class="pl-4">PatientID field</td></tr>
                    <tr><td class="pr-4 font-mono">00100030</td><td>Birth Date (DA)</td><td class="pl-4">→</td><td class="pl-4">BirthDate field</td></tr>
                    <tr><td class="pr-4 font-mono">00100040</td><td>Sex (CS)</td><td class="pl-4">→</td><td class="pl-4">Sex field</td></tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
