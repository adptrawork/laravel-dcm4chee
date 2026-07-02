<x-app-layout>
    @section('title', 'Create MWL')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Modality Worklist (MWL)</h2>
            <form action="{{ route('debug.set-server') }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}" {{ $srv->id == ($serverId ?? null) ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if (session('success'))
                    <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mwl.create') }}">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $serverId }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_name" class="block text-sm font-medium text-gray-700">Nama Pasien</label>
                            <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">No. RM</label>
                            <input type="text" name="patient_id" id="patient_id" value="{{ old('patient_id') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="modality" class="block text-sm font-medium text-gray-700">Modalitas</label>
                            <select name="modality" id="modality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="DX" {{ old('modality') == 'DX' ? 'selected' : '' }}>DX (Digital Radiography)</option>
                                <option value="CT" {{ old('modality') == 'CT' ? 'selected' : '' }}>CT (Computed Tomography)</option>
                                <option value="MR" {{ old('modality') == 'MR' ? 'selected' : '' }}>MR (Magnetic Resonance)</option>
                                <option value="US" {{ old('modality') == 'US' ? 'selected' : '' }}>US (Ultrasound)</option>
                                <option value="CR" {{ old('modality') == 'CR' ? 'selected' : '' }}>CR (Computed Radiography)</option>
                                <option value="MG" {{ old('modality') == 'MG' ? 'selected' : '' }}>MG (Mammography)</option>
                            </select>
                        </div>
                        <div>
                            <label for="procedure_description" class="block text-sm font-medium text-gray-700">Pemeriksaan</label>
                            <input type="text" name="procedure_description" id="procedure_description" value="{{ old('procedure_description') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label for="requesting_physician" class="block text-sm font-medium text-gray-700">Dokter Pengirim</label>
                            <input type="text" name="requesting_physician" id="requesting_physician" value="{{ old('requesting_physician') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Tanggal Dijadwalkan</label>
                            <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Jam Dijadwalkan</label>
                            <input type="time" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time', date('H:i')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-medium">
                            Create MWL Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
