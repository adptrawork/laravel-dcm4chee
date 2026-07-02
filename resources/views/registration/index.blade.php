<x-app-layout>
    @section('title', 'Registrasi Pasien')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrasi Pasien</h2>
            <form action="{{ route('registration.set-server') }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if($errors->any())
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('registration.store') }}">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $serverId }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pasien</label>
                            <input type="text" name="patient_name" value="{{ old('patient_name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. RM</label>
                            <input type="text" name="patient_id" value="{{ old('patient_id') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="">--</option>
                                <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Perempuan</option>
                                <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dokter Pengirim</label>
                            <input type="text" name="requesting_physician" value="{{ old('requesting_physician') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Modality</label>
                            <select name="modality" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="DX" {{ old('modality') == 'DX' ? 'selected' : '' }}>DX - Radiography</option>
                                <option value="CT" {{ old('modality') == 'CT' ? 'selected' : '' }}>CT - Computed Tomography</option>
                                <option value="MR" {{ old('modality') == 'MR' ? 'selected' : '' }}>MR - Magnetic Resonance</option>
                                <option value="US" {{ old('modality') == 'US' ? 'selected' : '' }}>US - Ultrasound</option>
                                <option value="CR" {{ old('modality') == 'CR' ? 'selected' : '' }}>CR - Computed Radiography</option>
                                <option value="MG" {{ old('modality') == 'MG' ? 'selected' : '' }}>MG - Mammography</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pemeriksaan</label>
                            <input type="text" name="procedure_description" value="{{ old('procedure_description') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ruangan</label>
                            <input type="text" name="room" value="{{ old('room') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prioritas</label>
                            <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="routine" {{ old('priority') == 'routine' ? 'selected' : '' }}>Routine</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="stat" {{ old('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Periksa</label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jam Periksa</label>
                            <input type="time" name="scheduled_time" value="{{ old('scheduled_time', date('H:i')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex items-center space-x-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-medium">
                            Simpan
                        </button>
                        <p class="text-xs text-gray-400">Patient + MWL akan dibuat otomatis di DCM4CHEE.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
