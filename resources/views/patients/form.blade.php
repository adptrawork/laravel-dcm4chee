<x-app-layout>
    @section('title', 'Add Patient')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Patient</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if(session('preview'))
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">DICOM JSON Preview</h3>
                        <pre class="text-xs bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>{{ json_encode(session('preview'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        <div class="mt-4 flex items-center space-x-3">
                            <form method="POST" action="{{ route('patients.store') }}">
                                @csrf
                                <input type="hidden" name="server_id" value="{{ old('server_id', $serverId) }}">
                                <input type="hidden" name="patientName" value="{{ old('patientName') }}">
                                <input type="hidden" name="patientId" value="{{ old('patientId') }}">
                                <input type="hidden" name="patientBirthDate" value="{{ old('patientBirthDate') }}">
                                <input type="hidden" name="patientSex" value="{{ old('patientSex') }}">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">Confirm Submit</button>
                            </form>
                            <a href="{{ route('patients.create') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="server_id" class="block text-sm font-medium text-gray-700 mb-1">Target Server</label>
                            <select name="server_id" id="server_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ $srv->id == old('server_id', $serverId) ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                            @error('server_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="patientName" class="block text-sm font-medium text-gray-700 mb-1">Patient Name <span class="text-red-500">*</span></label>
                                <input type="text" id="patientName" name="patientName" value="{{ old('patientName') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="Smith^John" required>
                                <p class="mt-1 text-xs text-gray-400">Format: Last^First Middle</p>
                                @error('patientName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="patientId" class="block text-sm font-medium text-gray-700 mb-1">Patient ID <span class="text-red-500">*</span></label>
                                <input type="text" id="patientId" name="patientId" value="{{ old('patientId') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="MRN12345" required>
                                @error('patientId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="patientBirthDate" class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                                <input type="date" id="patientBirthDate" name="patientBirthDate" value="{{ old('patientBirthDate') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @error('patientBirthDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="patientSex" class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                                <select id="patientSex" name="patientSex" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">-- Select --</option>
                                    <option value="M" {{ old('patientSex') == 'M' ? 'selected' : '' }}>Male (M)</option>
                                    <option value="F" {{ old('patientSex') == 'F' ? 'selected' : '' }}>Female (F)</option>
                                    <option value="O" {{ old('patientSex') == 'O' ? 'selected' : '' }}>Other (O)</option>
                                </select>
                                @error('patientSex') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('patients.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                            <div class="flex items-center space-x-3">
                                <button type="submit" name="preview" value="1" class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                                    Preview JSON
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                    Submit to PACS
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">DICOM JSON Format</h4>
                <p class="text-xs text-gray-500">Data will be sent to the PACS as DICOM JSON using these tag mappings:</p>
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
