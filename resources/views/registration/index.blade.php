<x-app-layout>
    @section('title', 'Registrasi Pasien')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Registrasi Pemeriksaan</x-ui.heading>
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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            @if(session('success'))
                <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Step 1: Cari Pasien --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <x-ui.heading level="h2" size="sm" class="mb-4">1. Cari Pasien</x-ui.heading>

                <form method="POST" action="{{ route('registration.search-patients') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $serverId }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                        <x-ui.input name="search_name" value="{{ $query['search_name'] ?? '' }}" placeholder="Nama lengkap" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. RM / NIK</label>
                        <x-ui.input name="search_id" value="{{ $query['search_id'] ?? '' }}" placeholder="No. rekam medis" />
                    </div>
                    <div class="flex items-end gap-2">
                        <x-ui.button type="submit" variant="primary">Cari</x-ui.button>
                        <a href="{{ route('registration.index') }}"><x-ui.button type="button" variant="outline">Reset</x-ui.button></a>
                    </div>
                </form>

                {{-- Hasil Pencarian --}}
                @if(!empty($patients))
                    <div class="mt-4 overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">No. RM</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Nama</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Tgl Lahir</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">JK</th>
                                    <th class="px-3 py-2 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($patients as $p)
                                    @php $flat = \App\Services\Dcm4chee\DicomHelper::flattenPatient($p) @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ $flat['PatientID'] ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium">{{ $flat['PatientName'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $flat['PatientBirthDate'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $flat['PatientSex'] ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('registration.index', ['select_patient' => $flat['PatientID'], 'search_name' => $query['search_name'] ?? null, 'search_id' => $query['search_id'] ?? null]) }}"
                                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">Pilih</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif(($query['search_name'] ?? $query['search_id'] ?? null) && $serverId)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                        <p class="text-sm text-gray-500 mb-3">Pasien tidak ditemukan.</p>
                        <button type="button" onclick="showNewPatientForm()"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">+ Daftarkan sebagai Pasien Baru</button>
                    </div>
                @endif
            </div>

            {{-- Step 2: Data Pasien + Pemeriksaan --}}
            <form method="POST" action="{{ route('registration.store') }}" id="registrationForm">
                @csrf
                <input type="hidden" name="server_id" value="{{ $serverId }}">
                <input type="hidden" name="action" id="actionType" value="{{ $selectedPatient ? 'existing' : '' }}">

                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    @if($selectedPatient)
                        <input type="hidden" name="patient_id" value="{{ $selectedPatient['PatientID'] }}">
                        <input type="hidden" name="patient_name" value="{{ $selectedPatient['PatientName'] ?? '' }}">
                        <input type="hidden" name="gender" value="{{ $selectedPatient['PatientSex'] ?? '' }}">
                        <input type="hidden" name="birth_date" value="{{ $selectedPatient['PatientBirthDate'] ?? '' }}">

                        <x-ui.heading level="h2" size="sm" class="mb-4">2. Data Pasien</x-ui.heading>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div>
                                <p class="text-xs text-gray-500">No. RM</p>
                                <p class="font-semibold text-sm">{{ $selectedPatient['PatientID'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Nama</p>
                                <p class="font-semibold text-sm">{{ $selectedPatient['PatientName'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tgl Lahir</p>
                                <p class="font-semibold text-sm">{{ $selectedPatient['PatientBirthDate'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                <p class="font-semibold text-sm">{{ $selectedPatient['PatientSex'] ?? '-' }}</p>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">
                    @endif

                    {{-- Inline New Patient Form --}}
                    <div id="newPatientSection" class="{{ $selectedPatient ? 'hidden' : '' }}">
                        <x-ui.heading level="h2" size="sm" class="mb-4">2. Data Pasien Baru</x-ui.heading>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien <span class="text-red-500">*</span></label>
                                <x-ui.input name="new_patient_name" id="newPatientName" placeholder="Nama lengkap" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. RM <span class="text-red-500">*</span></label>
                                <x-ui.input name="new_patient_id" id="newPatientId" placeholder="Rekam medis" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                <x-ui.input name="nik" placeholder="Nomor KTP" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <x-ui.input type="date" name="birth_date" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="gender"
                                    class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <option value="">--</option>
                                    <option value="M">Laki-laki</option>
                                    <option value="F">Perempuan</option>
                                    <option value="O">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                                <x-ui.input name="phone" placeholder="08xxxxxxxxxx" />
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="address" rows="1"
                                    class="w-full rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                            </div>
                        </div>
                        <hr class="my-6 border-gray-200">
                    </div>

                    {{-- Step 3: Data Pemeriksaan --}}
                    <x-ui.heading level="h2" size="sm" class="mb-4">3. Data Pemeriksaan</x-ui.heading>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Pengirim</label>
                            <x-ui.input name="requesting_physician" value="{{ old('requesting_physician', $mwlConfig->default_physician ?? '') }}" placeholder="dr. Spesialis" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                            <select name="template_id" onchange="applyTemplate(this)"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">-- Pilih Template --</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}"
                                        data-modality="{{ $t->modality }}"
                                        data-desc="{{ $t->description }}"
                                        data-room="{{ $t->room }}"
                                        data-priority="{{ $t->priority }}">{{ $t->name }} ({{ $t->modality }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modality <span class="text-red-500">*</span></label>
                            <select name="modality" required
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="DX" {{ old('modality', $mwlConfig->default_modality ?? '') == 'DX' ? 'selected' : '' }}>DX - Radiography</option>
                                <option value="CT" {{ old('modality', $mwlConfig->default_modality ?? '') == 'CT' ? 'selected' : '' }}>CT - Computed Tomography</option>
                                <option value="MR" {{ old('modality', $mwlConfig->default_modality ?? '') == 'MR' ? 'selected' : '' }}>MR - Magnetic Resonance</option>
                                <option value="US" {{ old('modality', $mwlConfig->default_modality ?? '') == 'US' ? 'selected' : '' }}>US - Ultrasound</option>
                                <option value="CR" {{ old('modality', $mwlConfig->default_modality ?? '') == 'CR' ? 'selected' : '' }}>CR - Computed Radiography</option>
                                <option value="MG" {{ old('modality', $mwlConfig->default_modality ?? '') == 'MG' ? 'selected' : '' }}>MG - Mammography</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan <span class="text-red-500">*</span></label>
                            <x-ui.input name="procedure_description" value="{{ old('procedure_description') }}" required placeholder="Jenis pemeriksaan" id="procDesc" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                            <x-ui.input name="room" value="{{ old('room', $mwlConfig->default_room ?? '') }}" placeholder="Ruang radiologi" id="roomField" />
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Periksa <span class="text-red-500">*</span></label>
                            <x-ui.input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Periksa <span class="text-red-500">*</span></label>
                            <x-ui.input type="time" name="scheduled_time" value="{{ old('scheduled_time', date('H:i')) }}" required />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-ui.button type="submit" variant="primary">Daftarkan</x-ui.button>
                        <p class="text-xs text-gray-400">MWL akan dibuat otomatis di DCM4CHEE.</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showNewPatientForm() {
            document.getElementById('newPatientSection').classList.remove('hidden');
            document.getElementById('actionType').value = 'new';
            document.getElementById('newPatientSection').scrollIntoView({ behavior: 'smooth' });
        }

        function applyTemplate(select) {
            const option = select.options[select.selectedIndex];
            if (!option.value) return;
            if (option.dataset.modality) {
                document.querySelector('select[name="modality"]').value = option.dataset.modality;
            }
            if (option.dataset.desc) {
                document.getElementById('procDesc').value = option.dataset.desc;
            }
            if (option.dataset.room) {
                document.getElementById('roomField').value = option.dataset.room;
            }
            if (option.dataset.priority) {
                document.querySelector('select[name="priority"]').value = option.dataset.priority;
            }
        }

        // Intercept form submit: if no patient selected and new patient form visible, set action=new
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const action = document.getElementById('actionType').value;
            if (!action) {
                const newSection = document.getElementById('newPatientSection');
                if (!newSection.classList.contains('hidden')) {
                    document.getElementById('actionType').value = 'new';
                } else {
                    e.preventDefault();
                    alert('Cari dan pilih pasien terlebih dahulu, atau buka form Pasien Baru.');
                    return;
                }
            }
            // Remap new patient field names for the controller
            const actionType = document.getElementById('actionType').value;
            if (actionType === 'new') {
                // Copy values from new patient fields to main fields
                const nameVal = document.getElementById('newPatientName').value;
                const idVal = document.getElementById('newPatientId').value;
                if (!nameVal || !idVal) {
                    e.preventDefault();
                    alert('Isi nama dan No. RM pasien baru.');
                    return;
                }
                // Add hidden fields with correct names
                const form = this;
                const h1 = document.createElement('input'); h1.type = 'hidden'; h1.name = 'patient_name'; h1.value = nameVal; form.appendChild(h1);
                const h2 = document.createElement('input'); h2.type = 'hidden'; h2.name = 'new_patient_id'; h2.value = idVal; form.appendChild(h2);
            }
            if (actionType === 'existing' && !document.querySelector('input[name="patient_id"]')) {
                e.preventDefault();
                alert('Pasien belum dipilih.');
                return;
            }
        });

        // Show new patient form if validation errors exist and no patient selected
        @if(old('action') === 'new' && !$selectedPatient)
            document.addEventListener('DOMContentLoaded', function() {
                showNewPatientForm();
            });
        @endif
    </script>
</x-app-layout>
