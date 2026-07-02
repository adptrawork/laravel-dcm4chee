<x-app-layout>
    @section('title', isset($device) ? 'Edit Device' : 'Add Device')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isset($device) ? 'Edit Device' : 'Add Device' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ isset($device) ? route('devices.update', $device) : route('devices.store') }}">
                    @csrf
                    @if(isset($device)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" value="{{ old('name', $device->name ?? '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">AE Title</label>
                            <input type="text" name="ae_title" value="{{ old('ae_title', $device->ae_title ?? '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Modality</label>
                            <input type="text" name="modality" value="{{ old('modality', $device->modality ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hostname</label>
                            <input type="text" name="hostname" value="{{ old('hostname', $device->hostname ?? '') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Port</label>
                            <input type="number" name="port" value="{{ old('port', $device->port ?? 11112) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Server (optional)</label>
                            <select name="server_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="">None</option>
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ old('server_id', $device->server_id ?? '') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">{{ isset($device) ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('devices.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
