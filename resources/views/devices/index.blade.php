<x-app-layout>
    @section('title', 'Devices')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Devices</h2>
            <a href="{{ route('devices.create') }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">+ Add Device</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">AE Title</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Host:Port</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Modality</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 text-xs uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($devices as $device)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $device->name }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $device->ae_title }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $device->hostname }}:{{ $device->port }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $device->modality ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $sc = ['online' => 'bg-green-100 text-green-700', 'offline' => 'bg-red-100 text-red-700', 'unknown' => 'bg-gray-100 text-gray-700'];
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $sc[$device->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($device->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('devices.echo', $device) }}" class="text-xs text-blue-600 hover:text-blue-800">C-ECHO</a>
                                    <a href="{{ route('devices.edit', $device) }}" class="text-xs text-gray-600 hover:text-gray-800">Edit</a>
                                    <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Delete?')" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">No devices configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
