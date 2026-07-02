<x-app-layout>
    @section('title', 'Registrasi Pasien')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Registrasi Pasien</x-ui.heading>
            <form action="{{ route('registration.set-server') }}" method="POST">
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <form method="POST" action="{{ route('registration.store') }}">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $serverId }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                            <x-ui.input name="patient_name" value="{{ old('patient_name') }}" required placeholder="Nama lengkap" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. RM</label>
                            <x-ui.input name="patient_id" value="{{ old('patient_id') }}" required placeholder="Rekam medis" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <x-ui.input name="nik" value="{{ old('nik') }}" placeholder="Nomor KTP" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <x-ui.input type="date" name="birth_date" value="{{ old('birth_date') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="gender"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">--</option>
                                <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Perempuan</option>
                                <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <x-ui.input name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="address" rows="2"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Pengirim</label>
                            <x-ui.input name="requesting_physician" value="{{ old('requesting_physician') }}" placeholder="dr. Spesialis" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modality</label>
                            <select name="modality" required
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="DX" {{ old('modality') == 'DX' ? 'selected' : '' }}>DX - Radiography</option>
                                <option value="CT" {{ old('modality') == 'CT' ? 'selected' : '' }}>CT - Computed Tomography</option>
                                <option value="MR" {{ old('modality') == 'MR' ? 'selected' : '' }}>MR - Magnetic Resonance</option>
                                <option value="US" {{ old('modality') == 'US' ? 'selected' : '' }}>US - Ultrasound</option>
                                <option value="CR" {{ old('modality') == 'CR' ? 'selected' : '' }}>CR - Computed Radiography</option>
                                <option value="MG" {{ old('modality') == 'MG' ? 'selected' : '' }}>MG - Mammography</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan</label>
                            <x-ui.input name="procedure_description" value="{{ old('procedure_description') }}" required placeholder="Jenis pemeriksaan" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <x-ui.input name="room" value="{{ old('room') }}" placeholder="Ruang radiologi" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                            <select name="priority"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="routine" {{ old('priority') == 'routine' ? 'selected' : '' }}>Routine</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="stat" {{ old('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Periksa</label>
                            <x-ui.input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Periksa</label>
                            <x-ui.input type="time" name="scheduled_time" value="{{ old('scheduled_time', date('H:i')) }}" required />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                        <p class="text-xs text-gray-400">Patient + MWL akan dibuat otomatis di DCM4CHEE.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
