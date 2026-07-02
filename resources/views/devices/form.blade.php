<x-app-layout>
    @section('title', isset($device) ? 'Edit Device' : 'Add Device')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">{{ isset($device) ? 'Edit Device' : 'Add Device' }}</x-ui.heading>
            <a href="{{ route('devices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <form method="POST" action="{{ isset($device) ? route('devices.update', $device) : route('devices.store') }}">
                    @csrf
                    @if(isset($device)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <x-ui.input name="name" value="{{ old('name', $device->name ?? '') }}" required placeholder="CT Scanner 1" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AE Title</label>
                            <x-ui.input name="ae_title" value="{{ old('ae_title', $device->ae_title ?? '') }}" required placeholder="CT_SCANNER_01" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modality</label>
                            <x-ui.input name="modality" value="{{ old('modality', $device->modality ?? '') }}" placeholder="CT" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hostname</label>
                            <x-ui.input name="hostname" value="{{ old('hostname', $device->hostname ?? '') }}" required placeholder="192.168.1.50" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                            <x-ui.input type="number" name="port" value="{{ old('port', $device->port ?? 11112) }}" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Server (optional)</label>
                            <select name="server_id"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">None</option>
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ old('server_id', $device->server_id ?? '') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <x-ui.button type="submit" variant="primary">{{ isset($device) ? 'Update' : 'Create' }}</x-ui.button>
                        <a href="{{ route('devices.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
