<x-app-layout>
    @section('title', 'DICOM Tools')
    <x-slot name="header">
        <x-ui.heading level="h1" size="xl">DICOM Tools</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">C-ECHO</h3>
                    <form method="POST" action="{{ route('utilities.dicom.echo') }}" class="space-y-3">
                        @csrf
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">AE Title</label>
                                <x-ui.input name="ae_title" placeholder="DCM4CHEE" class="text-xs" required />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Host</label>
                                <x-ui.input name="hostname" placeholder="192.168.1.100" class="text-xs" required />
                            </div>
                            <div class="w-20">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Port</label>
                                <x-ui.input name="port" placeholder="11112" class="text-xs" required />
                            </div>
                            <div class="flex items-end">
                                <button class="px-3 py-2 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">Echo</button>
                            </div>
                        </div>
                        <div class="flex gap-2 text-xs text-gray-500">
                            @foreach($devices as $d)
                                <button type="button" onclick="fillEcho('{{ $d->ae_title }}','{{ $d->hostname }}','{{ $d->port }}')" class="text-blue-600 hover:text-blue-800 underline">{{ $d->name }}</button>
                            @endforeach
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Ping</h3>
                    <form method="POST" action="{{ route('utilities.dicom.ping') }}" class="space-y-3">
                        @csrf
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Hostname / IP</label>
                                <x-ui.input name="hostname" placeholder="192.168.1.1" class="text-xs" required />
                            </div>
                            <div class="flex items-end">
                                <button class="px-3 py-2 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">Ping</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">C-FIND (Patient Query)</h3>
                    <form method="POST" action="{{ route('utilities.dicom.find') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Server</label>
                            <select name="server_id" class="w-full rounded-field border border-black/10 bg-white px-2 py-1.5 text-sm">
                                @foreach($servers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Patient Name</label>
                                <x-ui.input name="patient_name" placeholder="Budi*" class="text-xs" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Patient ID</label>
                                <x-ui.input name="patient_id" placeholder="ID" class="text-xs" />
                            </div>
                            <div class="flex items-end">
                                <button class="px-3 py-2 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">Find</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">C-MOVE</h3>
                    <form method="POST" action="{{ route('utilities.dicom.move') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Server</label>
                            <select name="server_id" class="w-full rounded-field border border-black/10 bg-white px-2 py-1.5 text-sm">
                                @foreach($servers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-[2]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Study UID</label>
                                <x-ui.input name="study_uid" placeholder="1.2.840.10008..." class="text-xs font-mono" required />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Destination AE</label>
                                <x-ui.input name="dest_ae" placeholder="DCM4CHEE" class="text-xs" required />
                            </div>
                            <div class="flex items-end">
                                <button class="px-3 py-2 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">Move</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($result))
                <div class="mt-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full {{ $success ? 'bg-green-400' : 'bg-red-400' }}"></div>
                        <span class="text-xs font-medium {{ $success ? 'text-green-700' : 'text-red-700' }}">{{ $success ? 'Success' : 'Failed' }}</span>
                    </div>
                    <pre class="text-xs bg-gray-50 rounded-lg p-3 max-h-96 overflow-auto font-mono">{{ $result }}</pre>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function fillEcho(ae, host, port) {
        document.querySelector('input[name="ae_title"]').value = ae;
        document.querySelector('input[name="hostname"]').value = host;
        document.querySelector('input[name="port"]').value = port;
    }
    </script>
    @endpush
</x-app-layout>
